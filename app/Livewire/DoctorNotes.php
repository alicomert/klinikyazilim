<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DoctorNote;
use App\Models\PatientNote;
use App\Models\OperationNote;
use App\Models\AppointmentNote;
use App\Models\Patient;
use App\Models\Operation;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class DoctorNotes extends Component
{
    // Modal kontrolü
    public $showModal = false;
    public $showNoteModal = false;
    public $selectedNote = null;
    
    // Form verileri
    public $newNote = [];
    public $editingNote = null;
    
    // Tab kontrolü
    public $activeTab = 'my_notes'; // my_notes, team_notes
    public $activeNotesTab = 'my_notes'; // Yeni tab sistemi için
    
    // Not türü seçimi
    public $noteType = 'doctor'; // doctor, patient, operation, appointment
    
    // Bağlantılı kayıt seçimi
    public $selectedPatient = null;
    public $selectedOperation = null;
    public $selectedAppointment = null;
    
    // Arama ve filtreleme
    public $search = '';
    public $filterType = 'all';
    public $filterPrivacy = 'all';
    
    // Validation rules
    protected $rules = [
        'newNote.title' => 'nullable|string|max:255',
        'newNote.content' => 'required|string',
        'newNote.note_type' => 'required|string',
        'newNote.is_private' => 'boolean',
        'selectedPatient' => 'nullable|exists:patients,id',
        'selectedOperation' => 'nullable|exists:operations,id',
        'selectedAppointment' => 'nullable|exists:appointments,id'
    ];
    
    protected $validationAttributes = [
        'newNote.content' => 'not içeriği',
        'newNote.note_type' => 'not türü'
    ];
    
    public function mount()
    {
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->newNote = [
            'title' => '',
            'content' => '',
            'note_type' => 'general',
            'is_private' => false
        ];
        $this->editingNote = null;
        $this->noteType = 'doctor';
        $this->selectedPatient = null;
        $this->selectedOperation = null;
        $this->selectedAppointment = null;
    }
    
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function openNoteModal($noteId, $type = null)
    {
        // Eğer type belirtilmemişse, note ID'den türü belirle
        if (!$type) {
            $type = $this->determineNoteType($noteId);
        }
        
        $this->selectedNote = $this->getNoteById($noteId, $type);
        $this->showNoteModal = true;
    }
    
    private function determineNoteType($noteId)
    {
        // Önce doctor notes'ta ara
        if (DoctorNote::find($noteId)) {
            return 'doctor';
        }
        
        // Patient notes'ta ara
        if (PatientNote::find($noteId)) {
            return 'patient';
        }
        
        // Operation notes'ta ara
        if (OperationNote::find($noteId)) {
            return 'operation';
        }
        
        // Appointment notes'ta ara
        if (AppointmentNote::find($noteId)) {
            return 'appointment';
        }
        
        // Varsayılan olarak doctor
        return 'doctor';
    }
    
    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->selectedNote = null;
    }
    
    private function getNoteById($noteId, $type)
    {
        switch ($type) {
            case 'doctor':
                return DoctorNote::with('user')->find($noteId);
            case 'patient':
                return PatientNote::with(['user', 'patient'])->find($noteId);
            case 'operation':
                return OperationNote::with(['user', 'operation.patient'])->find($noteId);
            case 'appointment':
                return AppointmentNote::with(['user', 'appointment.patient'])->find($noteId);
            default:
                return null;
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            $user = auth()->user();
            $doctorId = $user->getDoctorIdForFiltering();
            
            $baseData = [
                'user_id' => $user->id,
                'content' => $this->newNote['content'],
                'note_type' => $this->newNote['note_type'],
                'is_private' => $this->newNote['is_private'],
                'note_date' => now(),
                'last_updated' => now()
            ];
            
            switch ($this->noteType) {
                case 'doctor':
                    DoctorNote::create(array_merge($baseData, [
                        'doctor_id' => $doctorId,
                        'title' => $this->newNote['title']
                    ]));
                    break;
                    
                case 'patient':
                    if (!$this->selectedPatient) {
                        session()->flash('error', 'Lütfen bir hasta seçin.');
                        return;
                    }
                    PatientNote::create(array_merge($baseData, [
                        'patient_id' => $this->selectedPatient
                    ]));
                    break;
                    
                case 'operation':
                    if (!$this->selectedOperation) {
                        session()->flash('error', 'Lütfen bir operasyon seçin.');
                        return;
                    }
                    OperationNote::create(array_merge($baseData, [
                        'operation_id' => $this->selectedOperation,
                        'doctor_id' => $doctorId
                    ]));
                    break;
                    
                case 'appointment':
                    if (!$this->selectedAppointment) {
                        session()->flash('error', 'Lütfen bir randevu seçin.');
                        return;
                    }
                    AppointmentNote::create(array_merge($baseData, [
                        'appointment_id' => $this->selectedAppointment,
                        'doctor_id' => $doctorId
                    ]));
                    break;
            }
            
            session()->flash('message', 'Not başarıyla eklendi.');
            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function edit($noteId, $type = null)
    {
        // Eğer type belirtilmemişse, note ID'den türü belirle
        if (!$type) {
            $type = $this->determineNoteType($noteId);
        }
        
        $note = $this->getNoteById($noteId, $type);
        
        if (!$note || !$this->canEdit($note)) {
            session()->flash('error', 'Bu notu düzenleme yetkiniz yok.');
            return;
        }
        
        $this->editingNote = $noteId;
        $this->noteType = $type;
        
        $this->newNote = [
            'title' => $note->title ?? '',
            'content' => $note->content,
            'note_type' => $note->note_type,
            'is_private' => $note->is_private
        ];
        
        // Bağlantılı kayıtları set et
        if ($type === 'patient') {
            $this->selectedPatient = $note->patient_id;
        } elseif ($type === 'operation') {
            $this->selectedOperation = $note->operation_id;
        } elseif ($type === 'appointment') {
            $this->selectedAppointment = $note->appointment_id;
        }
        
        $this->showModal = true;
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $note = $this->getNoteById($this->editingNote, $this->noteType);
            
            if (!$note || !$this->canEdit($note)) {
                session()->flash('error', 'Bu notu düzenleme yetkiniz yok.');
                return;
            }
            
            $updateData = [
                'content' => $this->newNote['content'],
                'note_type' => $this->newNote['note_type'],
                'is_private' => $this->newNote['is_private'],
                'last_updated' => now()
            ];
            
            if ($this->noteType === 'doctor') {
                $updateData['title'] = $this->newNote['title'];
            }
            
            $note->update($updateData);
            
            session()->flash('message', 'Not başarıyla güncellendi.');
            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function delete($noteId, $type = null)
    {
        try {
            // Eğer type belirtilmemişse, note ID'den türü belirle
            if (!$type) {
                $type = $this->determineNoteType($noteId);
            }
            
            $note = $this->getNoteById($noteId, $type);
            
            if (!$note || !$this->canDelete($note)) {
                session()->flash('error', 'Bu notu silme yetkiniz yok.');
                return;
            }
            
            $note->delete();
            session()->flash('message', 'Not başarıyla silindi.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }
    }
    
    private function canEdit($note)
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        return $note->user_id === $user->id;
    }
    
    private function canDelete($note)
    {
        return $this->canEdit($note);
    }
    
    public function getMyNotesProperty()
    {
        $user = auth()->user();
        $doctorId = $user->getDoctorIdForFiltering();
        
        $notes = collect();
        
        // Doktor notları
        $doctorNotes = DoctorNote::with('user')
            ->where('user_id', $user->id)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType !== 'all', function($query) {
                $query->where('note_type', $this->filterType);
            })
            ->when($this->filterPrivacy !== 'all', function($query) {
                $query->where('is_private', $this->filterPrivacy === 'private');
            })
            ->orderBy('note_date', 'desc')
            ->get()
            ->map(function($note) {
                $note->type = 'doctor';
                $note->related_info = 'Genel Not';
                return $note;
            });
        
        // Hasta notları
        $patientNotes = PatientNote::with(['user', 'patient'])
            ->where('user_id', $user->id)
            ->when($this->search, function($query) {
                $query->where('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType !== 'all', function($query) {
                $query->where('note_type', $this->filterType);
            })
            ->when($this->filterPrivacy !== 'all', function($query) {
                $query->where('is_private', $this->filterPrivacy === 'private');
            })
            ->orderBy('note_date', 'desc')
            ->get()
            ->map(function($note) {
                $note->type = 'patient';
                $note->related_info = 'Hasta: ' . ($note->patient ? $note->patient->first_name . ' ' . $note->patient->last_name : 'Bilinmeyen');
                return $note;
            });
        
        // Operasyon notları
        $operationNotes = OperationNote::with(['user', 'operation.patient'])
            ->where('user_id', $user->id)
            ->when($this->search, function($query) {
                $query->where('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType !== 'all', function($query) {
                $query->where('note_type', $this->filterType);
            })
            ->when($this->filterPrivacy !== 'all', function($query) {
                $query->where('is_private', $this->filterPrivacy === 'private');
            })
            ->orderBy('note_date', 'desc')
            ->get()
            ->map(function($note) {
                $note->type = 'operation';
                $patientName = $note->operation && $note->operation->patient ? 
                    $note->operation->patient->first_name . ' ' . $note->operation->patient->last_name : 'Bilinmeyen';
                $operationType = $note->operation ? $note->operation->process : 'Bilinmeyen';
                $note->related_info = 'Operasyon: ' . $patientName . ' - ' . $operationType;
                return $note;
            });
        
        // Randevu notları
        $appointmentNotes = AppointmentNote::with(['user', 'appointment.patient'])
            ->where('user_id', $user->id)
            ->when($this->search, function($query) {
                $query->where('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterType !== 'all', function($query) {
                $query->where('note_type', $this->filterType);
            })
            ->when($this->filterPrivacy !== 'all', function($query) {
                $query->where('is_private', $this->filterPrivacy === 'private');
            })
            ->orderBy('note_date', 'desc')
            ->get()
            ->map(function($note) {
                $note->type = 'appointment';
                if ($note->appointment) {
                    $appointmentDate = $note->appointment->appointment_date ? 
                        $note->appointment->appointment_date->format('d.m.Y') : '';
                    $appointmentTime = $note->appointment->appointment_time ? 
                        \Carbon\Carbon::parse($note->appointment->appointment_time)->format('H:i') : '';
                    $dateTimeStr = trim($appointmentDate . ' ' . $appointmentTime);
                    $note->related_info = 'Randevu: ' . $dateTimeStr;
                } else {
                    $note->related_info = 'Randevu: Bilinmeyen';
                }
                return $note;
            });
        
        return $notes->concat($doctorNotes)
                    ->concat($patientNotes)
                    ->concat($operationNotes)
                    ->concat($appointmentNotes)
                    ->sortByDesc('note_date')
                    ->values();
    }
    
    public function getTeamNotesProperty()
    {
        $user = auth()->user();
        $doctorId = $user->getDoctorIdForFiltering();
        
        $notes = collect();
        
        // Hemşire ve sekreter notları
        $teamUserIds = User::where('doctor_id', $doctorId)
                          ->whereIn('role', ['nurse', 'secretary'])
                          ->pluck('id');
        
        if ($teamUserIds->isNotEmpty()) {
            // Doktor notları
            $doctorNotes = DoctorNote::with('user')
                ->whereIn('user_id', $teamUserIds)
                ->where('is_private', false) // Sadece public notlar
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->filterType !== 'all', function($query) {
                    $query->where('note_type', $this->filterType);
                })
                ->when($this->filterPrivacy !== 'all', function($query) {
                    $query->where('is_private', $this->filterPrivacy === 'private');
                })
                ->orderBy('note_date', 'desc')
                ->get()
                ->map(function($note) {
                    $note->type = 'doctor';
                    $note->related_info = 'Genel Not';
                    return $note;
                });
            
            // Hasta notları
            $patientNotes = PatientNote::with(['user', 'patient'])
                ->whereIn('user_id', $teamUserIds)
                ->where('is_private', false)
                ->when($this->search, function($query) {
                    $query->where('content', 'like', '%' . $this->search . '%');
                })
                ->when($this->filterType !== 'all', function($query) {
                    $query->where('note_type', $this->filterType);
                })
                ->when($this->filterPrivacy !== 'all', function($query) {
                    $query->where('is_private', $this->filterPrivacy === 'private');
                })
                ->orderBy('note_date', 'desc')
                ->get()
                ->map(function($note) {
                    $note->type = 'patient';
                    if ($note->patient) {
                        $note->related_info = 'Hasta: ' . $note->patient->first_name . ' ' . $note->patient->last_name;
                    } else {
                        $note->related_info = 'Hasta: Bilinmeyen';
                    }
                    return $note;
                });
            
            // Operasyon notları
            $operationNotes = OperationNote::with(['user', 'operation.patient'])
                ->whereIn('user_id', $teamUserIds)
                ->where('is_private', false)
                ->when($this->search, function($query) {
                    $query->where('content', 'like', '%' . $this->search . '%');
                })
                ->when($this->filterType !== 'all', function($query) {
                    $query->where('note_type', $this->filterType);
                })
                ->when($this->filterPrivacy !== 'all', function($query) {
                    $query->where('is_private', $this->filterPrivacy === 'private');
                })
                ->orderBy('note_date', 'desc')
                ->get()
                ->map(function($note) {
                    $note->type = 'operation';
                    if ($note->operation) {
                        $patientName = '';
                        if ($note->operation->patient) {
                            $patientName = $note->operation->patient->first_name . ' ' . $note->operation->patient->last_name . ' - ';
                        }
                        $processName = $note->operation->process ?? $note->operation->operation_type ?? 'Bilinmeyen';
                        $note->related_info = 'Operasyon: ' . $patientName . $processName;
                    } else {
                        $note->related_info = 'Operasyon: Bilinmeyen';
                    }
                    return $note;
                });
            
            // Randevu notları
            $appointmentNotes = AppointmentNote::with(['user', 'appointment.patient'])
                ->whereIn('user_id', $teamUserIds)
                ->where('is_private', false)
                ->when($this->search, function($query) {
                    $query->where('content', 'like', '%' . $this->search . '%');
                })
                ->when($this->filterType !== 'all', function($query) {
                    $query->where('note_type', $this->filterType);
                })
                ->when($this->filterPrivacy !== 'all', function($query) {
                    $query->where('is_private', $this->filterPrivacy === 'private');
                })
                ->orderBy('note_date', 'desc')
                ->get()
                ->map(function($note) {
                    $note->type = 'appointment';
                    if ($note->appointment) {
                        $appointmentDate = $note->appointment->appointment_date ? 
                            $note->appointment->appointment_date->format('d.m.Y') : '';
                        $appointmentTime = $note->appointment->appointment_time ? 
                            \Carbon\Carbon::parse($note->appointment->appointment_time)->format('H:i') : '';
                        $dateTimeStr = trim($appointmentDate . ' ' . $appointmentTime);
                        $note->related_info = 'Randevu: ' . $dateTimeStr;
                    } else {
                        $note->related_info = 'Randevu: Bilinmeyen';
                    }
                    return $note;
                });
            
            $notes = $notes->concat($doctorNotes)
                          ->concat($patientNotes)
                          ->concat($operationNotes)
                          ->concat($appointmentNotes);
        }
        
        return $notes->sortByDesc('note_date')->values();
    }
    
    public function getCurrentNotesProperty()
    {
        if ($this->activeNotesTab === 'my_notes') {
            return $this->myNotes;
        } else {
            return $this->teamNotes;
        }
    }
    
    public function switchNotesTab($tab)
    {
        $this->activeNotesTab = $tab;
    }
    
    public function getPatientsProperty()
    {
        $user = auth()->user();
        $doctorId = $user->getDoctorIdForFiltering();
        
        return Patient::where('doctor_id', $doctorId)
                     ->orderBy('first_name')
                     ->orderBy('last_name')
                     ->get();
    }
    
    public function getOperationsProperty()
    {
        $user = auth()->user();
        $doctorId = $user->getDoctorIdForFiltering();
        
        return Operation::with('patient')
                       ->where('doctor_id', $doctorId)
                       ->orderBy('process_date', 'desc')
                       ->get();
    }
    
    public function getAppointmentsProperty()
    {
        $user = auth()->user();
        $doctorId = $user->getDoctorIdForFiltering();
        
        return Appointment::with('patient')
                         ->where('doctor_id', $doctorId)
                         ->orderBy('appointment_date', 'desc')
                         ->get();
    }
    
    // Helper methods for note styling
    public function getNoteColor($noteType)
    {
        $colors = [
            'general' => 'from-yellow-200 to-yellow-300',
            'medical' => 'from-blue-200 to-blue-300',
            'reminder' => 'from-orange-200 to-orange-300',
            'important' => 'from-red-200 to-red-300',
            'follow_up' => 'from-green-200 to-green-300'
        ];
        
        return $colors[$noteType] ?? $colors['general'];
    }
    
    public function getNoteColorDot($noteType)
    {
        $colors = [
            'general' => 'bg-yellow-400',
            'medical' => 'bg-blue-400',
            'reminder' => 'bg-orange-400',
            'important' => 'bg-red-400',
            'follow_up' => 'bg-green-400'
        ];
        
        return $colors[$noteType] ?? $colors['general'];
    }
    
    public function getNoteTypeLabel($noteType)
    {
        $labels = [
            'general' => 'Genel',
            'medical' => 'Tıbbi',
            'reminder' => 'Hatırlatma',
            'important' => 'Önemli',
            'follow_up' => 'Takip'
        ];
        
        return $labels[$noteType] ?? 'Genel';
    }

    public function render()
    {
        return view('livewire.doctor-notes', [
            'currentNotes' => $this->currentNotes,
            'myNotes' => $this->myNotes,
            'teamNotes' => $this->teamNotes,
            'patients' => $this->patients,
            'operations' => $this->operations,
            'appointments' => $this->appointments
        ]);
    }
}
