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

Route::middleware(['auth'])->group(function () {
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
