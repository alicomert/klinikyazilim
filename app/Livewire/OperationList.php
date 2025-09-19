<?php

namespace App\Livewire;

use App\Models\Operation;
use App\Models\OperationNote;
use App\Models\Patient;
use App\Models\Activity;
use App\Models\OperationType;
use App\Models\OperationDetail;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OperationList extends Component
{
    use WithPagination;

    // Event listeners
    protected $listeners = ['operation-type-added' => 'refreshOperationTypes'];

    // Public properties
    public $operations = [];
    public $patients = [];
    public $newOperation = [];
    public $editingOperation = null;
    public $showModal = false;
    public $searchTerm = '';
    public $filterProcess = '';
    public $filterRegistrationPeriod = '';
    public $showNotesModal = false;
    public $selectedOperationForNotes = null;
    public $operationNotes = [];
    public $newNote = [
        'content' => '',
        'note_type' => 'general',
        'is_private' => false
    ];
    public $editingNote = null;
    public $currentRegistrationPeriod;
    public $patientSearch = '';
    public $filteredPatients = [];
    public $selectedPatient = null;
    public $showPatientDetails = false;
    
    // Yeni istatistik sistemi için
    public $statsPeriod = 'monthly'; // monthly, yearly, this_year, all

    // Dinamik dropdown için yeni property'ler
    public $operationTypes = [];
    public $operationDetails = [];
    public $selectedOperationType = null;
    public $operationTypeSearch = '';
    public $operationDetailSearch = '';
    public $showOperationTypeDropdown = false;
    public $showOperationDetailDropdown = false;
    public $showAddOperationTypeModal = false;
    public $showAddOperationDetailModal = false;
    public $editingOperationDetail = null;
    public $editingOperationType = null;
    
    // Yeni İşlem Tipi Ekle sistemi için ayrı property'ler
    public $operationTypeForm = ['name' => '', 'value' => ''];
    
    public $newOperationDetail = ['name' => '', 'description' => ''];
    
    // Process dropdown için property'ler
    public $processOptions = [
        'surgery' => 'Ameliyat',
        'mesotherapy' => 'Mezoterapi',
        'botox' => 'Botoks',
        'filler' => 'Dolgu'
    ];
    public $processSearch = '';
    public $showProcessDropdown = false;
    public $showAddProcessModal = false;
    public $newProcess = ['name' => '', 'value' => '', 'description' => ''];

    // Validation rules
    protected $rules = [
        'newOperation.patient_id' => 'required|exists:patients,id',
        'newOperation.process' => 'required|string',
        'selectedOperationType' => 'required|exists:operation_types,id',
        'newOperation.process_detail' => 'nullable|string',
        'newOperation.registration_period' => 'required|string'
    ];

    protected $messages = [
        'newOperation.patient_id.required' => 'Hasta seçimi zorunludur.',
        'newOperation.patient_id.exists' => 'Seçilen hasta bulunamadı.',
        'newOperation.process.required' => 'İşlem süreci seçimi zorunludur.',
        'selectedOperationType.required' => 'İşlem tipi seçimi zorunludur.',
        'selectedOperationType.exists' => 'Seçilen işlem tipi bulunamadı.',
        'newOperation.registration_period.required' => 'Kayıt dönemi zorunludur.'
    ];

    // Mount methodu dinamik dropdown için güncellendi

    // Month input için YYYY-MM formatı
    private function getCurrentRegistrationPeriod()
    {
        return Carbon::now()->format('Y-m');
    }

    // YYYY-MM formatını Türkçe ay adına çevir
    private function convertToTurkishMonth($yearMonth)
    {
        $months = [
            '01' => 'ocak', '02' => 'şubat', '03' => 'mart', '04' => 'nisan',
            '05' => 'mayıs', '06' => 'haziran', '07' => 'temmuz', '08' => 'ağustos',
            '09' => 'eylül', '10' => 'ekim', '11' => 'kasım', '12' => 'aralık'
        ];
        
        $parts = explode('-', $yearMonth);
        if (count($parts) === 2) {
            $year = $parts[0];
            $month = $parts[1];
            return $months[$month] . ' ' . $year;
        }
        
        return $yearMonth;
    }

    // Türkçe ay adını YYYY-MM formatına çevir
    private function convertFromTurkishMonth($turkishMonth)
    {
        $months = [
            'ocak' => '01', 'şubat' => '02', 'mart' => '03', 'nisan' => '04',
            'mayıs' => '05', 'haziran' => '06', 'temmuz' => '07', 'ağustos' => '08',
            'eylül' => '09', 'ekim' => '10', 'kasım' => '11', 'aralık' => '12'
        ];
        
        $parts = explode(' ', $turkishMonth);
        if (count($parts) === 2) {
            $monthName = strtolower($parts[0]);
            $year = $parts[1];
            if (isset($months[$monthName])) {
                return $year . '-' . $months[$monthName];
            }
        }
        
        return $turkishMonth;
    }

    // Hasta arama fonksiyonu
    public function updatedPatientSearch()
    {
        if (empty($this->patientSearch)) {
            $this->filteredPatients = [];
        } else {
            // Veritabanından direkt arama yap
            $query = Patient::where(function($q) {
                $q->where('first_name', 'like', '%' . $this->patientSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->patientSearch . '%')
                  ->orWhereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%' . $this->patientSearch . '%']);
                
                // TC kimlik arama
                if (preg_match('/^[0-9]+$/', $this->patientSearch)) {
                    // Tam TC kimlik (11 haneli) için şifreli arama
                    if (strlen($this->patientSearch) === 11) {
                        $patient = new Patient();
                        $encryptedTc = $patient->encryptField('tc_identity', $this->patientSearch);
                        $q->orWhere('tc_identity', $encryptedTc);
                    }
                    // Kısmi TC arama için şifresiz arama da dene
                    $q->orWhere('tc_identity', 'like', '%' . $this->patientSearch . '%');
                }
            });
            
            // Doktor bazlı filtreleme
            $user = Auth::user();
            if ($user->role === 'doctor') {
                $query->where('doctor_id', $user->id);
            } elseif ($user->role === 'secretary') {
                $query->where('doctor_id', $user->doctor_id);
            } elseif ($user->role === 'nurse' && $user->doctor_id) {
                $query->where('doctor_id', $user->doctor_id);
            }
            // Admin için tüm hastalar görünür (ek filtre yok)
            
            $this->filteredPatients = $query->orderBy('first_name')->orderBy('last_name')->get();
        }
    }

    // Hasta detaylarını göster
    public function showPatientDetails($patientId)
    {
        $this->selectedPatient = Patient::find($patientId);
        $this->showPatientDetails = true;
    }

    // Hasta detay modalını kapat
    public function closePatientDetails()
    {
        $this->selectedPatient = null;
        $this->showPatientDetails = false;
    }

    // Process alanı real-time validation
    public function updatedNewOperationProcess()
    {
        $this->validateOnly('newOperation.process');
    }

    // Hasta seç ve modalı kapat
    public function selectPatient($patientId)
    {
        $this->newOperation['patient_id'] = $patientId;
        $this->closePatientDetails();
    }

    // İstatistik hesaplama methodları
    public function getSurgeryCountProperty()
    {
        return Operation::where('process', 'surgery')->count();
    }

    public function getMesotherapyCountProperty()
    {
        return Operation::where('process', 'mesotherapy')->count();
    }

    public function getBotoxCountProperty()
    {
        $user = auth()->user();
        $query = Operation::where('process', 'botox');
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->count();
    }

    public function getFillerCountProperty()
    {
        $user = auth()->user();
        $query = Operation::where('process', 'filler');
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->count();
    }

    public function getTotalOperationsProperty()
    {
        $user = auth()->user();
        $query = Operation::query();
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->count();
    }

    public function getTodayOperationsProperty()
    {
        $user = auth()->user();
        // Bugünkü kayıt dönemi için (registration_period'a göre)
        $todayPeriod = $this->convertToTurkishMonth(Carbon::today()->format('Y-m'));
        $query = Operation::where('registration_period', $todayPeriod);
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->count();
    }

    public function getThisMonthOperationsProperty()
    {
        $user = auth()->user();
        // Bu ayın kayıt dönemi için (registration_period'a göre)
        $currentPeriod = $this->convertToTurkishMonth(Carbon::now()->format('Y-m'));
        $query = Operation::where('registration_period', $currentPeriod);
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->count();
    }

    public function getStatsProperty()
    {
        $user = auth()->user();
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $previousYear = $currentYear - 1;
        
        // Doktor bazlı query oluştur
        $operationQuery = Operation::query();
        $patientQuery = \App\Models\Patient::query();
        
        if ($user->role === 'doctor') {
            $operationQuery->where('doctor_id', $user->id);
            $patientQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $operationQuery->where('doctor_id', $user->doctor_id);
            $patientQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm verileri görebilir (ek filtre yok)
        
        // Bu yıl toplam operasyonlar (registration_period'a göre)
        $thisYearOperations = (clone $operationQuery)->where('registration_period', 'like', '%' . $currentYear)->count();
        
        // Bu ay operasyonlar (registration_period'a göre)
        $currentPeriod = $this->convertToTurkishMonth(now()->format('Y-m'));
        $thisMonthOperations = (clone $operationQuery)->where('registration_period', $currentPeriod)->count();
        
        // Toplam operasyonlar
        $totalOperations = (clone $operationQuery)->count();
        
        // Toplam hasta sayısı
        $totalPatients = $patientQuery->count();
        
        // Geçen yıl aynı dönem karşılaştırması (registration_period'a göre)
        $previousYearSameMonthPeriod = $this->convertToTurkishMonth($previousYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT));
        $previousYearSameMonth = (clone $operationQuery)->where('registration_period', $previousYearSameMonthPeriod)->count();
        
        $previousYearTotal = (clone $operationQuery)->where('registration_period', 'like', '%' . $previousYear)->count();
        
        // Yüzde hesaplamaları
        $monthlyPercentageChange = 0;
        $yearlyPercentageChange = 0;
        
        if ($previousYearSameMonth > 0) {
            $monthlyPercentageChange = (($thisMonthOperations - $previousYearSameMonth) / $previousYearSameMonth) * 100;
        }
        
        if ($previousYearTotal > 0) {
            $yearlyPercentageChange = (($thisYearOperations - $previousYearTotal) / $previousYearTotal) * 100;
        }
        
        return [
            'total_operations' => $totalOperations,
            'this_year_operations' => $thisYearOperations,
            'this_month_operations' => $thisMonthOperations,
            'total_patients' => $totalPatients,
            'monthly_percentage_change' => round($monthlyPercentageChange, 1),
            'yearly_percentage_change' => round($yearlyPercentageChange, 1),
            'has_previous_year_data' => $previousYearTotal > 0
        ];
    }

    public function loadOperations()
    {
        $user = auth()->user();
        
        $query = Operation::with(['patient', 'creator', 'doctor', 'operationType'])
            ->when($this->searchTerm, function($q) {
                $q->whereHas('patient', function($patientQuery) {
                    $patientQuery->where('first_name', 'like', '%' . $this->searchTerm . '%')
                                ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%')
                                ->orWhereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%' . $this->searchTerm . '%']);
                    
                    // TC kimlik arama - hem şifreli hem şifresiz
                    if (preg_match('/^[0-9]+$/', $this->searchTerm)) {
                        // Tam TC kimlik (11 haneli) için şifreli arama
                        if (strlen($this->searchTerm) === 11) {
                            $patient = new Patient();
                            $encryptedTc = $patient->encryptField('tc_identity', $this->searchTerm);
                            $patientQuery->orWhere('tc_identity', $encryptedTc);
                        }
                        // Kısmi TC arama için şifresiz arama da dene
                        $patientQuery->orWhere('tc_identity', 'like', '%' . $this->searchTerm . '%');
                    }
                })
                ->orWhere('process_detail', 'like', '%' . $this->searchTerm . '%');
            })

            ->when($this->filterProcess, function($q) {
                $q->where('process', $this->filterProcess);
            })
            ->when($this->filterRegistrationPeriod, function($q) {
                $q->where('registration_period', $this->filterRegistrationPeriod);
            });

        // Doktor bazlı erişim kontrolü
        if ($user->role === 'doctor') {
            // Doktor sadece kendi operasyonlarını görebilir
            $query->byDoctor($user->id);
        } elseif (in_array($user->role, ['nurse', 'secretary'])) {
            // Hemşire ve sekreter sadece bağlı olduğu doktorun operasyonlarını görebilir
            $query->byDoctor($user->doctor_id);
        }
        // Admin tüm operasyonları görebilir (ek filtre yok)

        $this->operations = $query->orderBy('created_at', 'desc')
                                 ->orderBy('process_date', 'desc')
                                 ->get();
    }

    public function loadPatients()
    {
        $user = auth()->user();
        $query = Patient::orderBy('first_name')
                       ->orderBy('last_name');
        
        // Doktor bazlı hasta filtreleme
        if ($user->role === 'doctor') {
            // Doktor sadece kendi hastalarını görebilir
            $query->where('doctor_id', $user->id);
        } elseif (in_array($user->role, ['nurse', 'secretary'])) {
            // Hemşire ve sekreter sadece bağlı olduğu doktorun hastalarını görebilir
            $query->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm hastaları görebilir (ek filtre yok)
        
        $this->patients = $query->get();
    }

    public function create()
    {
        try {
            $this->validate();
            
            $user = auth()->user();
            
            // Hasta yetki kontrolü
            $patient = Patient::find($this->newOperation['patient_id']);
            if (!$patient) {
                session()->flash('error', 'Seçilen hasta bulunamadı.');
                return;
            }
            
            // Kullanıcının bu hastaya erişim yetkisi var mı kontrol et
            if ($user->role === 'doctor' && $patient->doctor_id !== $user->id) {
                session()->flash('error', 'Bu hastaya operasyon ekleme yetkiniz yok.');
                return;
            } elseif (in_array($user->role, ['nurse', 'secretary']) && $patient->doctor_id !== $user->doctor_id) {
                session()->flash('error', 'Bu hastaya operasyon ekleme yetkiniz yok.');
                return;
            }
            
            // Doktor ID'sini belirle
            $doctorId = null;
            if ($user->role === 'doctor') {
                $doctorId = $user->id;
            } elseif (in_array($user->role, ['nurse', 'secretary'])) {
                $doctorId = $user->doctor_id;
            } elseif ($user->role === 'admin') {
                // Admin için hasta doktorunu kullan
                $doctorId = $patient->doctor_id;
            }

            // created_by logic: doctor ise kendi id'si, nurse/secretary ise doctor_id'si
            $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

            $operation = Operation::create([
                'patient_id' => $this->newOperation['patient_id'],
                'process' => $this->newOperation['process'],
                'process_type' => $this->selectedOperationType,
                'process_detail' => $this->newOperation['process_detail'],
                'process_date' => Carbon::today(),
                'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period']),
                'created_by' => $createdBy,
                'doctor_id' => $doctorId
            ]);

            // Activities tablosuna kayıt ekle
            Activity::create([
                'type' => 'operation_added',
                'description' => 'Yeni operasyon eklendi: ' . $this->newOperation['process'] . ' - ' . substr($this->newOperation['process_detail'], 0, 50) . (strlen($this->newOperation['process_detail']) > 50 ? '...' : ''),
                'patient_id' => $this->newOperation['patient_id'],
                'doctor_id' => $doctorId
            ]);

            $this->closeModal();
            $this->loadOperations();

            session()->flash('message', 'Operasyon başarıyla eklendi.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation hataları otomatik olarak gösterilir
            throw $e;
        } catch (\Exception $e) {
            // Database veya diğer hatalar
            session()->flash('error', 'Operasyon kaydedilirken bir hata oluştu: ' . $e->getMessage());
            \Log::error('Operation creation error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'operation_data' => $this->newOperation,
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function edit($operationId)
    {
        $operation = Operation::findOrFail($operationId);

        if (!$this->canEdit($operation)) {
            session()->flash('error', 'Bu operasyonu düzenleme yetkiniz yok.');
            return;
        }

        $this->editingOperation = $operation->id;
        $this->selectedOperationType = $operation->process_type;
        
        // Eğer process_type varsa, operation type search'ü set et
        if ($operation->process_type) {
            $operationType = OperationType::find($operation->process_type);
            if ($operationType) {
                $this->operationTypeSearch = $operationType->name;
                $this->loadOperationDetails($operation->process_type);
            }
        }
        
        $this->newOperation = [
            'patient_id' => $operation->patient_id,
            'process' => $operation->process,
            'process_detail' => $operation->process_detail,
            'registration_period' => $this->convertFromTurkishMonth($operation->registration_period)
        ];
        
        $this->showModal = true;
    }

    public function update()
    {
        try {
            $this->validate();

            $operation = Operation::findOrFail($this->editingOperation);

            if (!$this->canEdit($operation)) {
                session()->flash('error', 'Bu operasyonu düzenleme yetkiniz yok.');
                return;
            }

            $operation->update([
                'patient_id' => $this->newOperation['patient_id'],
                'process' => $this->newOperation['process'],
                'process_type' => $this->selectedOperationType,
                'process_detail' => $this->newOperation['process_detail'],
                'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period'])
            ]);

            // Activities tablosuna kayıt ekle
            $user = auth()->user();
            $doctorId = $user->role === 'doctor' ? $user->id : (in_array($user->role, ['nurse', 'secretary']) ? $user->doctor_id : null);
            
            Activity::create([
                'type' => 'operation_updated',
                'description' => 'Operasyon güncellendi: ' . $this->newOperation['process'] . ' - ' . substr($this->newOperation['process_detail'], 0, 50) . (strlen($this->newOperation['process_detail']) > 50 ? '...' : ''),
                'patient_id' => $this->newOperation['patient_id'],
                'doctor_id' => $doctorId
            ]);

            $this->closeModal();
            $this->loadOperations();

            session()->flash('message', 'Operasyon başarıyla güncellendi.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation hataları otomatik olarak gösterilir
            throw $e;
        } catch (\Exception $e) {
            // Database veya diğer hatalar
            session()->flash('error', 'Operasyon güncellenirken bir hata oluştu. Lütfen tüm alanları doğru şekilde doldurun.');
            \Log::error('Operation update error: ' . $e->getMessage());
        }
    }

    public function delete($operationId)
    {
        $operation = Operation::findOrFail($operationId);

        if (!$this->canDelete($operation)) {
            session()->flash('error', 'Bu operasyonu silme yetkiniz yok.');
            return;
        }

        // Activities tablosuna kayıt ekle (silmeden önce)
        $user = auth()->user();
        $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
        
        Activity::create([
            'type' => 'operation_deleted',
            'description' => 'Operasyon silindi: ' . $operation->process . ' - ' . substr($operation->process_detail, 0, 50) . (strlen($operation->process_detail) > 50 ? '...' : ''),
            'patient_id' => $operation->patient_id,
            'doctor_id' => $doctorId
        ]);

        $operation->delete();
        $this->loadOperations();

        session()->flash('message', 'Operasyon başarıyla silindi.');
    }



    public function resetForm()
    {
        $this->newOperation = [
            'patient_id' => '',
            'process' => '',
            'operation_detail_id' => null,
            'process_detail' => '',
            'registration_period' => $this->getCurrentRegistrationPeriod()
        ];
        $this->patientSearch = '';
        $this->filteredPatients = []; // Hasta seçimi temizlendiğinde liste boş olsun
        $this->selectedPatient = null; // Seçili hastayı da temizle
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->editingOperation = null;
    }

    public function resetEditForm()
    {
        $this->editingOperation = null;
        $this->newOperation = [];
        $this->showModal = false;
    }

    public function updatedSearchTerm()
    {
        $this->loadOperations();
    }



    public function updatedFilterProcess()
    {
        $this->loadOperations();
    }

    public function updatedFilterRegistrationPeriod()
    {
        $this->loadOperations();
    }

    // Yetki kontrol methodları
    public function canEdit($operation)
    {
        $user = auth()->user();

        // Admin her şeyi düzenleyebilir
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor sadece kendi işlemlerini düzenleyebilir
        if ($user->role === 'doctor') {
            return $operation->doctor_id === $user->id;
        }

        // Hemşire ve sekreter aynı doktora bağlı işlemleri düzenleyebilir
        if (in_array($user->role, ['nurse', 'secretary'])) {
            return $operation->doctor_id === $user->doctor_id;
        }

        // Diğer durumlar için sadece kendi oluşturduğu kayıtları düzenleyebilir
        // created_by kontrolü: doctor ise kendi id'si, nurse/secretary ise doctor_id'si ile karşılaştır
        $expectedCreatedBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;
        return $operation->created_by === $expectedCreatedBy;
    }

    public function canDelete($operation)
    {
        return $this->canEdit($operation);
    }

    public function canCreate()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor', 'nurse']);
    }

    public function render()
    {
        return view('livewire.operation-list');
    }

    // Notes Methods
    public function showNotes($operationId)
    {
        $this->selectedOperationForNotes = Operation::find($operationId);
        $this->loadOperationNotes($operationId);
        $this->showNotesModal = true;
        $this->resetNoteForm();
    }

    public function closeNotesModal()
    {
        $this->showNotesModal = false;
        $this->selectedOperationForNotes = null;
        $this->operationNotes = [];
        $this->resetNoteForm();
        $this->editingNote = null;
    }

    public function loadOperationNotes($operationId)
    {
        $user = Auth::user();
        
        $query = OperationNote::where('operation_id', $operationId)
            ->with(['user', 'doctor'])
            ->where(function($query) use ($user) {
                // Public notları göster
                $query->where('is_private', false)
                    // Veya kullanıcının kendi private notlarını göster
                    ->orWhere(function($subQuery) use ($user) {
                        $subQuery->where('is_private', true)
                                ->where('user_id', $user->id);
                    });
            });
            
        // Doktor bazlı not filtreleme
        if ($user->role === 'doctor') {
            // Doktor sadece kendi notlarını görebilir
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            // Sekreter sadece bağlı olduğu doktorun notlarını görebilir
            $query->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm notları görebilir (ek filtre yok)
        
        $this->operationNotes = $query->orderBy('created_at', 'desc')->get();
    }

    public function saveNote()
    {
        $this->validate([
            'newNote.content' => 'required|string',
            'newNote.note_type' => 'required|string',
            'newNote.is_private' => 'boolean'
        ]);

        if ($this->editingNote) {
            // Düzenleme işlemi
            $note = OperationNote::find($this->editingNote);
            
            // Yetki kontrolü
            if (!$this->canEditNote($note)) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => 'Bu notu düzenleme yetkiniz yok.'
                ]);
                return;
            }

            $note->update([
                'content' => $this->newNote['content'],
                'note_type' => $this->newNote['note_type'],
                'is_private' => $this->newNote['is_private'],
                'last_updated' => now()
            ]);

            // Activities tablosuna kayıt ekle
            Activity::create([
                'type' => 'operation_note_updated',
                'description' => 'Operasyon notu güncellendi: ' . substr($this->newNote['content'], 0, 50) . (strlen($this->newNote['content']) > 50 ? '...' : ''),
                'patient_id' => $note->operation->patient_id,
                'doctor_id' => $doctorId
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Not başarıyla güncellendi.'
            ]);
        } else {
            // Yeni not ekleme
            $user = Auth::user();
            
            // Doktor ID'sini belirle
            $doctorId = null;
            if ($user->role === 'doctor') {
                $doctorId = $user->id;
            } elseif ($user->role === 'secretary') {
                $doctorId = $user->doctor_id;
            }
            
            $operationNote = OperationNote::create([
                'operation_id' => $this->selectedOperationForNotes->id,
                'user_id' => Auth::id(),
                'doctor_id' => $doctorId,
                'content' => $this->newNote['content'],
                'note_type' => $this->newNote['note_type'],
                'is_private' => $this->newNote['is_private'],
                'note_date' => now(),
                'last_updated' => now()
            ]);

            // Activities tablosuna kayıt ekle
            Activity::create([
                'type' => 'operation_note_added',
                'description' => 'Operasyon notu eklendi: ' . substr($this->newNote['content'], 0, 50) . (strlen($this->newNote['content']) > 50 ? '...' : ''),
                'patient_id' => $this->selectedOperationForNotes->patient_id,
                'doctor_id' => $doctorId
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Not başarıyla eklendi.'
            ]);
        }

        $this->loadOperationNotes($this->selectedOperationForNotes->id);
        $this->resetNoteForm();
        $this->editingNote = null;
    }

    public function editNote($noteId)
    {
        $note = OperationNote::find($noteId);
        
        if (!$this->canEditNote($note)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Bu notu düzenleme yetkiniz yok.'
            ]);
            return;
        }

        $this->editingNote = $noteId;
        $this->newNote = [
            'content' => $note->content,
            'note_type' => $note->note_type,
            'is_private' => $note->is_private
        ];
    }

    public function deleteNote($noteId)
    {
        $note = OperationNote::find($noteId);
        
        if (!$this->canEditNote($note)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Bu notu silme yetkiniz yok.'
            ]);
            return;
        }

        // Activities tablosuna kayıt ekle (silmeden önce)
        $user = auth()->user();
        $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
        
        Activity::create([
            'type' => 'operation_note_deleted',
            'description' => 'Operasyon notu silindi: ' . substr($note->content, 0, 50) . (strlen($note->content) > 50 ? '...' : ''),
            'patient_id' => $note->operation->patient_id,
            'doctor_id' => $doctorId
        ]);

        $note->delete();
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Not başarıyla silindi.'
        ]);
        
        $this->loadOperationNotes($this->selectedOperationForNotes->id);
    }

    public function canEditNote($note)
    {
        $user = Auth::user();
        
        // Doktor sadece kendi notlarını düzenleyebilir
        if ($user->role === 'doctor') {
            return $note->user_id === $user->id;
        }
        
        // Nurse ve secretary birbirlerinin notlarını düzenleyebilir
        // ama doktor notlarına dokunamaz
        if ($note->user->role === 'doctor') {
            return false;
        }
        
        return true; // nurse/secretary birbirlerinin notlarını düzenleyebilir
    }

    public function resetNoteForm()
    {
        $this->newNote = [
            'content' => '',
            'note_type' => 'general',
            'is_private' => false
        ];
    }

    public function getNoteTypeIcon($type)
    {
        return match($type) {
            'medical' => 'fas fa-stethoscope',
            'procedure' => 'fas fa-scalpel-path',
            'followup' => 'fas fa-calendar-check',
            'general' => 'fas fa-sticky-note',
            default => 'fas fa-sticky-note'
        };
    }

    public function getNoteTypeText($type)
    {
        return match($type) {
            'medical' => 'Tıbbi',
            'procedure' => 'İşlem',
            'followup' => 'Takip',
            'general' => 'Genel',
            default => 'Genel'
        };
    }

    public function canDeleteNote($note)
    {
        $user = Auth::user();
        
        // Doktor sadece kendi notlarını silebilir
        if ($user->role === 'doctor') {
            return $note->user_id === $user->id;
        }
        
        // Nurse ve secretary birbirlerinin notlarını silebilir
        // ama doktor notlarına dokunamaz
        if ($note->user->role === 'doctor') {
            return false;
        }
        
        return true; // nurse/secretary birbirlerinin notlarını silebilir
    }

    public function getAvailablePeriods()
    {
        return Operation::distinct()
            ->pluck('registration_period')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }
    
    // İstatistik dönemi değiştirme
    public function changeStatsPeriod($period)
    {
        $this->statsPeriod = $period;
        session(['operation_stats_period' => $period]);
        
        // JavaScript'e event gönder
        $this->dispatch('statsperiodchanged', $period);
    }
    
    // Yeni istatistik sistemi
    public function getProcessStatsProperty()
    {
        $currentDate = now();
        $processes = ['surgery', 'mesotherapy', 'botox', 'filler'];
        $stats = [];
        
        foreach ($processes as $process) {
            $current = $this->getProcessCount($process, $this->statsPeriod);
            $previous = $this->getProcessCount($process, $this->statsPeriod, true);
            
            $percentageChange = 0;
            if ($previous > 0) {
                $percentageChange = (($current - $previous) / $previous) * 100;
            }
            
            $stats[$process] = [
                'current' => $current,
                'previous' => $previous,
                'percentage_change' => round($percentageChange, 1),
                'label' => $this->getProcessLabel($process)
            ];
        }
        
        return $stats;
    }
    
    private function getProcessCount($process, $period, $isPrevious = false)
    {
        $user = auth()->user();
        $query = Operation::where('process', $process);
        
        // Doktor bazlı filtreleme ekle
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm verileri görebilir (ek filtre yok)
        
        switch ($period) {
            case 'monthly':
                $month = $isPrevious ? now()->subMonth() : now();
                $targetPeriod = $this->convertToTurkishMonth($month->format('Y-m'));
                $query->where('registration_period', $targetPeriod);
                break;
                
            case 'yearly':
                $year = $isPrevious ? now()->subYear()->year : now()->year;
                $query->where('registration_period', 'like', '%' . $year);
                break;
                
            case 'this_year':
                $year = $isPrevious ? now()->subYear()->year : now()->year;
                $query->where('registration_period', 'like', '%' . $year);
                break;
                
            case 'all':
                if ($isPrevious) {
                    // Tümü için önceki ay karşılaştırması
                    $previousMonth = now()->subMonth();
                    $targetPeriod = $this->convertToTurkishMonth($previousMonth->format('Y-m'));
                    $query->where('registration_period', '<', $targetPeriod);
                } else {
                    // Tüm veriler - herhangi bir filtreleme yok
                }
                break;
        }
        
        return $query->count();
    }
    
    private function getProcessLabel($process)
    {
        return match($process) {
            'surgery' => 'Ameliyat',
            'mesotherapy' => 'Mezoterapi',
            'botox' => 'Botoks',
            'filler' => 'Dolgu',
            default => $process
        };
    }
    
    public function getPeriodLabelProperty()
    {
        return match($this->statsPeriod) {
            'monthly' => 'Aylık',
            'yearly' => 'Yıllık',
            'this_year' => 'Bu Yıl',
            'all' => 'Tümü',
            default => 'Aylık'
        };
    }

    // Dinamik dropdown methodları
    public function mount()
    {
        $this->loadOperations();
        $this->loadPatients();
        $this->loadOperationTypes();
        $this->resetForm();
        $this->currentRegistrationPeriod = $this->getCurrentRegistrationPeriod();
        $this->filteredPatients = [];
        
        $this->statsPeriod = session('operation_stats_period', 'monthly');
    }

    public function loadOperationTypes()
    {
        $user = auth()->user();
        
        // Doktor ID'sini belirle
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
        
        $this->operationTypes = OperationType::active()
            ->forDoctor($doctorId)
            ->ordered()
            ->get();
    }

    public function loadOperationDetails($operationTypeId = null)
    {
        if ($operationTypeId) {
            $query = OperationDetail::active()
                ->byType($operationTypeId)
                ->ordered();
            
            // Arama filtresi
            if (!empty($this->operationDetailSearch)) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->operationDetailSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->operationDetailSearch . '%');
                });
            }
            
            $this->operationDetails = $query->get();
        } else {
            $this->operationDetails = [];
        }
    }

    public function updatedSelectedOperationType()
    {
        $this->loadOperationDetails($this->selectedOperationType);
        
        // Sadece yeni kayıt ekleme modunda process_detail'ı temizle
        // Güncelleme modunda mevcut detayı koru
        if (!$this->editingOperation) {
            $this->newOperation['process_detail'] = '';
        }
    }

    public function updatedOperationTypeSearch()
    {
        if (empty($this->operationTypeSearch)) {
            $this->loadOperationTypes();
        } else {
            $user = auth()->user();
            
            // Doktor ID'sini belirle
            $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
            
            $this->operationTypes = OperationType::active()
                ->forDoctor($doctorId)
                ->where('name', 'like', '%' . $this->operationTypeSearch . '%')
                ->ordered()
                ->get();
        }
    }

    public function updatedOperationDetailSearch()
    {
        if (empty($this->operationDetailSearch)) {
            $this->loadOperationDetails($this->selectedOperationType);
        } else {
            $this->operationDetails = OperationDetail::active()
                ->when($this->selectedOperationType, function($query) {
                    $query->byType($this->selectedOperationType);
                })
                ->where('name', 'like', '%' . $this->operationDetailSearch . '%')
                ->ordered()
                ->get();
        }
        $this->showOperationDetailDropdown = true;
    }

    public function openAddOperationTypeModal()
    {
        $this->showAddOperationTypeModal = true;
        $this->operationTypeForm = [
            'name' => '',
            'value' => ''
        ];
    }

    public function closeAddOperationTypeModal()
    {
        $this->showAddOperationTypeModal = false;
        $this->resetOperationTypeForm();
    }

    public function addOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255'
        ]);

        $user = auth()->user();
        
        // created_by logic: doctor ise kendi id'si, nurse/secretary ise doctor_id'si
        $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $operationType = OperationType::create([
            'name' => $this->operationTypeForm['name'],
            'value' => strtolower(str_replace(' ', '_', $this->operationTypeForm['name'])),
            'is_active' => true,
            'sort_order' => (OperationType::max('sort_order') ?? 0) + rand(1, 100),
            'created_by' => $createdBy
        ]);

        $this->loadOperationTypes();
        $this->selectedOperationType = $operationType->id;
        $this->operationTypeSearch = $operationType->name;
        $this->newOperation['process'] = $operationType->value;
        $this->loadOperationDetails($operationType->id);
        $this->closeAddOperationTypeModal();

        // Event dispatch ederek diğer component'lerin güncellenmesini sağla
        $this->dispatch('operation-type-added', operationTypeId: $operationType->id);

        session()->flash('message', 'Yeni işlem türü başarıyla eklendi.');
    }

    public function createOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255'
        ]);

        // Name'den otomatik benzersiz value oluştur
        $baseValue = \Str::slug($this->operationTypeForm['name'], '_');
        $value = $baseValue;
        $counter = 1;
        
        // Benzersiz value bulana kadar dene
        while (OperationType::where('value', $value)->exists()) {
            $value = $baseValue . '_' . $counter;
            $counter++;
        }

        // created_by logic: doctor ise kendi id'si, nurse/secretary ise doctor_id'si
        $user = auth()->user();
        $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $operationType = OperationType::create([
            'name' => $this->operationTypeForm['name'],
            'value' => $value,
            'is_active' => true,
            'sort_order' => (OperationType::max('sort_order') ?? 0) + rand(1, 100),
            'created_by' => $createdBy
        ]);

        $this->loadOperationTypes();
        $this->selectedOperationType = $operationType->id;
        $this->newOperation['process'] = $operationType->value;
        $this->closeAddOperationTypeModal();

        // Event dispatch ederek diğer component'lerin güncellenmesini sağla
        $this->dispatch('operation-type-added', operationTypeId: $operationType->id);

        session()->flash('message', 'Yeni işlem türü başarıyla eklendi.');
    }

    public function editOperationType($operationTypeValue)
    {
        $operationType = OperationType::where('value', $operationTypeValue)->firstOrFail();
        
        $this->editingOperationType = $operationType->id;
        $this->operationTypeForm = [
            'name' => $operationType->name,
            'value' => $operationType->value
        ];
    }

    public function updateOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255',
            'operationTypeForm.value' => 'required|string|max:255|unique:operation_types,value,' . $this->editingOperationType
        ]);

        $operationType = OperationType::findOrFail($this->editingOperationType);
        
        $operationType->update([
            'name' => $this->operationTypeForm['name'],
            'value' => $this->operationTypeForm['value']
        ]);

        $this->loadOperationTypes();
        $this->resetOperationTypeForm();

        session()->flash('message', 'İşlem türü başarıyla güncellendi.');
    }

    public function deleteOperationType($operationTypeValue)
    {
        $operationType = OperationType::where('value', $operationTypeValue)->firstOrFail();

        $operationType->delete();
        $this->loadOperationTypes();

        session()->flash('message', 'İşlem türü başarıyla silindi.');
    }

    public function resetOperationTypeForm()
    {
        $this->editingOperationType = null;
        $this->operationTypeForm = ['name' => '', 'value' => ''];
    }

    public function showAddOperationDetailModal()
    {
        if (!$this->selectedOperationType) {
            session()->flash('error', 'Önce bir işlem türü seçmelisiniz.');
            return;
        }

        $this->showAddOperationDetailModal = true;
        $this->newOperationDetail = [
            'name' => '',
            'description' => ''
        ];
    }

    public function closeAddOperationDetailModal()
    {
        $this->showAddOperationDetailModal = false;
        $this->editingOperationDetail = null;
        $this->newOperationDetail = [
            'name' => '',
            'description' => ''
        ];
    }

    public function createOperationDetail()
    {
        // Eğer düzenleme modundaysak updateOperationDetail çağır
        if ($this->editingOperationDetail) {
            $this->updateOperationDetail();
            return;
        }

        $this->validate([
            'newOperationDetail.name' => 'required|string|max:255',
            'newOperationDetail.description' => 'nullable|string'
        ]);

        if (!$this->selectedOperationType) {
            session()->flash('error', 'Önce bir işlem türü seçmelisiniz.');
            return;
        }

        // created_by logic: doctor ise kendi id'si, nurse/secretary ise doctor_id'si
        $user = auth()->user();
        $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $operationDetail = OperationDetail::create([
            'name' => $this->newOperationDetail['name'],
            'description' => $this->newOperationDetail['description'],
            'is_active' => true,
            'sort_order' => (OperationDetail::max('sort_order') ?? 0) + rand(1, 100),
            'created_by' => $createdBy
        ]);

        $this->loadOperationDetails($this->selectedOperationType);
        $this->newOperation['process_detail'] = $operationDetail->name;
        $this->closeAddOperationDetailModal();

        session()->flash('message', 'Yeni işlem detayı başarıyla eklendi.');
    }

    public function selectOperationType($operationTypeId)
    {
        $operationType = OperationType::find($operationTypeId);
        if ($operationType) {
            $this->selectedOperationType = $operationTypeId;

            $this->operationTypeSearch = $operationType->name;
            $this->loadOperationDetails($operationTypeId);
            
            // Sadece yeni kayıt ekleme modunda detayı temizle
            // Güncelleme modunda mevcut detayı koru
            if (!$this->editingOperation) {
                $this->newOperation['process_detail'] = '';
                $this->operationDetailSearch = '';
            }
            // Alpine.js ile dropdown otomatik kapanacak
        }
    }

    public function selectOperationTypeWithDetail($operationTypeId, $operationDetailId)
    {
        // Önce operation type'ı seç
        $this->selectOperationType($operationTypeId);
        
        // Sonra operation detail'ı seç
        $operationDetail = OperationDetail::find($operationDetailId);
        if ($operationDetail) {
            $this->newOperation['operation_detail_id'] = $operationDetailId;
            $this->newOperation['process_detail'] = $operationDetail->name;
            $this->operationDetailSearch = $operationDetail->name;
            $this->showOperationDetailDropdown = false;
        }
        
        // Operation type dropdown'ını kapat
        $this->showOperationTypeDropdown = false;
    }

    public function selectOperationDetail($operationDetailId)
    {
        $operationDetail = OperationDetail::find($operationDetailId);
        if ($operationDetail) {
            $this->newOperation['operation_detail_id'] = $operationDetailId;
            $this->newOperation['process_detail'] = $operationDetail->name;
            $this->operationDetailSearch = $operationDetail->name;
            $this->showOperationDetailDropdown = false;
        }
    }

    public function editOperationDetail($operationDetailId)
    {
        $operationDetail = OperationDetail::find($operationDetailId);
        if ($operationDetail) {
            $this->editingOperationDetail = $operationDetailId;
            $this->newOperationDetail = [
                'name' => $operationDetail->name,
                'description' => $operationDetail->description
            ];
            $this->showAddOperationDetailModal = true;
        }
    }

    public function updateOperationDetail()
    {
        $this->validate([
            'newOperationDetail.name' => 'required|string|max:255',
            'newOperationDetail.description' => 'nullable|string'
        ]);

        $operationDetail = OperationDetail::find($this->editingOperationDetail);
        if ($operationDetail) {
            $operationDetail->update([
                'name' => $this->newOperationDetail['name'],
                'description' => $this->newOperationDetail['description']
            ]);

            $this->loadOperationDetails($this->selectedOperationType);
            $this->closeAddOperationDetailModal();

            session()->flash('message', 'İşlem detayı başarıyla güncellendi.');
        }
    }

    public function deleteOperationDetail($operationDetailId)
    {
        $operationDetail = OperationDetail::find($operationDetailId);
        if ($operationDetail) {
            $operationDetail->delete();
            $this->loadOperationDetails($this->selectedOperationType);
            session()->flash('message', 'İşlem detayı başarıyla silindi.');
        }
    }

    public function resetDynamicDropdowns()
    {
        $this->selectedOperationType = null;
        $this->operationDetails = [];
        $this->operationTypeSearch = '';
        $this->operationDetailSearch = '';

        $this->newOperation['operation_detail_id'] = null;
    }

    // İşlem Süreci ile ilgili methodlar
    public function selectProcess($processValue)
    {
        $this->newOperation['process'] = $processValue;
        $this->processSearch = $this->processOptions[$processValue] ?? '';
        // Alpine.js ile dropdown otomatik kapanacak
    }

    public function showAddProcessModal()
    {
        $this->showAddProcessModal = true;
        $this->newProcess = [
            'name' => '',
            'value' => '',
            'description' => ''
        ];
    }

    public function closeAddProcessModal()
    {
        $this->showAddProcessModal = false;
        $this->newProcess = [];
    }

    public function addProcess()
    {
        $this->validate([
             'newProcess.name' => 'required|string|max:255',
             'newProcess.value' => 'required|string|max:100',
             'newProcess.description' => 'nullable|string'
         ], [
             'newProcess.name.required' => 'İşlem süreci adı zorunludur.',
             'newProcess.value.required' => 'İşlem süreci değeri zorunludur.'
         ]);

        // Yeni işlem süreci ekle (şimdilik processOptions array'ine ekleyeceğiz)
        // Gelecekte database tablosu oluşturulabilir
        $this->processOptions[$this->newProcess['value']] = $this->newProcess['name'];
        
        // Form'u temizle ve modal'ı kapat
        $this->closeAddProcessModal();
        
        // Yeni eklenen süreci seç
        $this->selectProcess($this->newProcess['value']);
        
        session()->flash('message', 'Yeni işlem süreci başarıyla eklendi.');
    }

    // Event listener method - İşlem tipi eklendiğinde çalışır
    public function refreshOperationTypes($operationTypeId = null)
    {
        // İşlem tipi listesini yenile
        $this->loadOperationTypes();
        
        // Eğer yeni eklenen işlem tipi ID'si varsa, onu seç
        if ($operationTypeId) {
            $this->selectedOperationType = $operationTypeId;
            $operationType = OperationType::find($operationTypeId);
            if ($operationType) {
                $this->operationTypeSearch = $operationType->name;
                $this->newOperation['process'] = $operationType->value;
                $this->loadOperationDetails($operationTypeId);
            }
        }
    }
}
