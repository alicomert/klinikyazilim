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
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OperationsImport;

class OperationList extends Component
{
    use WithPagination, WithFileUploads;

    // Event listeners
    protected $listeners = ['operation-type-added' => 'refreshOperationTypes'];

    // Public properties
    // Not: Sayfalanmış operasyonlar artık public property olarak tutulmuyor.
    // render() içinde görünüme doğrudan aktarılıyor. Bu nedenle $operations
    // property’sini kaldırıyoruz (Livewire, paginator nesnelerini serialize edemez).
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
    public $operationTypeForm = ['name' => '', 'process' => null];
    
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
    public $newProcess = ['name' => '', 'description' => ''];

    // Çoklu operasyon ekleme için
    public $additionalOperations = [];
    public $allOperationTypes = [];

    // Toplu içe aktarma için
    public $showImportModal = false;
    public $importExcelFile;
    public $importJsonFile;
    public $importReport = ['success' => 0, 'errors' => []];

    // Pagination
    protected $paginationTheme = 'tailwind';
    public $perPage = 25; // varsayılan sayfa başına kayıt

    // Validation rules
    // Validation rules - Workspace standartlarına uygun
    protected $rules = [
        'newOperation.patient_id' => 'nullable|exists:patients,id|required_without:newOperation.patient_name',
        'newOperation.patient_name' => 'nullable|string|max:255|required_without:newOperation.patient_id',
        'newOperation.process' => 'required|string|in:surgery,mesotherapy,botox,filler',
        'selectedOperationType' => 'required|exists:operation_types,id',
        'newOperation.process_detail' => 'nullable|string|max:500',
        'newOperation.registration_period' => 'required|string',
        'additionalOperations' => 'array|max:5',
        'additionalOperations.*.process' => 'required|string|in:surgery,mesotherapy,botox,filler',
        'additionalOperations.*.operation_type_id' => 'required|exists:operation_types,id',
        'importExcelFile' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',
        'importJsonFile' => 'nullable|file|mimes:json,txt|max:2048'
    ];

    // Validation attributes - Türkçe alan adları
    protected $validationAttributes = [
        'newOperation.patient_id' => 'hasta',
        'newOperation.patient_name' => 'hasta adı',
        'newOperation.process' => 'işlem süreci',
        'selectedOperationType' => 'işlem tipi',
        'newOperation.process_detail' => 'işlem detayı',
        'newOperation.registration_period' => 'kayıt dönemi',
        'additionalOperations' => 'ek operasyonlar',
        'importExcelFile' => 'Excel dosyası',
        'importJsonFile' => 'JSON dosyası'
    ];

    // Custom validation messages - Türkçe mesajlar
    protected $messages = [
        'newOperation.patient_id.exists' => 'Seçilen hasta sistemde bulunamadı.',
        'newOperation.patient_id.required_without' => 'Hasta seçimi veya hasta adı girişi zorunludur.',
        'newOperation.patient_name.required_without' => 'Hasta seçimi veya hasta adı girişi zorunludur.',
        'newOperation.patient_name.max' => 'Hasta adı en fazla 255 karakter olabilir.',
        'newOperation.process.required' => 'İşlem süreci seçimi zorunludur.',
        'newOperation.process.in' => 'Geçersiz işlem süreci seçimi.',
        'selectedOperationType.required' => 'İşlem tipi seçimi zorunludur.',
        'selectedOperationType.exists' => 'Seçilen işlem tipi sistemde bulunamadı.',
        'newOperation.process_detail.max' => 'İşlem detayı en fazla 500 karakter olabilir.',
        'newOperation.registration_period.required' => 'Kayıt dönemi seçimi zorunludur.',
        'additionalOperations.max' => 'En fazla 5 ek operasyon ekleyebilirsiniz.',
        'additionalOperations.*.process.required' => 'Ek operasyon süreci seçimi zorunludur.',
        'additionalOperations.*.process.in' => 'Geçersiz ek operasyon süreci.',
        'additionalOperations.*.operation_type_id.required' => 'Ek operasyon tipi seçimi zorunludur.',
        'additionalOperations.*.operation_type_id.exists' => 'Seçilen ek operasyon tipi bulunamadı.',
        'importExcelFile.file' => 'Geçersiz dosya seçimi.',
        'importExcelFile.mimes' => 'Sadece Excel dosyaları kabul edilir (xlsx, xls, csv).',
        'importExcelFile.max' => 'Dosya boyutu en fazla 2MB olabilir.',
        'importJsonFile.file' => 'Geçersiz dosya seçimi.',
        'importJsonFile.mimes' => 'Sadece JSON dosyaları kabul edilir (json, txt).',
        'importJsonFile.max' => 'Dosya boyutu en fazla 2MB olabilir.'
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
        // Reset dependent selections when process changes
        $this->selectedOperationType = null;
        $this->operationDetails = [];
        $this->operationTypeSearch = '';
        $this->operationDetailSearch = '';
        $this->loadOperationTypes();
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

        return $query->orderBy('created_at', 'desc')
                     ->orderBy('process_date', 'desc')
                     ->paginate($this->perPage);
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
        // Yetki kontrolü
        if (!$this->canCreate()) {
            session()->flash('error', 'Operasyon ekleme yetkiniz yok.');
            return;
        }

        try {
            $this->validate();
            
            $user = Auth::user();
            $patientId = ($this->newOperation['patient_id'] ?? null) ?: null;
            $patientName = trim($this->newOperation['patient_name'] ?? '');
            $patient = $patientId ? Patient::find($patientId) : null;
            
            // Doktor ID belirleme
            $doctorId = $this->getDoctorId($user, $patient);
            
            // Ana operasyon verisi
            $operationData = [
                'patient_id' => $patientId,
                'process' => $this->newOperation['process'],
                'process_detail' => $this->newOperation['process_detail'] ?? null,
                'process_date' => Carbon::today(),
                'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period']),
                'created_by' => Auth::id(),
                'doctor_id' => $doctorId
            ];
            
            // Dinamik sütun kontrolü
            if (Schema::hasColumn('operations', 'process_type')) {
                $operationData['process_type'] = $this->selectedOperationType;
            }
            if (Schema::hasColumn('operations', 'patient_name')) {
                $operationData['patient_name'] = $patientName ?: null;
            }
            
            // Ana operasyonu oluştur
            $operation = Operation::create($operationData);
            
            // Aktivite kaydı
            $this->createActivity('operation_added', 'Operasyon eklendi: ' . $this->newOperation['process'], $patientId, $doctorId);
            
            // Ek operasyonları işle
            $this->processAdditionalOperations($operationData, $patientName, $patientId, $doctorId);
            
            // Form temizle ve listeyi yenile
            $this->resetForm();
            $this->loadOperations();
            $this->showModal = false;
            
            session()->flash('message', 'Operasyon(lar) başarıyla eklendi.');
            
        } catch (ValidationException $e) {
            // Validation hataları otomatik olarak gösterilir
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Operasyon kaydedilirken bir hata oluştu: ' . $e->getMessage());
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
            'patient_name' => $operation->patient_name ?? '',
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
            
            // Yetki kontrolü
            if (!$this->canEdit($operation)) {
                session()->flash('error', 'Bu operasyonu düzenleme yetkiniz yok.');
                return;
            }
            
            $patientId = ($this->newOperation['patient_id'] ?? null) ?: null;
            $patientName = trim($this->newOperation['patient_name'] ?? '');
            
            // Güncelleme verisi
            $data = [
                'patient_id' => $patientId,
                'process' => $this->newOperation['process'],
                'process_detail' => $this->newOperation['process_detail'] ?? null,
                'process_date' => Carbon::today(),
                'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period']),
            ];
            
            // Dinamik sütun kontrolü
            if (Schema::hasColumn('operations', 'process_type')) {
                $data['process_type'] = $this->selectedOperationType;
            }
            if (Schema::hasColumn('operations', 'patient_name')) {
                $data['patient_name'] = $patientName ?: null;
            }
            
            // Operasyonu güncelle
            $operation->update($data);
            
            // Aktivite kaydı
            $this->createActivity('operation_updated', 'Operasyon güncellendi: ' . $this->newOperation['process'], $patientId, $operation->doctor_id);
            
            // Form temizle ve listeyi yenile
            $this->resetEditForm();
            $this->loadOperations();
            
            session()->flash('message', 'Operasyon başarıyla güncellendi.');
            
        } catch (ValidationException $e) {
            // Validation hataları otomatik olarak gösterilir
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Operasyon güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($operationId)
    {
        try {
            $operation = Operation::findOrFail($operationId);

            // Yetki kontrolü
            if (!$this->canDelete($operation)) {
                session()->flash('error', 'Bu operasyonu silme yetkiniz yok.');
                return;
            }

            // Silme öncesi aktivite kaydı
            $description = 'Operasyon silindi: ' . $operation->process;
            if ($operation->process_detail) {
                $description .= ' - ' . substr($operation->process_detail, 0, 50);
                if (strlen($operation->process_detail) > 50) {
                    $description .= '...';
                }
            }
            
            $this->createActivity('operation_deleted', $description, $operation->patient_id, $operation->doctor_id);

            // Operasyonu sil
            $operation->delete();
            
            // Listeyi yenile
            $this->loadOperations();

            session()->flash('message', 'Operasyon başarıyla silindi.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Operasyon silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }



    public function resetForm()
    {
        $this->newOperation = [
            'patient_id' => null,
            'patient_name' => '',
            'process' => '',
            'operation_detail_id' => null,
            'process_detail' => '',
            'registration_period' => $this->getCurrentRegistrationPeriod()
        ];
        $this->patientSearch = '';
        $this->filteredPatients = []; // Hasta seçimi temizlendiğinde liste boş olsun
        $this->selectedPatient = null; // Seçili hastayı da temizle
        $this->selectedOperationType = null;
        $this->additionalOperations = [];
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
        $this->resetPage();
    }



    public function updatedFilterProcess()
    {
        $this->resetPage();
    }

    public function updatedFilterRegistrationPeriod()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Global görünüm: Tıklayınca görünen kayıt sayısını artır.
     * Varsayılan olarak 25'er artırır.
     */
    public function showMore()
    {
        $step = 25;
        $this->perPage = $this->perPage + $step;
    }

    // Rol Tabanlı Yetkilendirme Sistemi - Workspace Standards
    
    /**
     * Operasyon düzenleme yetkisi kontrolü
     */
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

        return false;
    }

    /**
     * Operasyon silme yetkisi kontrolü
     */
    public function canDelete($operation)
    {
        $user = auth()->user();

        // Admin her şeyi silebilir
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor sadece kendi işlemlerini silebilir
        if ($user->role === 'doctor') {
            return $operation->doctor_id === $user->id;
        }

        // Hemşire ve sekreter aynı doktora bağlı işlemleri silebilir
        if (in_array($user->role, ['nurse', 'secretary'])) {
            return $operation->doctor_id === $user->doctor_id;
        }

        return false;
    }

    /**
     * Operasyon görüntüleme yetkisi kontrolü
     */
    public function canView($operation)
    {
        $user = auth()->user();

        // Admin her şeyi görebilir
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor sadece kendi işlemlerini görebilir
        if ($user->role === 'doctor') {
            return $operation->doctor_id === $user->id;
        }

        // Hemşire ve sekreter aynı doktora bağlı işlemleri görebilir
        if (in_array($user->role, ['nurse', 'secretary'])) {
            return $operation->doctor_id === $user->doctor_id;
        }

        return false;
    }

    /**
     * Operasyon notları düzenleme yetkisi
     */
    public function canEditNotes($operation)
    {
        return $this->canEdit($operation);
    }

    /**
     * İçe aktarma yetkisi kontrolü
     */
    public function canImport()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor']);
    }

    /**
     * Operasyon türü yönetimi yetkisi
     */
    public function canManageOperationTypes()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor']);
    }

    public function render()
    {
        // Sayfalanmış operasyonları sadece görünüme aktar (public property olarak saklama)
        $operations = $this->loadOperations();
        return view('livewire.operation-list', [
            'operations' => $operations,
        ]);
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
        $this->loadPatients();
        $this->loadOperationTypes();
        $this->resetForm();
        $this->currentRegistrationPeriod = $this->getCurrentRegistrationPeriod();
        $this->filteredPatients = [];
        
        $this->statsPeriod = session('operation_stats_period', 'monthly');
        
        // Set default filter to show all operations at first load
        $this->filterProcess = '';
    }

    public function loadOperationTypes()
    {
        $user = auth()->user();
        
        // Doktor ID'sini belirle
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
    
        $query = OperationType::active()->forDoctor($doctorId)->ordered();
        // Apply process filter only when not managing types modal
        if (!empty($this->newOperation['process']) && !$this->showAddOperationTypeModal) {
            $query->where('process', $this->newOperation['process']);
        }
        $this->operationTypes = $query->get();
        // Ek Operasyonlar için tüm tipleri (process filtresi olmadan) ayrıca tut
        $this->allOperationTypes = OperationType::active()
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
            $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
            $query = OperationType::active()
                ->forDoctor($doctorId)
                ->ordered();
            if (!empty($this->newOperation['process'])) {
                $query->where('process', $this->newOperation['process']);
            }
            $this->operationTypes = $query
                ->where('name', 'like', '%' . $this->operationTypeSearch . '%')
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
            'process' => null
        ];
        // Ensure full list is loaded and search reset when managing operation types
        $this->operationTypeSearch = '';
        $this->loadOperationTypes();
    }

    public function closeAddOperationTypeModal()
    {
        $this->showAddOperationTypeModal = false;
        $this->resetOperationTypeForm();
    }

    public function addOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255',
            'operationTypeForm.process' => 'required|in:surgery,mesotherapy,botox,filler'
        ]);

        $user = auth()->user();
        $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $operationType = OperationType::create([
            'name' => $this->operationTypeForm['name'],
            'process' => $this->operationTypeForm['process'],
            'is_active' => true,
            'sort_order' => (OperationType::max('sort_order') ?? 0) + rand(1, 100),
            'created_by' => $createdBy
        ]);

        $this->loadOperationTypes();
        $this->selectedOperationType = $operationType->id;
        $this->operationTypeSearch = $operationType->name;
        $this->newOperation['process'] = $operationType->process;
        $this->loadOperationDetails($operationType->id);
        $this->closeAddOperationTypeModal();

        // Event dispatch ederek diğer component'lerin güncellenmesini sağla
        $this->dispatch('operation-type-added', operationTypeId: $operationType->id);

        session()->flash('message', 'Yeni işlem türü başarıyla eklendi.');
    }

    public function createOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255',
            'operationTypeForm.process' => 'required|in:surgery,mesotherapy,botox,filler'
        ]);

        $user = auth()->user();
        $createdBy = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $operationType = OperationType::create([
            'name' => $this->operationTypeForm['name'],
            'process' => $this->operationTypeForm['process'],
            'is_active' => true,
            'sort_order' => (OperationType::max('sort_order') ?? 0) + rand(1, 100),
            'created_by' => $createdBy
        ]);

        $this->loadOperationTypes();
        $this->selectedOperationType = $operationType->id;
        $this->operationTypeSearch = $operationType->name;
        $this->closeAddOperationTypeModal();

        // Event dispatch ederek diğer component'lerin güncellenmesini sağla
        $this->dispatch('operation-type-added', operationTypeId: $operationType->id);

        session()->flash('message', 'Yeni işlem türü başarıyla eklendi.');
    }

    public function editOperationType($operationTypeId)
    {
        $operationType = OperationType::findOrFail($operationTypeId);
        
        $this->editingOperationType = $operationType->id;
        $this->operationTypeForm = [
            'name' => $operationType->name,
            'process' => $operationType->process
        ];
    }

    public function updateOperationType()
    {
        $this->validate([
            'operationTypeForm.name' => 'required|string|max:255',
            'operationTypeForm.process' => 'required|in:surgery,mesotherapy,botox,filler'
        ]);

        $operationType = OperationType::findOrFail($this->editingOperationType);
        $operationType->update([
            'name' => $this->operationTypeForm['name'],
            'process' => $this->operationTypeForm['process']
        ]);

        $this->loadOperationTypes();
        $this->resetOperationTypeForm();

        session()->flash('message', 'İşlem türü başarıyla güncellendi.');
    }

    public function deleteOperationType($operationTypeId)
    {
        $operationType = OperationType::findOrFail($operationTypeId);

        $operationType->delete();
        $this->loadOperationTypes();

        session()->flash('message', 'İşlem türü başarıyla silindi.');
    }

    public function resetOperationTypeForm()
    {
        $this->editingOperationType = null;
        $this->operationTypeForm = ['name' => '', 'process' => null];
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
        // Refresh operation types list
        $this->loadOperationTypes();
        
        // If new operation type id provided, select it and update related state
        if ($operationTypeId) {
            $this->selectedOperationType = $operationTypeId;
            $operationType = OperationType::find($operationTypeId);
            if ($operationType) {
                $this->operationTypeSearch = $operationType->name;
                // Use the correct column 'process' instead of deprecated 'value'
                $this->newOperation['process'] = $operationType->process;
                $this->loadOperationDetails($operationTypeId);
            }
        }
    }
    public function addOperationEntry()
    {
        $this->additionalOperations[] = ['process' => '', 'operation_type_id' => null];
    }

    public function removeOperationEntry($index)
    {
        if (isset($this->additionalOperations[$index])) {
            unset($this->additionalOperations[$index]);
            $this->additionalOperations = array_values($this->additionalOperations);
        }
    }

    // Import modal controls
    public function openImportModal()
    {
        // Yetki kontrolü
        if (!$this->canImport()) {
            session()->flash('error', 'İçe aktarma yetkiniz yok.');
            return;
        }
        
        $this->showImportModal = true;
        $this->importReport = ['success' => 0, 'errors' => []];
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->resetImportForm();
    }

    public function resetImportForm()
    {
        $this->importExcelFile = null;
        $this->importJsonFile = null;
        $this->importReport = ['success' => 0, 'errors' => []];
    }

    // Helpers for import
    private function col(array $row, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                return $row[$k];
            }
        }
        return null;
    }

    private function normalizeProcess($value)
    {
        if (!$value) return null;
        $v = strtolower(trim($value));
        $map = [
            'surgery' => ['surgery','ameliyat'],
            'mesotherapy' => ['mesotherapy','mezoterapi'],
            'botox' => ['botox','botoks'],
            'filler' => ['filler','dolgu']
        ];
        foreach ($map as $key => $aliases) {
            if (in_array($v, $aliases, true)) return $key;
        }
        return null;
    }

    private function resolvePatientFromRow(array $row)
    {
        $user = Auth::user();
        $patientId = $this->col($row, ['patient_id','hasta_id']);
        $tc = $this->col($row, ['tc_identity','patient_tc','tc','tc_kimlik']);

        $query = Patient::query();
        if ($patientId) {
            $query->where('id', $patientId);
        } elseif ($tc && preg_match('/^\d{11}$/', (string)$tc)) {
            $tmp = new Patient();
            $encryptedTc = $tmp->encryptField('tc_identity', (string)$tc);
            $query->where('tc_identity', $encryptedTc);
        } else {
            return null;
        }

        // Doktor bazlı erişim
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif (in_array($user->role, ['secretary','nurse'])) {
            if ($user->doctor_id) $query->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm hastaları görebilir

        return $query->first();
    }

    private function getPatientNameFromRow(array $row)
    {
        // Try direct name fields
        $name = $this->col($row, ['patient_name','hasta_adi_soyadi','adsoyad','full_name','fullname']);
        if ($name) return trim((string)$name);
        $first = $this->col($row, ['first_name','adi','ad']);
        $last = $this->col($row, ['last_name','soyadi','soyad']);
        $combined = trim(((string)$first).' '.((string)$last));
        if (trim($combined) !== '') return $combined;
        // Fallback single-field name
        $single = $this->col($row, ['hasta','name']);
        return $single ? trim((string)$single) : null;
    }

    private function normalizeTypeName(string $name)
    {
        // Lowercase, trim, collapse spaces, remove punctuation for tolerant matching
        $n = preg_replace('/\s+/u', ' ', trim(mb_strtolower($name)));
        $n = preg_replace('/[\p{P}\p{S}]/u', '', $n);
        return $n;
    }

    private function resolveOperationTypeFromRow(?string $process, array $row, ?\App\Models\Patient $patient = null)
    {
        $user = Auth::user();
        // Determine doctor context for matching
        $doctorId = null;
        if ($patient && $patient->doctor_id) {
            $doctorId = $patient->doctor_id;
        } elseif ($user->role === 'doctor') {
            $doctorId = $user->id;
        } elseif (in_array($user->role, ['secretary','nurse'])) {
            $doctorId = $user->doctor_id;
        }

        $typeId = $this->col($row, ['operation_type_id','type_id','islem_tipi_id']);
        $typeName = $this->col($row, ['operation_type_name','type_name','islem_tipi']);

        // Build base query: filter by doctor if available, and by process/value
        $query = OperationType::active()->ordered();
        if ($doctorId) {
            $query->forDoctor($doctorId);
        }
        if ($process) {
            $query->where(function($q) use ($process) {
                $q->where('process', $process);
                if (\Illuminate\Support\Facades\Schema::hasColumn('operation_types', 'value')) {
                    $q->orWhere('value', $process);
                }
            });
        }

        if ($typeId) {
            return (clone $query)->where('id', $typeId)->first();
        }
        if ($typeName) {
            $trimmed = trim((string)$typeName);
            $lowerTrim = mb_strtolower($trimmed);
            // Exact (case-insensitive) match
            $direct = (clone $query)->whereRaw('LOWER(TRIM(name)) = ?', [$lowerTrim])->first();
            if ($direct) return $direct;
            // Loose LIKE match
            $like = (clone $query)->where('name', 'like', '%'.$trimmed.'%')->first();
            if ($like) return $like;
            // Fuzzy matching in memory
            $all = (clone $query)->get();
            $targetNorm = $this->normalizeTypeName($trimmed);
            $best = null; $bestScore = 0;
            foreach ($all as $t) {
                $dbNorm = $this->normalizeTypeName($t->name);
                similar_text($targetNorm, $dbNorm, $percent);
                if ($percent > 92 || $dbNorm === $targetNorm) {
                    if ($percent > $bestScore) { $bestScore = $percent; $best = $t; }
                }
            }
            if ($best) return $best;
        }
        return null;
    }

    private function parseRegistrationPeriod($value)
    {
        if (!$value) {
            return $this->convertToTurkishMonth($this->getCurrentRegistrationPeriod());
        }
        $val = trim((string)$value);

        // YYYY-MM or Y-M style with different separators
        if (preg_match('/^(\d{4})[-\/.](\d{1,2})$/', $val, $m)) {
            $yearMonth = sprintf('%04d-%02d', (int)$m[1], (int)$m[2]);
            return $this->convertToTurkishMonth($yearMonth);
        }
        // MM-YYYY style with different separators
        if (preg_match('/^(\d{1,2})[-\/.](\d{4})$/', $val, $m)) {
            $yearMonth = sprintf('%04d-%02d', (int)$m[2], (int)$m[1]);
            return $this->convertToTurkishMonth($yearMonth);
        }
        // Turkish month name + year (order-insensitive, case-insensitive)
        if (preg_match('/(ocak|şubat|subat|mart|nisan|mayıs|mayis|haziran|temmuz|ağustos|agustos|eylül|eylul|ekim|kasım|kasim|aralık|aralik)/iu', $val, $mm) && preg_match('/(\d{4})/', $val, $yy)) {
            $months = [
                'ocak' => '01','şubat' => '02','subat' => '02','mart' => '03','nisan' => '04',
                'mayıs' => '05','mayis' => '05','haziran' => '06','temmuz' => '07','ağustos' => '08','agustos' => '08',
                'eylül' => '09','eylul' => '09','ekim' => '10','kasım' => '11','kasim' => '11','aralık' => '12','aralik' => '12'
            ];
            $monthName = strtolower($mm[1]);
            $year = (int)$yy[1];
            $month = $months[$monthName] ?? null;
            if ($month) {
                return $this->convertToTurkishMonth(sprintf('%04d-%02d', $year, (int)$month));
            }
        }

        // Fallback: use current period to keep import flowing
        return $this->convertToTurkishMonth($this->getCurrentRegistrationPeriod());
    }

    private function parseProcessDate($value)
    {
        if (!$value) return Carbon::today();
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::today();
        }
    }

    private function importRows(array $rows)
    {
        if (!$this->canCreate()) {
            session()->flash('error', 'Toplu içe aktarma yetkiniz yok.');
            return;
        }

        $success = 0;
        $errors = [];
        $savedRecords = [];

        foreach ($rows as $index => $rawRow) {
            // Ensure associative array
            $row = is_array($rawRow) ? $rawRow : (array)$rawRow;

            $processRaw = $this->col($row, ['process','islem']);
            $process = $this->normalizeProcess($processRaw);
            if (!$process) {
                $errors[] = "Satır " . ($index+1) . ": Geçersiz veya eksik 'process' alanı.";
                continue;
            }

            // Attempt to resolve patient by id or TC
            $patient = $this->resolvePatientFromRow($row);
            $tcRaw = $this->col($row, ['tc_identity','patient_tc','tc','tc_kimlik']);
            $patientName = $this->getPatientNameFromRow($row);

            // If TC provided but no patient found, report a clear error and skip this row
            if ($tcRaw && !$patient) {
                $errors[] = "Satır " . ($index+1) . ": Kayıtlarınızda böyle bir hasta yok (TC: " . $tcRaw . ").";
                continue;
            }

            // Operation type required if column exists in operations
            $requiresType = Schema::hasColumn('operations', 'process_type');
            $typeNameRaw = $this->col($row, ['operation_type_name','type_name','islem_tipi']);
            // Try resolving with patient/doctor context for better matching
            $operationType = $this->resolveOperationTypeFromRow($process, $row, $patient);
            // Missing type will be handled after doctor_id is determined (auto-create if needed)

            $detail = $this->col($row, ['process_detail','detail','aciklama','operation_detail']);
            $registrationPeriodRaw = $this->col($row, ['registration_period','donem']);
            $registrationPeriod = $this->parseRegistrationPeriod($registrationPeriodRaw);
            $processDateRaw = $this->col($row, ['process_date','tarih','date']);
            $processDate = $this->parseProcessDate($processDateRaw);

            try {
                $operationData = [
                    'patient_id' => $patient ? $patient->id : null,
                    'process' => $process,
                    'process_detail' => $detail,
                    'registration_period' => $registrationPeriod,
                    'process_date' => $processDate,
                ];

                // Doktor ID'si ve Created By
                $user = Auth::user();
                if ($patient) {
                    if ($user->role === 'admin' && $patient->doctor_id) {
                        $operationData['doctor_id'] = $patient->doctor_id;
                    } elseif ($user->role === 'doctor') {
                        $operationData['doctor_id'] = $user->id;
                    } elseif (in_array($user->role, ['secretary','nurse']) && $user->doctor_id) {
                        $operationData['doctor_id'] = $user->doctor_id;
                    }
                } else {
                    // Name-only operation: attach current actor's doctor
                    if ($user->role === 'doctor') {
                        $operationData['doctor_id'] = $user->id;
                    } elseif (in_array($user->role, ['secretary','nurse']) && $user->doctor_id) {
                        $operationData['doctor_id'] = $user->doctor_id;
                    } else {
                        // Admin: leave null or set to none
                        $operationData['doctor_id'] = $operationData['doctor_id'] ?? null;
                    }
                }
                // Operations tablosunda created_by zorunlu olduğu için, mevcut kullanıcı ile set edilir
                $operationData['created_by'] = $user->id;

                if ($requiresType) {
                    if (!$operationType && $typeNameRaw) {
                        $createDoctorId = $operationData['doctor_id'] ?? null;
                        if (!$createDoctorId) {
                            if ($user->role === 'doctor') {
                                $createDoctorId = $user->id;
                            } elseif (in_array($user->role, ['secretary','nurse']) && $user->doctor_id) {
                                $createDoctorId = $user->doctor_id;
                            }
                        }
                        // sort_order: aynı process ve doktor için en büyük değerin +1'i
                        $orderQuery = OperationType::query()->where('process', $process);
                        if ($createDoctorId) { $orderQuery->where('created_by', $createDoctorId); }
                        $nextOrder = ((int) $orderQuery->max('sort_order')) + 1;
                        $newTypeData = [
                            'name' => trim((string)$typeNameRaw),
                            'process' => $process,
                            'is_active' => true,
                            'sort_order' => $nextOrder,
                            'created_by' => $createDoctorId
                        ];
                        if (Schema::hasColumn('operation_types', 'value')) {
                            $slug = \Illuminate\Support\Str::slug(trim((string)$typeNameRaw), '_');
                            $baseValue = $process . '_' . $slug;
                            $uniqueValue = $baseValue;
                            $i = 1;
                            while (OperationType::where('value', $uniqueValue)->exists()) {
                                $uniqueValue = $baseValue . '_' . $i;
                                $i++;
                            }
                            $newTypeData['value'] = $uniqueValue;
                        }
                        $operationType = OperationType::create($newTypeData);
                    }

                    if (!$operationType) {
                        $errors[] = "Satır " . ($index+1) . ": İşlem tipi eşleştirilemedi (operation_type_id veya operation_type_name).";
                        continue;
                    }

                    $operationData['process_type'] = $operationType->id;
                }

                // If operations table has patient_name column and we don't have a linked patient, use provided name
                if (Schema::hasColumn('operations', 'patient_name')) {
                    $operationData['patient_name'] = $patient ? ($patient->first_name . ' ' . $patient->last_name) : ($patientName ?: null);
                }

                // If no patient resolved and no name provided, block with a clear error
                if (!$patient && empty($operationData['patient_name'])) {
                    $errors[] = "Satır " . ($index+1) . ": Hasta bilgisi eksik (isim soyisim veya TC gerekli).";
                    continue;
                }

                $operation = Operation::create($operationData);

                // Aktivite kaydı
                Activity::create([
                    'type' => 'operation_added',
                    'description' => 'Toplu içe aktarma ile işlem eklendi: ' . $process,
                    'patient_id' => $patient ? $patient->id : null,
                    'doctor_id' => $operationData['doctor_id'] ?? null
                ]);

                // Build saved record summary for reporting
                $procLabel = $this->getProcessLabel($process);
                $typeLabel = $operationType ? ($operationType->name ?? ('ID ' . $operationType->id)) : null;
                $summary = 'Satır ' . ($index+1) . ': ' . $procLabel . ' - ';
                if ($patient) {
                    $summary .= 'Hasta #' . $patient->id;
                } else {
                    $summary .= 'Hasta: ' . ($operationData['patient_name'] ?? '(adı yok)');
                }
                $summary .= ' - Dönem ' . $registrationPeriod;
                $summary .= ' - Tarih ' . $processDate->format('Y-m-d');
                if ($typeLabel) $summary .= ' - Tip ' . $typeLabel;
                $normalizedDefault = $this->convertToTurkishMonth($this->getCurrentRegistrationPeriod());
                $periodFallbackUsed = !empty($registrationPeriodRaw) && ($registrationPeriod === $normalizedDefault) && (strtolower(trim((string)$registrationPeriodRaw)) !== $normalizedDefault);
                if ($periodFallbackUsed) $summary .= ' (Dönem algılanamadı, otomatik belirlendi)';
                $savedRecords[] = $summary;

                $success++;
            } catch (\Exception $e) {
                $errors[] = "Satır " . ($index+1) . ": Kayıt sırasında hata - " . $e->getMessage();
            }
        }

        $this->importReport = ['success' => $success, 'errors' => $errors, 'saved' => $savedRecords];
        $this->loadOperations();

        if (empty($errors)) {
            session()->flash('message', "Toplu içe aktarma tamamlandı. Başarılı kayıt: {$success}.");
            $this->showImportModal = false;
            $this->resetImportForm();
        } else {
            session()->flash('error', 'Bazı satırlar içe aktarılırken hata oluştu. Ayrıntıları kontrol edin.');
        }
    }

    public function importExcel()
    {
        // Yetki kontrolü
        if (!$this->canImport()) {
            session()->flash('error', 'İçe aktarma yetkiniz yok.');
            return;
        }
        
        $this->validate(['importExcelFile' => 'required|file|mimes:xlsx,xls,csv']);
        try {
            $import = new \App\Imports\OperationsImport();
            Excel::import($import, $this->importExcelFile->getRealPath());
            $rows = $import->rows ? $import->rows->toArray() : [];
            $this->importRows($rows);
        } catch (\Throwable $e) {
            session()->flash('error', 'Excel içe aktarma sırasında hata: ' . $e->getMessage());
        }
    }

    public function importJson()
    {
        // Yetki kontrolü
        if (!$this->canImport()) {
            session()->flash('error', 'İçe aktarma yetkiniz yok.');
            return;
        }
        
        $this->validate(['importJsonFile' => 'required|file|mimes:json,txt']);
        try {
            $content = file_get_contents($this->importJsonFile->getRealPath());
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('JSON formatı geçersiz: ' . json_last_error_msg());
            }

            // Accept either array of rows or object with 'operations' key
            $rows = [];
            if (is_array($data)) {
                $rows = isset($data['operations']) && is_array($data['operations']) ? $data['operations'] : $data;
            }
            if (!is_array($rows)) {
                throw new \RuntimeException('JSON beklenen formatta değil. Bir dizi satır veya {"operations": [...]} olmalı.');
            }

            $this->importRows($rows);
        } catch (\Throwable $e) {
            session()->flash('error', 'JSON içe aktarma sırasında hata: ' . $e->getMessage());
        }
    }

    // CRUD Helper Methods - Workspace Standards
    
    /**
     * Yetki kontrolü - Operasyon oluşturma
     */
    public function canCreate()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor', 'nurse', 'secretary']);
    }
    
    /**
     * Doktor ID belirleme
     */
    private function getDoctorId($user, $patient = null)
    {
        if ($user->role === 'doctor') {
            return $user->id;
        } elseif (in_array($user->role, ['nurse', 'secretary'])) {
            return $user->doctor_id;
        } elseif ($user->role === 'admin') {
            return $patient ? $patient->doctor_id : null;
        }
        
        return null;
    }
    
    /**
     * Aktivite kaydı oluşturma
     */
    private function createActivity($type, $description, $patientId = null, $doctorId = null)
    {
        Activity::create([
            'type' => $type,
            'description' => $description,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId
        ]);
    }
    
    /**
     * Ek operasyonları işleme
     */
    private function processAdditionalOperations($baseOperationData, $patientName, $patientId, $doctorId)
    {
        foreach ($this->additionalOperations as $extra) {
            if (!empty($extra['process']) && !empty($extra['operation_type_id'])) {
                $extraData = $baseOperationData;
                $extraData['process'] = $extra['process'];
                
                if (Schema::hasColumn('operations', 'process_type')) {
                    $extraData['process_type'] = $extra['operation_type_id'];
                }
                if (Schema::hasColumn('operations', 'patient_name')) {
                    $extraData['patient_name'] = $patientName ?: null;
                }
                
                Operation::create($extraData);
                $this->createActivity('operation_added', 'Operasyon eklendi: ' . $extra['process'], $patientId, $doctorId);
            }
        }
    }
}
