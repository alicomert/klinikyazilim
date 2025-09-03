<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\PatientNote;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PatientList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $procedureFilter = 'all';
    public $perPage = 10;
    public $showDetailsModal = false;
    public $selectedPatient = null;
    public $showNotesModal = false;
    public $selectedPatientForNotes = null;
    public $patientNotes = [];
    public $newNote = [
        'title' => '',
        'content' => '',
        'note_type' => 'general',
        'priority' => 'medium',
        'is_private' => false
    ];
    public $editingNote = null;

    protected $paginationTheme = 'tailwind';
    protected $listeners = [
        'patient-added' => 'refreshList', 
        'patient-updated' => 'refreshList',
        'note-saved' => 'refreshNotes',
        'note-deleted' => 'refreshNotes'
    ];

    public function openModal()
    {
        $this->dispatch('open-patient-modal');
    }

    public function showPatientDetails($patientId)
    {
        $this->selectedPatient = Patient::find($patientId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPatient = null;
    }

    public function editPatient($patientId)
    {
        // Hasta düzenleme modalını aç
        $this->dispatch('open-patient-modal', $patientId);
        $this->closeDetailsModal();
    }

    public function scheduleAppointment($patientId)
    {
        // Randevu verme modalını aç
        $this->dispatch('open-appointment-modal', ['patientId' => $patientId]);
        $this->closeDetailsModal();
    }

    public function deletePatient($patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $patient->delete();
            
            $this->dispatch('patient-deleted', [
                'message' => 'Hasta başarıyla silindi.',
                'type' => 'success'
            ]);
            
            $this->refreshList();
        } catch (\Exception $e) {
            $this->dispatch('patient-deleted', [
                'message' => 'Hasta silinirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function refreshList()
    {
        $this->resetPage();
        $this->render();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProcedureFilter()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        return [
            'total_patients' => Patient::count(),
            'new_patients_this_month' => Patient::whereMonth('created_at', now()->month)
                                              ->whereYear('created_at', now()->year)
                                              ->count(),
            'active_treatments' => Patient::where('is_active', true)->count(),
            'today_appointments' => 0 // Bu randevu sistemi eklendiğinde güncellenecek
        ];
    }

    public function render()
    {
        $query = Patient::query();

        // Arama filtresi
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
                
                // TC kimlik numarası şifrelenmiş arama
                if (preg_match('/^[0-9]{11}$/', $this->search)) {
                    $patient = new Patient();
                    $encryptedTc = $patient->encryptField('tc_identity', $this->search);
                    $q->orWhere('tc_identity', $encryptedTc);
                }
            });
        }

        // Durum filtresi
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'completed':
                    $query->where('is_active', false);
                    break;
                case 'waiting':
                    // Beklemede durumu için gelecekte ek alan eklenebilir
                    break;
            }
        }

        // Prosedür filtresi (planned_operation alanına göre)
        if ($this->procedureFilter !== 'all') {
            $query->where('planned_operation', 'like', '%' . $this->procedureFilter . '%');
        }

        $patients = $query->orderBy('created_at', 'desc')
                         ->paginate($this->perPage);

        return view('livewire.patient-list', [
            'patients' => $patients,
            'stats' => $this->stats
        ]);
    }

    public function calculateAge($birthDate)
    {
        if (!$birthDate) return 'Bilinmiyor';
        
        try {
            return Carbon::parse($birthDate)->age;
        } catch (\Exception $e) {
            return 'Bilinmiyor';
        }
    }

    public function getGenderText($patient)
    {
        // Gender alanı olmadığı için sadece yaş bilgisi döndürüyoruz
        return '';
    }

    public function getStatusBadge($patient)
    {
        if ($patient->is_active) {
            return [
                'class' => 'bg-yellow-100 text-yellow-800',
                'text' => 'Tedavi'
            ];
        } else {
            return [
                'class' => 'bg-green-100 text-green-800',
                'text' => 'Tamamlandı'
            ];
        }
    }

    public function getProcedureBadge($procedure)
    {
        if (!$procedure) {
            return [
                'class' => 'bg-gray-100 text-gray-800',
                'text' => 'Belirtilmemiş'
            ];
        }

        // Prosedür tipine göre renk belirleme
        $colors = [
            'burun' => 'bg-blue-100 text-blue-800',
            'botoks' => 'bg-purple-100 text-purple-800',
            'dolgu' => 'bg-pink-100 text-pink-800',
            'germe' => 'bg-green-100 text-green-800',
        ];

        $procedureLower = strtolower($procedure);
        $colorClass = 'bg-gray-100 text-gray-800';

        foreach ($colors as $keyword => $class) {
            if (strpos($procedureLower, $keyword) !== false) {
                $colorClass = $class;
                break;
            }
        }

        return [
            'class' => $colorClass,
            'text' => $procedure
        ];
    }

    public function formatDate($date)
    {
        if (!$date) return '-';
        
        try {
            return Carbon::parse($date)->format('d.m.Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function maskTcIdentity($tc)
    {
        if (!$tc || strlen($tc) < 11) return $tc;
        
        return substr($tc, 0, 3) . '*****' . substr($tc, -3);
    }

    public function formatPhone($phone)
    {
        if (!$phone) return '-';
        
        // Telefon numarasını formatla (0532 123 45 67)
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($cleaned) === 11 && $cleaned[0] === '0') {
            return substr($cleaned, 0, 4) . ' ' . substr($cleaned, 4, 3) . ' ' . substr($cleaned, 7, 2) . ' ' . substr($cleaned, 9, 2);
        }
        
        return $phone;
    }

    // Notes Methods
    public function showNotes($patientId)
    {
        $this->selectedPatientForNotes = Patient::find($patientId);
        $this->loadPatientNotes($patientId);
        $this->showNotesModal = true;
        $this->resetNoteForm();
    }

    public function closeNotesModal()
    {
        $this->showNotesModal = false;
        $this->selectedPatientForNotes = null;
        $this->patientNotes = [];
        $this->resetNoteForm();
        $this->editingNote = null;
    }

    public function loadPatientNotes($patientId)
    {
        $user = Auth::user();
        
        $this->patientNotes = PatientNote::where('patient_id', $patientId)
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
            $note = PatientNote::find($this->editingNote);
            
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
            PatientNote::create([
                'patient_id' => $this->selectedPatientForNotes->id,
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

        $this->loadPatientNotes($this->selectedPatientForNotes->id);
        $this->resetNoteForm();
        $this->editingNote = null;
    }

    public function editNote($noteId)
    {
        $note = PatientNote::find($noteId);
        
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
        $note = PatientNote::find($noteId);
        
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
        
        $this->loadPatientNotes($this->selectedPatientForNotes->id);
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

    public function refreshNotes()
    {
        if ($this->selectedPatientForNotes) {
            $this->loadPatientNotes($this->selectedPatientForNotes->id);
        }
    }



    public function getNoteTypeIcon($type)
    {
        return match($type) {
            'medical' => 'fas fa-stethoscope',
            'appointment' => 'fas fa-calendar',
            'treatment' => 'fas fa-pills',
            'general' => 'fas fa-sticky-note',
            default => 'fas fa-sticky-note'
        };
    }

    public function getNoteTypeText($type)
    {
        return match($type) {
            'medical' => 'Tıbbi',
            'appointment' => 'Randevu',
            'treatment' => 'Tedavi',
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
}