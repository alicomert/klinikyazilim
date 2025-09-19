<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentReports extends Component
{
    use WithPagination;

    // Filtreleme özellikleri
    public $search = '';
    public $dateFilter = 'all'; // all, today, week, month, custom
    public $startDate = '';
    public $endDate = '';
    public $paymentMethodFilter = 'all';
    public $userFilter = 'all';
    public $perPage = 15;
    
    // Ödeme yapmayan hastalar için ayarlar
    public $showOverdueOnly = false;
    public $overdueDays = 7; // localStorage'dan gelecek
    public $overdueSettings = [
        'enabled' => true,
        'days' => 7,
        'showWarning' => true
    ];
    
    // Modal ve detay görünümleri
    public $showStatsModal = false;
    public $showOverdueModal = false;
    public $selectedOverduePatients = [];
    public $showDeleteModal = false;
    
    // Dropdown özelliği için
    public $expandedPayments = [];
    
    // Ödeme ekleme modalı
    public $showPaymentsModal = false;
    public $selectedPatientForPayments = null;
    public $patientPayments = [];
    public $newPayment = [
        'payment_method' => 'nakit',
        'paid_amount' => '',
        'notes' => ''
    ];// Ödeme silme modalı
    public $paymentToDelete = null;
    
    // Borç düzenleme
    public $showDebtModal = false;
    public $selectedPatientForDebt = null;
    public $newDebtAmount = '';
    
    // İstatistikler
    public $totalPayments = 0;
    public $totalAmount = 0;
    public $averagePayment = 0;
    public $paymentsByMethod = [];
    public $dailyStats = [];
    public $weeklyStats = [];
    public $monthlyStats = [];

    protected $paginationTheme = 'tailwind';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'dateFilter' => ['except' => 'all'],
        'paymentMethodFilter' => ['except' => 'all'],
        'userFilter' => ['except' => 'all'],
        'showOverdueOnly' => ['except' => false]
    ];

    public function mount()
    {
        $this->loadOverdueSettings();
        $this->calculateStats();
    }

    public function render()
    {
        $payments = $this->getPayments();
        $overduePatients = $this->getOverduePatients();
        $users = User::whereIn('role', ['admin', 'doctor', 'nurse', 'secretary'])->get();
        
        return view('livewire.payment-reports', [
            'payments' => $payments,
            'overduePatients' => $overduePatients,
            'users' => $users
        ]);
    }

    public function getPayments()
    {
        // Sadece bekleyen ödeme verilerini al (needs_paid > 0 olan hastalar)
        $unpaidPatients = Patient::where('needs_paid', '>', 0)
            ->when(auth()->user()->role !== 'admin', function($q) {
                $q->where('doctor_id', auth()->id());
            })
            ->when($this->search, function($q) {
                $q->where(function($subQ) {
                    $subQ->where('first_name', 'like', '%' . $this->search . '%')
                         ->orWhere('last_name', 'like', '%' . $this->search . '%')
                         ->orWhere('phone', 'like', '%' . $this->search . '%')
                         ->orWhere('tc_identity', 'like', '%' . $this->search . '%');
                });
            })
            ->get();
            
        $payments = collect();
            
        // Bekleyen ödeme verilerini fake payment objesi olarak ekle
        foreach ($unpaidPatients as $patient) {
            // Patient objesine name property'si ekle
            $patient->name = trim($patient->first_name . ' ' . $patient->last_name);
            
            // Hastanın tüm ödemelerini kontrol et
            $totalPaid = Payment::where('patient_id', $patient->id)->sum('paid_amount');
            $remainingDebt = max(0, $patient->needs_paid - $totalPaid);
            
            // Eğer borç tamamen ödenmişse
            if ($remainingDebt <= 0) {
                $fakePayment = (object) [
                    'id' => 'completed_' . $patient->id,
                    'patient' => $patient,
                    'user' => (object) ['name' => 'Sistem'],
                    'user_id' => null,
                    'payment_method' => 'Ödeme Tamamlandı',
                    'paid_amount' => 0,
                    'created_at' => $patient->updated_at,
                    'notes' => 'Tüm ödemeler tamamlandı',
                    'is_completed_payment' => true
                ];
            } else {
                // Bekleyen ödeme varsa
                $fakePayment = (object) [
                    'id' => 'pending_' . $patient->id,
                    'patient' => $patient,
                    'user' => (object) ['name' => 'Sistem'],
                    'user_id' => null,
                    'payment_method' => 'Bekleyen Ödeme',
                    'paid_amount' => $remainingDebt,
                    'created_at' => $patient->updated_at,
                    'notes' => 'Kalan Borç: ' . $this->formatCurrency($remainingDebt),
                    'is_pending_payment' => true
                ];
            }
            $payments->push($fakePayment);
        }
        
        // Tarihe göre sırala ve paginate et
        $payments = $payments->sortByDesc('created_at');
        
        // Manuel pagination
        $currentPage = request()->get('page', 1);
        $perPage = $this->perPage;
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $payments->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $payments->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    public function getOverduePatients()
    {
        if (!$this->overdueSettings['enabled']) {
            return collect();
        }
        
        $overdueDate = now()->subDays($this->overdueSettings['days']);
        
        // Tüm borçlu hastaları al
        $query = Patient::with(['payments' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->where('needs_paid', '>', 0);
            
        // Kullanıcı rolüne göre filtreleme
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $patients = $query->get();
        
        // Her hasta için ödeme durumunu kontrol et
        $overduePatients = $patients->filter(function($patient) use ($overdueDate) {
            // Hastanın toplam ödenen miktarını hesapla
            $totalPaid = $patient->payments->sum('paid_amount');
            $remainingDebt = max(0, $patient->needs_paid - $totalPaid);
            
            // Eğer borç tamamen ödenmişse, ödeme yapmayan listesine ekleme
            if ($remainingDebt <= 0) {
                return false;
            }
            
            // En son ödeme tarihini bul
            $lastPayment = $patient->payments->first(); // payments zaten created_at desc sıralı
            
            if ($lastPayment) {
                // En son ödeme tarihinden 7 gün geçmişse ödeme yapmayan
                return $lastPayment->created_at <= $overdueDate;
            } else {
                // Hiç ödeme yoksa, hasta güncellenme tarihinden 7 gün geçmişse ödeme yapmayan
                return $patient->updated_at <= $overdueDate;
            }
        });
        
        return $overduePatients;
    }

    public function calculateStats()
    {
        $query = Payment::query();
        
        // Kullanıcı rolüne göre filtreleme
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        // Tarih filtresi uygula
        $this->applyDateFilterToQuery($query);
        
        $payments = $query->get();
        
        $this->totalPayments = $payments->count();
        $this->totalAmount = $payments->sum('paid_amount');
        $this->averagePayment = $this->totalPayments > 0 ? $this->totalAmount / $this->totalPayments : 0;
        
        // Ödeme yöntemlerine göre grupla
        $this->paymentsByMethod = $payments->groupBy('payment_method')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('paid_amount'),
                    'percentage' => $this->totalAmount > 0 ? ($group->sum('paid_amount') / $this->totalAmount) * 100 : 0
                ];
            })->toArray();
            
        // Günlük istatistikler (son 7 gün)
        $this->dailyStats = $this->getDailyStats();
        
        // Haftalık istatistikler (son 4 hafta)
        $this->weeklyStats = $this->getWeeklyStats();
        
        // Aylık istatistikler (son 6 ay)
        $this->monthlyStats = $this->getMonthlyStats();
    }
    
    private function applyDateFilterToQuery($query)
    {
        switch($this->dateFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'custom':
                if($this->startDate && $this->endDate) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
                }
                break;
        }
    }

    private function getDailyStats()
    {
        $stats = [];
        for($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayPayments = Payment::whereDate('created_at', $date)
                ->when(auth()->user()->role !== 'admin', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->get();
                
            $stats[] = [
                'date' => $date->format('d.m'),
                'day' => $date->format('l'),
                'count' => $dayPayments->count(),
                'total' => $dayPayments->sum('paid_amount')
            ];
        }
        return $stats;
    }

    private function getWeeklyStats()
    {
        $stats = [];
        for($i = 3; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();
            
            $weekPayments = Payment::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->when(auth()->user()->role !== 'admin', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->get();
                
            $stats[] = [
                'week' => $startOfWeek->format('d.m') . ' - ' . $endOfWeek->format('d.m'),
                'count' => $weekPayments->count(),
                'total' => $weekPayments->sum('paid_amount')
            ];
        }
        return $stats;
    }

    private function getMonthlyStats()
    {
        $stats = [];
        for($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            $monthPayments = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->when(auth()->user()->role !== 'admin', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->get();
                
            $stats[] = [
                'month' => $month->format('F Y'),
                'month_tr' => $this->getMonthNameTurkish($month->format('F')) . ' ' . $month->format('Y'),
                'count' => $monthPayments->count(),
                'total' => $monthPayments->sum('paid_amount')
            ];
        }
        return $stats;
    }
    
    private function getMonthNameTurkish($englishMonth)
    {
        $months = [
            'January' => 'Ocak', 'February' => 'Şubat', 'March' => 'Mart',
            'April' => 'Nisan', 'May' => 'Mayıs', 'June' => 'Haziran',
            'July' => 'Temmuz', 'August' => 'Ağustos', 'September' => 'Eylül',
            'October' => 'Ekim', 'November' => 'Kasım', 'December' => 'Aralık'
        ];
        return $months[$englishMonth] ?? $englishMonth;
    }

    // Filtreleme metodları
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedStartDate()
    {
        if($this->dateFilter === 'custom') {
            $this->resetPage();
            $this->calculateStats();
        }
    }

    public function updatedEndDate()
    {
        if($this->dateFilter === 'custom') {
            $this->resetPage();
            $this->calculateStats();
        }
    }

    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
    }

    public function updatedUserFilter()
    {
        $this->resetPage();
    }

    public function updatedShowOverdueOnly()
    {
        $this->resetPage();
    }

    // Ödeme yapmayan hastalar ayarları
    public function updateOverdueSettings()
    {
        $this->overdueSettings['days'] = max(1, (int)$this->overdueDays);
        $this->saveOverdueSettings();
        $this->calculateStats();
        
        session()->flash('message', 'Ödeme uyarı ayarları güncellendi.');
    }

    public function loadOverdueSettings()
    {
        // JavaScript'ten localStorage değerlerini alacağız
        $this->overdueSettings = [
            'enabled' => true,
            'days' => 7,
            'showWarning' => true
        ];
        $this->overdueDays = $this->overdueSettings['days'];
    }

    public function saveOverdueSettings()
    {
        // JavaScript ile localStorage'a kaydedeceğiz
        $this->dispatch('save-overdue-settings', $this->overdueSettings);
    }

    // Modal metodları
    public function showStats()
    {
        $this->calculateStats();
        $this->showStatsModal = true;
    }

    public function closeStatsModal()
    {
        $this->showStatsModal = false;
    }

    public function showOverduePatients()
    {
        $this->selectedOverduePatients = $this->getOverduePatients();
        $this->showOverdueModal = true;
    }

    public function closeOverdueModal()
    {
        $this->showOverdueModal = false;
        $this->selectedOverduePatients = [];
    }

    // Export metodları
    public function exportPayments()
    {
        // CSV export işlemi
        $payments = $this->getPayments();
        
        $filename = 'odeme_raporu_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM ekle
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Başlıklar
            fputcsv($file, [
                'Hasta Adı',
                'Telefon',
                'Ödeme Yöntemi', 
                'Tutar',
                'Tarih',
                'Kullanıcı',
                'Notlar'
            ], ';');
            
            // Veriler
            foreach($payments as $payment) {
                fputcsv($file, [
                    $payment->patient->name ?? '',
                    $payment->patient->phone ?? '',
                    $payment->payment_method,
                    $payment->paid_amount,
                    $payment->created_at->format('d.m.Y H:i'),
                    $payment->user->name ?? '',
                    $payment->notes
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // Yardımcı metodlar
    public function resetFilters()
    {
        $this->search = '';
        $this->dateFilter = 'all';
        $this->startDate = '';
        $this->endDate = '';
        $this->paymentMethodFilter = 'all';
        $this->userFilter = 'all';
        $this->showOverdueOnly = false;
        $this->resetPage();
        $this->calculateStats();
    }

    public function getPaymentMethodDisplayName($method)
    {
        $methods = [
            'nakit' => 'Nakit',
            'kredi_karti' => 'Kredi Kartı',
            'banka_havalesi' => 'Banka Havalesi',
            'pos' => 'POS',
            'cek' => 'Çek'
        ];
        return $methods[$method] ?? ucfirst($method);
    }

    public function formatCurrency($amount)
    {
        return number_format($amount, 2, ',', '.') . ' ₺';
    }
    
    // Ödeme modalı fonksiyonları
    public function openPaymentsModal($patientId)
    {
        $this->selectedPatientForPayments = Patient::find($patientId);
        if ($this->selectedPatientForPayments) {
            $this->loadPatientPayments();
            $this->showPaymentsModal = true;
            $this->resetNewPayment();
        }
    }
    
    public function closePaymentsModal()
    {
        $this->showPaymentsModal = false;
        $this->selectedPatientForPayments = null;
        $this->patientPayments = [];
        $this->resetNewPayment();
    }
    
    public function loadPatientPayments()
    {
        if ($this->selectedPatientForPayments) {
            $this->patientPayments = Payment::with('user')
                ->where('patient_id', $this->selectedPatientForPayments->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_method' => $payment->payment_method,
                        'paid_amount' => $payment->paid_amount,
                        'notes' => $payment->notes,
                        'created_at' => $payment->created_at,
                        'user_name' => $payment->user->name ?? 'Bilinmeyen',
                        'can_delete' => $this->canDeletePayment($payment)
                    ];
                })->toArray();
        }
    }
    
    public function addPayment()
    {
        $this->validate([
            'newPayment.paid_amount' => 'required|numeric|min:0.01',
            'newPayment.payment_method' => 'required|in:nakit,kredi_karti,banka_havalesi,pos,diger',
            'newPayment.notes' => 'nullable|string|max:255'
        ], [
            'newPayment.paid_amount.required' => 'Ödeme tutarı gereklidir.',
            'newPayment.paid_amount.numeric' => 'Ödeme tutarı sayısal olmalıdır.',
            'newPayment.paid_amount.min' => 'Ödeme tutarı 0.01 TL\'den az olamaz.',
            'newPayment.payment_method.required' => 'Ödeme yöntemi seçilmelidir.',
            'newPayment.notes.max' => 'Not 255 karakterden uzun olamaz.'
        ]);
        
        try {
            Payment::create([
                'patient_id' => $this->selectedPatientForPayments->id,
                'user_id' => auth()->id(),
                'payment_method' => $this->newPayment['payment_method'],
                'paid_amount' => $this->newPayment['paid_amount'],
                'notes' => $this->newPayment['notes'] ?? null
            ]);
            
            // Hasta bilgilerini yenile
            if ($this->selectedPatientForPayments) {
                $this->selectedPatientForPayments->refresh();
                $this->loadPatientPayments();
            }
            $this->resetNewPayment();
            $this->calculateStats();
            
            session()->flash('message', 'Ödeme başarıyla eklendi.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ödeme eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function confirmDeletePayment()
    {
        if ($this->paymentToDelete) {
            try {
                $payment = Payment::find($this->paymentToDelete);
                if ($payment && $this->canDeletePayment($payment)) {
                    $payment->delete();
                    
                    if ($this->selectedPatientForPayments) {
                        $this->selectedPatientForPayments->refresh();
                        $this->loadPatientPayments();
                    }
                    $this->calculateStats();
                    
                    session()->flash('message', 'Ödeme başarıyla silindi.');
                } else {
                    session()->flash('error', 'Bu ödemeyi silme yetkiniz yok.');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Ödeme silinirken bir hata oluştu: ' . $e->getMessage());
            }
        }
        
        $this->paymentToDelete = null;
    }
    
    public function cancelDeletePayment()
    {
        $this->paymentToDelete = null;
    }
    
    public function canDeletePayment($payment)
    {
        // Admin her ödemeyi silebilir
        if (auth()->user()->role === 'admin') {
            return true;
        }
        
        // Kendi eklediği ödemeleri silebilir (24 saat içinde)
        if ($payment->user_id === auth()->id()) {
            return $payment->created_at->diffInHours(now()) <= 24;
        }
        
        return false;
    }
    
    public function resetNewPayment()
    {
        $this->newPayment = [
            'payment_method' => 'nakit',
            'paid_amount' => '',
            'notes' => ''
        ];
    }
    
    // Borç düzenleme fonksiyonları
    public function openDebtModal($patientId)
    {
        $this->selectedPatientForDebt = Patient::find($patientId);
        if ($this->selectedPatientForDebt) {
            $this->newDebtAmount = $this->selectedPatientForDebt->needs_paid;
            $this->showDebtModal = true;
        }
    }
    
    public function closeDebtModal()
    {
        $this->showDebtModal = false;
        $this->selectedPatientForDebt = null;
        $this->newDebtAmount = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->paymentToDelete = null;
    }
    
    public function updateDebt()
    {
        $this->validate([
            'newDebtAmount' => 'required|numeric|min:0'
        ], [
            'newDebtAmount.required' => 'Borç tutarı gereklidir.',
            'newDebtAmount.numeric' => 'Borç tutarı sayısal olmalıdır.',
            'newDebtAmount.min' => 'Borç tutarı negatif olamaz.'
        ]);
        
        try {
            $this->selectedPatientForDebt->update([
                'needs_paid' => $this->newDebtAmount
            ]);
            
            $this->calculateStats();
            $this->closeDebtModal();
            
            session()->flash('message', 'Borç tutarı başarıyla güncellendi.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Borç güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    // Computed properties
    public function getTotalPaidProperty()
    {
        if (!$this->selectedPatientForPayments) {
            return 0;
        }
        
        return Payment::where('patient_id', $this->selectedPatientForPayments->id)->sum('paid_amount');
    }
    
    public function getRemainingAmountProperty()
    {
        if (!$this->selectedPatientForPayments) {
            return 0;
        }
        
        // Kalan borç = Toplam borç - Yapılan ödemeler
        $totalPaid = Payment::where('patient_id', $this->selectedPatientForPayments->id)->sum('paid_amount');
        return max(0, $this->selectedPatientForPayments->needs_paid - $totalPaid);
    }
    
    public function calculateAge($birthDate)
    {
        if (!$birthDate) {
            return 'Bilinmiyor';
        }
        
        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Exception $e) {
            return 'Bilinmiyor';
        }
    }
    
    // Dropdown özelliği için metodlar
    public function togglePaymentExpansion($paymentId)
    {
        if (in_array($paymentId, $this->expandedPayments)) {
            $this->expandedPayments = array_diff($this->expandedPayments, [$paymentId]);
        } else {
            $this->expandedPayments[] = $paymentId;
        }
    }
    
    public function isPaymentExpanded($paymentId)
    {
        return in_array($paymentId, $this->expandedPayments);
    }
    
    public function getPatientPaymentHistory($patientId)
    {
        return Payment::with('user')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    // Hızlı ödeme metodları
    public function quickPayment($patientId, $paymentMethod)
    {
        $patient = Patient::findOrFail($patientId);
        
        // Kalan borç hesapla
        $totalPaid = Payment::where('patient_id', $patientId)->sum('paid_amount');
        $remainingDebt = max(0, $patient->needs_paid - $totalPaid);
        
        if ($remainingDebt <= 0) {
            session()->flash('error', 'Bu hastanın borcu bulunmuyor.');
            return;
        }
        
        try {
            // Ödeme yöntemi 'diger' ise kalan borç kadar ödeme ekle
            if ($paymentMethod === 'diger') {
                Payment::create([
                    'patient_id' => $patientId,
                    'user_id' => auth()->id(),
                    'payment_method' => 'diger',
                    'paid_amount' => $remainingDebt,
                    'notes' => 'Hızlı ödeme - Kalan borç kapatıldı'
                ]);
                
                session()->flash('message', 'Kalan borç tamamen kapatıldı.');
            } else {
                // Diğer ödeme yöntemleri için modal aç
                $this->selectedPatientForPayments = $patient;
                $this->newPayment['payment_method'] = $paymentMethod;
                $this->newPayment['paid_amount'] = $remainingDebt;
                $this->newPayment['notes'] = 'Hızlı ödeme';
                $this->loadPatientPayments();
                $this->showPaymentsModal = true;
            }
            
            $this->calculateStats();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ödeme eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function closeDropdown($paymentId)
    {
        $this->expandedPayments = array_diff($this->expandedPayments, [$paymentId]);
    }
}