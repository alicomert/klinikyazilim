<?php

namespace App\Livewire;

use App\Models\Operation;
use App\Models\OperationNote;
use App\Models\Patient;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OperationList extends Component
{
    use WithPagination;

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

    // Validation rules
    protected $rules = [
        'newOperation.patient_id' => 'required|exists:patients,id',
        'newOperation.process' => 'required|in:surgery,mesotherapy,botox,filler',
        'newOperation.process_detail' => 'required|string',
        'newOperation.registration_period' => 'required|string'
    ];

    protected $messages = [
        'newOperation.patient_id.required' => 'Hasta seçimi zorunludur.',
        'newOperation.patient_id.exists' => 'Seçilen hasta bulunamadı.',
        'newOperation.process.required' => 'İşlem türü seçimi zorunludur.',
        'newOperation.process.in' => 'Geçersiz işlem türü.',
        'newOperation.process_detail.required' => 'İşlem detayı zorunludur.',
        'newOperation.registration_period.required' => 'Kayıt dönemi zorunludur.'
    ];

    public function mount()
    {
        $this->loadOperations();
        $this->loadPatients();
        $this->resetForm();
        $this->currentRegistrationPeriod = $this->getCurrentRegistrationPeriod();
        $this->filteredPatients = []; // Başlangıçta boş liste
    }

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
            $this->filteredPatients = $this->patients;
        } else {
            $this->filteredPatients = collect($this->patients)->filter(function ($patient) {
                return stripos($patient->first_name . ' ' . $patient->last_name, $this->patientSearch) !== false ||
                       stripos($patient->tc_identity, $this->patientSearch) !== false;
            })->values()->all();
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
        return Operation::where('process', 'botox')->count();
    }

    public function getFillerCountProperty()
    {
        return Operation::where('process', 'filler')->count();
    }

    public function getTotalOperationsProperty()
    {
        return Operation::count();
    }

    public function getTodayOperationsProperty()
    {
        return Operation::whereDate('process_date', Carbon::today())->count();
    }

    public function getThisMonthOperationsProperty()
    {
        return Operation::whereMonth('process_date', Carbon::now()->month)
                       ->whereYear('process_date', Carbon::now()->year)
                       ->count();
    }

    public function getStatsProperty()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $previousYear = $currentYear - 1;
        
        // Bu yıl toplam operasyonlar
        $thisYearOperations = Operation::whereYear('process_date', $currentYear)->count();
        
        // Bu ay operasyonlar
        $thisMonthOperations = Operation::whereMonth('process_date', $currentMonth)
                                        ->whereYear('process_date', $currentYear)
                                        ->count();
        
        // Toplam operasyonlar
        $totalOperations = Operation::count();
        
        // Geçen yıl aynı dönem karşılaştırması
        $previousYearSameMonth = Operation::whereMonth('process_date', $currentMonth)
                                          ->whereYear('process_date', $previousYear)
                                          ->count();
        
        $previousYearTotal = Operation::whereYear('process_date', $previousYear)->count();
        
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
            'monthly_percentage_change' => round($monthlyPercentageChange, 1),
            'yearly_percentage_change' => round($yearlyPercentageChange, 1),
            'has_previous_year_data' => $previousYearTotal > 0
        ];
    }

    public function loadOperations()
    {
        $query = Operation::with(['patient', 'creator'])
            ->when($this->searchTerm, function($q) {
                $q->whereHas('patient', function($patientQuery) {
                    $patientQuery->where('first_name', 'like', '%' . $this->searchTerm . '%')
                                ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%')
                                ->orWhere('tc_identity', 'like', '%' . $this->searchTerm . '%');
                })
                ->orWhere('process_detail', 'like', '%' . $this->searchTerm . '%');
            })

            ->when($this->filterProcess, function($q) {
                $q->where('process', $this->filterProcess);
            })
            ->when($this->filterRegistrationPeriod, function($q) {
                $q->where('registration_period', $this->filterRegistrationPeriod);
            });

        // Yetki kontrolü
        if (auth()->user()->role !== 'admin') {
            $query->where('created_by', auth()->id());
        }

        $this->operations = $query->orderBy('process_date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();
    }

    public function loadPatients()
    {
        $this->patients = Patient::orderBy('first_name')
                                ->orderBy('last_name')
                                ->get();
    }

    public function create()
    {
        $this->validate();

        Operation::create([
            'patient_id' => $this->newOperation['patient_id'],
            'process' => $this->newOperation['process'],
            'process_detail' => $this->newOperation['process_detail'],
            'process_date' => Carbon::today(),
            'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period']),
            'created_by' => auth()->id()
        ]);

        $this->closeModal();
        $this->loadOperations();

        session()->flash('message', 'Operasyon başarıyla eklendi.');
    }

    public function edit($operationId)
    {
        $operation = Operation::findOrFail($operationId);

        if (!$this->canEdit($operation)) {
            session()->flash('error', 'Bu operasyonu düzenleme yetkiniz yok.');
            return;
        }

        $this->editingOperation = $operation->id;
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
        $this->validate();

        $operation = Operation::findOrFail($this->editingOperation);

        if (!$this->canEdit($operation)) {
            session()->flash('error', 'Bu operasyonu düzenleme yetkiniz yok.');
            return;
        }

        $operation->update([
            'patient_id' => $this->newOperation['patient_id'],
            'process' => $this->newOperation['process'],
            'process_detail' => $this->newOperation['process_detail'],
            'registration_period' => $this->convertToTurkishMonth($this->newOperation['registration_period'])
        ]);

        $this->closeModal();
        $this->loadOperations();

        session()->flash('message', 'Operasyon başarıyla güncellendi.');
    }

    public function delete($operationId)
    {
        $operation = Operation::findOrFail($operationId);

        if (!$this->canDelete($operation)) {
            session()->flash('error', 'Bu operasyonu silme yetkiniz yok.');
            return;
        }

        $operation->delete();
        $this->loadOperations();

        session()->flash('message', 'Operasyon başarıyla silindi.');
    }



    public function resetForm()
    {
        $this->newOperation = [
            'patient_id' => '',
            'process' => '',
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

        if ($user->role === 'admin') {
            return true;
        }

        return $operation->created_by === $user->id;
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
        
        $this->operationNotes = OperationNote::where('operation_id', $operationId)
            ->with('user')
            ->where(function($query) use ($user) {
                // Public notları göster
                $query->where('is_private', false)
                    // Veya kullanıcının kendi private notlarını göster
                    ->orWhere(function($subQuery) use ($user) {
                        $subQuery->where('is_private', true)
                                ->where('user_id', $user->id);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
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

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Not başarıyla güncellendi.'
            ]);
        } else {
            // Yeni not ekleme
            OperationNote::create([
                'operation_id' => $this->selectedOperationForNotes->id,
                'user_id' => Auth::id(),
                'content' => $this->newNote['content'],
                'note_type' => $this->newNote['note_type'],
                'is_private' => $this->newNote['is_private'],
                'note_date' => now(),
                'last_updated' => now()
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
}
