<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'role.redirect'])->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'role.redirect'])
    ->name('dashboard');

Route::get('/settings', function () {
    return view('settings');
})->middleware('auth')->name('settings');

Route::get('/patients', function () {
    return view('patients');
})->middleware('auth')->name('patients');

Route::get('/operations', function () {
    return view('operations');
})->middleware('auth')->name('operations');

Route::get('/reports', function () {
    return view('reports');
})->middleware('auth')->name('reports');

Route::get('/messages', function () {
    return view('messages');
})->middleware('auth')->name('messages');

Route::get('/doctor-panel', function () {
    return view('doctor-panel');
})->middleware(['auth', 'role.redirect'])->name('doctor-panel');

Route::get('/clinic', function () {
    return view('clinic');
})->middleware('auth')->name('clinic');

Route::get('/payment-reports', function () {
    return view('payment-reports');
})->middleware('auth')->name('payment-reports');

// API Routes
// API endpoint for patient statistics
Route::get('/api/patient-stats', function () {
    $user = auth()->user();
    $period = request('period', 'monthly'); // monthly, quarterly, yearly
    
    // Base query with doctor_id filtering based on user role
    $query = \App\Models\Patient::query();
    
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        $query->where('doctor_id', $user->doctor_id);
    }
    // Admin sees all patients (no additional filtering)
    
    // Total patients
    $totalPatients = $query->count();
    
    // This month new patients
    $thisMonthNew = (clone $query)->whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
    
    // Last month new patients
    $lastMonthNew = (clone $query)->whereMonth('created_at', now()->subMonth()->month)
                                   ->whereYear('created_at', now()->subMonth()->year)
                                   ->count();
    
    // Calculate percentage change
    $newPatientsChange = $lastMonthNew > 0 
        ? round((($thisMonthNew - $lastMonthNew) / $lastMonthNew) * 100, 1)
        : ($thisMonthNew > 0 ? 100 : 0);
    
    // Age distribution
    $ageDistribution = (clone $query)->selectRaw('
        CASE 
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 25 THEN "18-25"
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 26 AND 35 THEN "26-35"
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 45 THEN "36-45"
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 46 AND 55 THEN "46-55"
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 55 THEN "55+"
            ELSE "Diğer"
        END as age_group,
        COUNT(*) as count
    ')
    ->whereNotNull('birth_date')
    ->where('birth_date', '!=', '0000-00-00')
    ->groupBy('age_group')
    ->pluck('count', 'age_group')
    ->toArray();
    
    // Registration trend based on period
    $trendData = [];
    
    if ($period === 'monthly') {
        // Last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = (clone $query)->whereMonth('created_at', $date->month)
                                   ->whereYear('created_at', $date->year)
                                   ->count();
            $trendData[] = [
                'label' => $date->format('M Y'),
                'count' => $count
            ];
        }
    } elseif ($period === 'quarterly') {
        // Last 8 quarters (2 years)
        for ($i = 7; $i >= 0; $i--) {
            $startDate = now()->subMonths($i * 3)->startOfQuarter();
            $endDate = now()->subMonths($i * 3)->endOfQuarter();
            $count = (clone $query)->whereBetween('created_at', [$startDate, $endDate])
                                   ->count();
            $quarter = ceil($startDate->month / 3);
            $trendData[] = [
                'label' => 'Q' . $quarter . ' ' . $startDate->year,
                'count' => $count
            ];
        }
    } elseif ($period === 'yearly') {
        // Last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $count = (clone $query)->whereYear('created_at', $year)
                                   ->count();
            $trendData[] = [
                'label' => (string)$year,
                'count' => $count
            ];
        }
    }
    
    return response()->json([
        'total_patients' => $totalPatients,
        'new_patients_this_month' => $thisMonthNew,
        'new_patients_change' => ($newPatientsChange >= 0 ? '+' : '') . $newPatientsChange . '%',
        'age_distribution' => [
            'labels' => array_keys($ageDistribution),
            'data' => array_values($ageDistribution)
        ],
        'monthly_trend' => [
            'labels' => array_column($trendData, 'label'),
            'data' => array_column($trendData, 'count')
        ]
    ]);
})->middleware('auth');

// API endpoint for operation statistics
Route::get('/api/operation-stats', function () {
    $user = auth()->user();
    $period = request('period', 'monthly');
    $customMonth = request('month');
    
    // Base query with doctor_id filtering based on user role
    $query = \App\Models\Operation::query();
    
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        $query->where('doctor_id', $user->doctor_id);
    }
    // Admin sees all operations (no additional filtering)
    
    // Apply period filtering for operation types
    $typeQuery = clone $query;
    if ($period === 'monthly') {
        $typeQuery->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
    } elseif ($period === 'yearly') {
        $typeQuery->whereYear('created_at', now()->year);
    } elseif ($period === 'custom' && $customMonth) {
        $date = \Carbon\Carbon::parse($customMonth);
        $typeQuery->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
    }
    
    // Total operations for current period
    $totalOperations = $typeQuery->count();
    
    // Previous period for comparison
    $prevQuery = clone $query;
    if ($period === 'monthly') {
        $prevQuery->whereMonth('created_at', now()->subMonth()->month)
                  ->whereYear('created_at', now()->subMonth()->year);
    } elseif ($period === 'yearly') {
        $prevQuery->whereYear('created_at', now()->subYear()->year);
    } elseif ($period === 'custom' && $customMonth) {
        $date = \Carbon\Carbon::parse($customMonth)->subMonth();
        $prevQuery->whereMonth('created_at', $date->month)
                  ->whereYear('created_at', $date->year);
    }
    
    $prevTotalOperations = $prevQuery->count();
    
    // Calculate percentage change
    $totalChange = $prevTotalOperations > 0 
        ? round((($totalOperations - $prevTotalOperations) / $prevTotalOperations) * 100, 1)
        : ($totalOperations > 0 ? 100 : 0);
    
    // Operation type distribution
    $operationTypes = $typeQuery->selectRaw('process, COUNT(*) as count')
                                ->groupBy('process')
                                ->pluck('count', 'process')
                                ->toArray();
    
    // Turkish translations for operation types
    $typeTranslations = [
        'surgery' => 'Ameliyat',
        'mesotherapy' => 'Mezoterapi',
        'botox' => 'Botoks',
        'filler' => 'Dolgu'
    ];
    
    // Find most and least performed operations
    $mostOperation = $operationTypes ? array_keys($operationTypes, max($operationTypes))[0] : null;
    $leastOperation = $operationTypes ? array_keys($operationTypes, min($operationTypes))[0] : null;
    
    // Previous period operation types for comparison
    $prevOperationTypes = $prevQuery->selectRaw('process, COUNT(*) as count')
                                    ->groupBy('process')
                                    ->pluck('count', 'process')
                                    ->toArray();
    
    // Calculate changes for most/least operations
    $mostOperationChange = 0;
    $leastOperationChange = 0;
    
    if ($mostOperation) {
        $currentMost = $operationTypes[$mostOperation] ?? 0;
        $prevMost = $prevOperationTypes[$mostOperation] ?? 0;
        $mostOperationChange = $prevMost > 0 
            ? round((($currentMost - $prevMost) / $prevMost) * 100, 1)
            : ($currentMost > 0 ? 100 : 0);
    }
    
    if ($leastOperation) {
        $currentLeast = $operationTypes[$leastOperation] ?? 0;
        $prevLeast = $prevOperationTypes[$leastOperation] ?? 0;
        $leastOperationChange = $prevLeast > 0 
            ? round((($currentLeast - $prevLeast) / $prevLeast) * 100, 1)
            : ($currentLeast > 0 ? 100 : 0);
    }
    
    // Monthly trend (last 6 months)
    $monthlyTrend = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $count = (clone $query)->whereMonth('created_at', $date->month)
                               ->whereYear('created_at', $date->year)
                               ->count();
        $monthlyTrend[] = [
            'month' => $date->format('M Y'),
            'count' => $count
        ];
    }
    
    // Prepare operation types for chart
    $chartLabels = [];
    $chartData = [];
    foreach ($operationTypes as $type => $count) {
        $chartLabels[] = $typeTranslations[$type] ?? ucfirst($type);
        $chartData[] = $count;
    }
    
    return response()->json([
        'total_operations' => $totalOperations,
        'total_operations_change' => ($totalChange >= 0 ? '+' : '') . $totalChange . '%',
        'most_operation_type' => $mostOperation ? ($typeTranslations[$mostOperation] ?? ucfirst($mostOperation)) : '-',
        'most_operation_count' => $mostOperation ? $operationTypes[$mostOperation] : 0,
        'most_operation_change' => ($mostOperationChange >= 0 ? '+' : '') . $mostOperationChange . '%',
        'least_operation_type' => $leastOperation ? ($typeTranslations[$leastOperation] ?? ucfirst($leastOperation)) : '-',
        'least_operation_count' => $leastOperation ? $operationTypes[$leastOperation] : 0,
        'least_operation_change' => ($leastOperationChange >= 0 ? '+' : '') . $leastOperationChange . '%',
        'operation_types' => [
            'labels' => $chartLabels,
            'data' => $chartData
        ],
        'monthly_trend' => [
            'labels' => array_column($monthlyTrend, 'month'),
            'data' => array_column($monthlyTrend, 'count')
        ]
    ]);
})->middleware('auth');

// API endpoint for process type statistics
Route::get('/api/process-type-stats', function () {
    $user = auth()->user();
    $period = request('period', 'monthly');
    
    // Base query with doctor_id filtering based on user role
    $query = \App\Models\Operation::with('operationType');
    
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        // Secretary ve nurse tüm verileri görebilir
    }
    
    // Period filtering
    if ($period === 'monthly') {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    } elseif ($period === 'yearly') {
        $query->whereYear('created_at', now()->year);
    }
    // 'all' için filtreleme yok
    
    $operations = $query->get();
    
    // Process type istatistikleri
    $processTypeCounts = [];
    $totalProcessTypes = 0;
    
    foreach ($operations as $operation) {
        $processTypeName = $operation->operationType ? $operation->operationType->name : 'Belirtilmemiş';
        $processTypeCounts[$processTypeName] = ($processTypeCounts[$processTypeName] ?? 0) + 1;
        $totalProcessTypes++;
    }
    
    // En çok yapılan process type
    $mostProcessType = null;
    $mostProcessTypeCount = 0;
    
    if (!empty($processTypeCounts)) {
        arsort($processTypeCounts);
        $mostProcessType = array_key_first($processTypeCounts);
        $mostProcessTypeCount = $processTypeCounts[$mostProcessType];
    }
    
    return response()->json([
        'most_process_type' => $mostProcessType ?: 'Veri yok',
        'most_process_type_count' => $mostProcessTypeCount,
        'total_process_types' => count($processTypeCounts),
        'process_types' => [
            'labels' => array_keys($processTypeCounts),
            'data' => array_values($processTypeCounts)
        ]
    ]);
})->middleware('auth');

// API endpoint for operations detail table
Route::get('/api/operations-detail', function () {
    $user = auth()->user();
    $period = request('period', 'monthly');
    $page = request('page', 1);
    $perPage = request('per_page', 10);
    
    // Base query with doctor_id filtering based on user role
    $query = \App\Models\Operation::with('operationType');
    
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        // Secretary ve nurse tüm verileri görebilir
    }
    
    // Period filtering for current data
    $currentQuery = clone $query;
    $previousQuery = clone $query;
    
    if ($period === 'monthly') {
        $currentQuery->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
        $previousQuery->whereMonth('created_at', now()->subMonth()->month)
                      ->whereYear('created_at', now()->subMonth()->year);
    } elseif ($period === 'yearly') {
        $currentQuery->whereYear('created_at', now()->year);
        $previousQuery->whereYear('created_at', now()->subYear()->year);
    }
    
    // Operasyon türlerine göre grupla
    $currentOperations = $currentQuery->get()->groupBy(function($operation) {
        return $operation->process . '|' . ($operation->operationType ? $operation->operationType->name : 'Belirtilmemiş');
    });
    
    $previousOperations = $previousQuery->get()->groupBy(function($operation) {
        return $operation->process . '|' . ($operation->operationType ? $operation->operationType->name : 'Belirtilmemiş');
    });
    
    $operationsData = [];
    
    foreach ($currentOperations as $key => $operations) {
        [$process, $processType] = explode('|', $key);
        $currentCount = $operations->count();
        $previousCount = $previousOperations->get($key, collect())->count();
        
        // Değişim yüzdesi hesapla
        $change = 0;
        if ($previousCount > 0) {
            $change = round((($currentCount - $previousCount) / $previousCount) * 100, 1);
        } elseif ($currentCount > 0) {
            $change = 100;
        }
        
        // Process türü çevirisi
        $processTranslations = [
            'surgery' => 'Ameliyat',
            'mesotherapy' => 'Mezoterapi',
            'botox' => 'Botoks',
            'filler' => 'Dolgu'
        ];
        
        $operationsData[] = [
            'operation_name' => $processTranslations[$process] ?? ucfirst($process),
            'process_type' => $processType,
            'total_count' => $currentCount + $previousCount,
            'current_month' => $currentCount,
            'previous_month' => $previousCount,
            'change' => $change
        ];
    }
    
    // Toplam sayıya göre sırala
    usort($operationsData, function($a, $b) {
        return $b['total_count'] - $a['total_count'];
    });
    
    // Sayfalama
    $total = count($operationsData);
    $offset = ($page - 1) * $perPage;
    $paginatedData = array_slice($operationsData, $offset, $perPage);
    
    return response()->json([
        'operations' => $paginatedData,
        'pagination' => [
            'current_page' => (int)$page,
            'last_page' => ceil($total / $perPage),
            'per_page' => (int)$perPage,
            'total' => $total,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]
    ]);
})->middleware('auth');

// API endpoint for exporting operations detail
Route::get('/api/operations-detail/export', function () {
    $user = auth()->user();
    $period = request('period', 'monthly');
    
    // Base query with doctor_id filtering based on user role
    $query = \App\Models\Operation::with('operationType');
    
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        // Secretary ve nurse tüm verileri görebilir
    }
    
    // Period filtering
    if ($period === 'monthly') {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    } elseif ($period === 'yearly') {
        $query->whereYear('created_at', now()->year);
    }
    
    $operations = $query->get();
    
    // CSV başlıkları
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="islem-detaylari-' . date('Y-m-d') . '.csv"',
    ];
    
    $callback = function() use ($operations) {
        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle (Excel için)
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Başlıkları yaz
        fputcsv($file, ['İşlem Adı', 'İşlem Türü', 'Tarih', 'Hasta', 'Doktor'], ';');
        
        // Process türü çevirisi
        $processTranslations = [
            'surgery' => 'Ameliyat',
            'mesotherapy' => 'Mezoterapi',
            'botox' => 'Botoks',
            'filler' => 'Dolgu'
        ];
        
        foreach ($operations as $operation) {
            fputcsv($file, [
                $processTranslations[$operation->process] ?? ucfirst($operation->process),
                $operation->operationType ? $operation->operationType->name : 'Belirtilmemiş',
                $operation->created_at->format('d.m.Y H:i'),
                $operation->patient ? $operation->patient->name : 'Bilinmiyor',
                $operation->doctor ? $operation->doctor->name : 'Bilinmiyor'
            ], ';');
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
})->middleware('auth');

// PDF Export endpoint for operations detail
Route::get('/api/operations-detail/pdf', function () {
    $user = auth()->user();
    $period = request('period', 'monthly');
    
    // Base query with relationships
    $query = \App\Models\Operation::with(['operationType', 'patient', 'doctor']);
    
    // Role-based filtering
    if ($user->role === 'doctor') {
        $query->where('doctor_id', $user->id);
    } elseif (in_array($user->role, ['secretary', 'nurse'])) {
        // Secretary ve nurse tüm verileri görebilir
    }
    
    // Period filtering
    if ($period === 'monthly') {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    } elseif ($period === 'yearly') {
        $query->whereYear('created_at', now()->year);
    }
    // 'all' için filtreleme yok
    
    $operations = $query->orderBy('created_at', 'desc')->get();
    
    // PDF oluştur
    $pdf = app('dompdf.wrapper');
    $pdf->loadView('pdf.operations-report', [
        'operations' => $operations,
        'period' => $period,
        'clinic_name' => config('app.name', 'Klinik Yönetim Sistemi')
    ]);
    
    // PDF ayarları
    $pdf->setPaper('A4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans'
    ]);
    
    // Dosya adı oluştur
    $filename = 'islem-detay-raporu-' . now()->format('Y-m-d-H-i') . '.pdf';
    
    return $pdf->download($filename);
})->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
