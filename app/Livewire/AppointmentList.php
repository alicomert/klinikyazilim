<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\AppointmentNote;
use App\Models\Patient;
use App\Models\Activity;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingAppointment = null;
    public $selectedDate = null;
    public $selectedTime = null;
    
    // View mode
    public $viewMode = 'weekly'; // weekly, monthly
    public $currentWeek = null;
    public $currentMonth = null;
    public $currentYear = null;
    
    // Form fields
    public $patient_id = null;
    public $patient_name = '';
    public $patient_phone = '';
    public $appointment_date = '';
    public $appointment_time = '';
    public $appointment_hour = '';
    public $appointment_minute = '';
    public $appointment_type = 'control';
    public $notes = '';
    public $status = 'scheduled';
    
    // Search and filter
    public $search = '';
    public $patientSearch = '';
    public $filterDate = '';
    public $filterStatus = '';
    public $filterType = '';
    
    // Drag and drop
    public $draggedAppointment = null;
    public $selectedPatient = null;
    public $searchResults = [];
    public $searchedPatients = [];
    
    // Bulk operations
    public $selectedAppointments = [];
    public $selectAll = false;
    public $bulkStatus = '';
    
    // Delete confirmation
    public $showDeleteModal = false;
    public $appointmentToDelete = null;
    
    // Notes
    public $showNotesModal = false;
    public $selectedAppointmentForNotes = null;
    public $appointmentNotes = [];
    public $newNote = [
        'content' => '',
        'note_type' => 'general',
        'is_private' => false
    ];
    public $editingNote = null;
    
    protected $rules = [
        'appointment_date' => 'required|date',
        'appointment_time' => 'required',
        'appointment_type' => 'required|in:consultation,operation,control,botox,filler',
        'status' => 'required|in:scheduled,completed,cancelled,no_show',
    ];
    
    protected $messages = [
        'appointment_date.required' => 'Randevu tarihi gereklidir.',
        'appointment_time.required' => 'Randevu saati gereklidir.',
        'appointment_type.required' => 'Randevu türü gereklidir.',
    ];

    public function mount($date = null, $time = null)
    {
        if ($date) {
            $this->selectedDate = $date;
            $this->appointment_date = $date;
        }
        if ($time) {
            $this->selectedTime = $time;
            $this->appointment_time = $time;
        }
        
        $this->currentWeek = now()->startOfWeek();
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->filterDate = now()->format('Y-m-d');
        $this->appointment_type = 'control'; // Varsayılan olarak Kontrol seçili
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        if ($mode === 'weekly') {
            $this->currentWeek = now()->startOfWeek();
        } else {
            $this->currentMonth = now()->month;
            $this->currentYear = now()->year;
        }
    }
    
    public function updatedViewMode($value)
    {
        $this->changeViewMode($value);
    }
    
    // Bulk operations methods
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Mevcut sayfadaki tüm randevuları seç
            $appointments = $this->getAppointmentsProperty();
            $this->selectedAppointments = $appointments->pluck('id')->toArray();
        } else {
            $this->selectedAppointments = [];
        }
    }
    
    public function updatedSelectedAppointments()
    {
        // Tüm randevular seçiliyse selectAll'ı true yap
        $appointments = $this->getAppointmentsProperty();
        $totalAppointments = $appointments->count();
        $selectedCount = count($this->selectedAppointments);
        
        $this->selectAll = $totalAppointments > 0 && $selectedCount === $totalAppointments;
    }
    
    public function updateAppointmentStatus($appointmentId, $status)
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);
        $appointment->update(['status' => $status]);
        
        // Activity log
        $patientName = $appointment->patient ? $appointment->patient->name : 'Bilinmeyen Hasta';
        
        Activity::create([
            'type' => 'appointment_status_updated',
            'description' => "Randevu durumu güncellendi: {$patientName} - {$status}",
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $this->getDoctorIdForFiltering()
        ]);
        
        session()->flash('message', 'Randevu durumu güncellendi.');
    }
    
    public function updateBulkStatus()
    {
        if (empty($this->selectedAppointments) || empty($this->bulkStatus)) {
            session()->flash('error', 'Lütfen randevu seçin ve durum belirleyin.');
            return;
        }
        
        $appointments = Appointment::with('patient')->whereIn('id', $this->selectedAppointments)->get();
        
        foreach ($appointments as $appointment) {
            $appointment->update(['status' => $this->bulkStatus]);
            
            // Activity log
            $patientName = $appointment->patient ? $appointment->patient->first_name . ' ' . $appointment->patient->last_name : $appointment->patient_name;
            $user = Auth::user();
            $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
            
            Activity::create([
                'type' => 'appointment_status_bulk_updated',
                'description' => "Toplu randevu durumu güncellendi: {$patientName} - {$this->bulkStatus}",
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $doctorId
            ]);
        }
        
        $this->clearSelection();
        session()->flash('message', count($appointments) . ' randevunun durumu güncellendi.');
    }
    
    public function clearSelection()
    {
        $this->selectedAppointments = [];
        $this->selectAll = false;
        $this->bulkStatus = '';
    }
    

    public function confirmDelete($appointmentId)
    {
        $this->appointmentToDelete = $appointmentId;
        $this->showDeleteModal = true;
    }
    
    public function deleteAppointment()
    {
        if ($this->appointmentToDelete) {
            $appointment = Appointment::with('patient')->findOrFail($this->appointmentToDelete);
            
            // Hasta adını güvenli şekilde al
            $patientName = $appointment->patient ? $appointment->patient->first_name . ' ' . $appointment->patient->last_name : $appointment->patient_name;
            
            // Activity log
            Activity::create([
                'type' => 'appointment_deleted',
                'description' => "Randevu silindi: {$patientName} - {$appointment->appointment_date} {$appointment->appointment_time}",
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $this->getDoctorIdForFiltering()
            ]);
            
            $appointment->delete();
            
            $this->showDeleteModal = false;
            $this->appointmentToDelete = null;
            
            session()->flash('message', 'Randevu başarıyla silindi.');
        }
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->appointmentToDelete = null;
    }
    
    public function previousPeriod()
    {
        if ($this->viewMode === 'weekly') {
            $this->currentWeek = Carbon::parse($this->currentWeek)->subWeek();
        } else {
            if ($this->currentMonth == 1) {
                $this->currentMonth = 12;
                $this->currentYear--;
            } else {
                $this->currentMonth--;
            }
        }
    }
    
    public function nextPeriod()
    {
        if ($this->viewMode === 'weekly') {
            $this->currentWeek = Carbon::parse($this->currentWeek)->addWeek();
        } else {
            if ($this->currentMonth == 12) {
                $this->currentMonth = 1;
                $this->currentYear++;
            } else {
                $this->currentMonth++;
            }
        }
    }
    
    public function goToToday()
    {
        if ($this->viewMode === 'weekly') {
            $this->currentWeek = now()->startOfWeek();
        } else {
            $this->currentMonth = now()->month;
            $this->currentYear = now()->year;
        }
    }
    
    public function getAppointmentTypeText($type)
    {
        $types = [
            'consultation' => 'Konsültasyon',
            'operation' => 'Operasyon',
            'control' => 'Kontrol',
            'botox' => 'Botoks',
            'filler' => 'Dolgu'
        ];
        
        return $types[$type] ?? ucfirst($type);
    }
    
    public function openModal($date = null, $time = null)
    {
        $this->resetForm();
        if ($date) {
            $this->appointment_date = $date;
        }
        if ($time) {
            $this->appointment_time = $time;
        }
        $this->showModal = true;
    }
    
    public function selectPatient($patientId)
    {
        $patient = Patient::find($patientId);
        if ($patient) {
            $this->selectedPatient = $patient;
            $this->patient_id = $patient->id;
            $this->patient_name = $patient->first_name . ' ' . $patient->last_name;
            $this->patient_phone = $patient->phone;
            $this->patientSearch = '';
        }
    }

    public function clearPatientSelection()
    {
        $this->selectedPatient = null;
        $this->patient_id = null;
        $this->patient_name = '';
        $this->patient_phone = '';
        $this->patientSearch = '';
    }

    public function showAppointmentDetails($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment) {
            $this->editAppointment($appointmentId);
        }
    }
    
    public function getTimeSlots()
    {
        $slots = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            foreach ([0, 15, 30, 45] as $minute) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $slots[] = $time;
            }
        }
        return $slots;
    }

    public function editAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->editingAppointment = $appointment;
        
        $this->patient_id = $appointment->patient_id;
        $this->patient_name = $appointment->patient_name;
        $this->patient_phone = $appointment->patient_phone;
        
        // Eğer patient_id varsa, hasta bilgilerini yükle
        if ($appointment->patient_id) {
            $this->selectedPatient = Patient::find($appointment->patient_id);
        } else {
            $this->selectedPatient = null;
        }
        
        $this->appointment_date = $appointment->appointment_date->format('Y-m-d');
        $this->appointment_time = $appointment->appointment_time->format('H:i');
        
        // Saat ve dakikayı ayır
        $timeParts = explode(':', $appointment->appointment_time->format('H:i'));
        $this->appointment_hour = $timeParts[0];
        $this->appointment_minute = $timeParts[1];
        
        $this->appointment_type = $appointment->appointment_type;
        $this->notes = $appointment->notes;
        $this->status = $appointment->status;
        
        $this->showModal = true;
    }

    public function saveAppointment()
    {
        // Hasta seçilmemişse, hasta adı gerekli
        if (!$this->patient_id) {
            $this->validate([
                'patient_name' => 'required|string|max:255',
                'patient_phone' => 'nullable|string|max:20',
            ], [
                'patient_name.required' => 'Hasta adı gereklidir.',
            ]);
        }
        
        // Saat ve dakikadan appointment_time oluştur
        if ($this->appointment_hour && $this->appointment_minute !== '') {
            $this->appointment_time = $this->appointment_hour . ':' . $this->appointment_minute;
        }
        
        // Otomatik olarak planlandı durumu
        $this->status = 'scheduled';
        
        $this->validate();

        $appointmentData = [
            'patient_id' => $this->patient_id,
            'patient_name' => $this->patient_name,
            'patient_phone' => $this->patient_phone,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'appointment_type' => $this->appointment_type,
            'notes' => $this->notes,
            'status' => $this->status,
            'doctor_id' => $this->getDoctorIdForFiltering(),
        ];

        if ($this->editingAppointment) {
            $this->editingAppointment->update($appointmentData);
            
            Activity::create([
                'type' => 'appointment_updated',
                'description' => 'Randevu güncellendi: ' . $this->appointment_type,
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->getDoctorIdForFiltering()
            ]);
            
            session()->flash('message', 'Randevu başarıyla güncellendi.');
        } else {
            Appointment::create($appointmentData);
            
            Activity::create([
                'type' => 'appointment_created',
                'description' => 'Yeni randevu oluşturuldu: ' . $this->appointment_type,
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->getDoctorIdForFiltering()
            ]);
            
            session()->flash('message', 'Randevu başarıyla oluşturuldu.');
        }

        $this->closeModal();
    }



    public function closeModal()
    {
        $this->showModal = false;
        $this->editingAppointment = null;
        $this->resetForm();
    }

    public function dragStart($appointmentId)
    {
        $this->draggedAppointment = $appointmentId;
    }
    
    public function dropAppointment($appointmentId, $newDate)
    {
        try {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->update([
                    'appointment_date' => $newDate
                ]);
                
                // Aktivite kaydı oluştur
                $user = Auth::user();
                $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
                
                Activity::create([
                    'type' => 'appointment_moved',
                    'description' => "Randevu {$appointment->appointment_date->format('d.m.Y')} tarihinden {$newDate} tarihine taşındı.",
                    'patient_id' => $appointment->patient_id,
                    'doctor_id' => $doctorId
                ]);
                
                session()->flash('message', 'Randevu başarıyla taşındı.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Randevu taşınırken bir hata oluştu.');
        }
    }
    
    private function resetForm()
    {
        $this->patient_id = null;
        $this->selectedPatient = null;
        $this->patient_name = '';
        $this->patient_phone = '';
        $this->appointment_date = now()->format('Y-m-d');
        $this->appointment_time = '';
        $this->appointment_type = 'control';
        $this->notes = '';
        $this->status = 'scheduled';
        $this->patientSearch = '';
    }

    public function getAppointmentsProperty()
    {
        $query = Appointment::with('patient');
        
        // Doktor bazlı filtreleme
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        } elseif (in_array($user->role, ['nurse', 'admin'])) {
            // Hemşire ve admin için doctor_id kontrolü
            if ($user->role === 'nurse' && $user->doctor_id) {
                $query->where('doctor_id', $user->doctor_id);
            }
            // Admin için tüm hastalar görünür (ek filtre yok)
        }
        
        // Görünüm moduna göre filtreleme
        if ($this->viewMode === 'weekly') {
            $startDate = Carbon::parse($this->currentWeek)->startOfWeek();
            $endDate = Carbon::parse($this->currentWeek)->endOfWeek();
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        } else {
            $query->whereMonth('appointment_date', $this->currentMonth)
                  ->whereYear('appointment_date', $this->currentYear);
        }
        
        // Arama filtreleri
        $query->when($this->search, function ($q) {
            $q->where(function ($query) {
                $query->where('patient_name', 'like', '%' . $this->search . '%')
                      ->orWhere('patient_phone', 'like', '%' . $this->search . '%')
                      ->orWhereHas('patient', function ($q) {
                          $q->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('tc_identity', 'like', '%' . $this->search . '%');
                      });
            });
        })
        ->when($this->filterStatus, function ($q) {
            $q->where('status', $this->filterStatus);
        })
        ->when($this->filterType, function ($q) {
            $q->where('appointment_type', $this->filterType);
        })
        ->orderBy('appointment_date', 'asc')
        ->orderBy('appointment_time', 'asc');

        return $query->paginate(10);
    }

    public function getPatientsProperty()
    {
        $query = Patient::orderBy('first_name');
        
        // Doktor bazlı filtreleme
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        } elseif (in_array($user->role, ['nurse', 'admin'])) {
            // Hemşire ve admin için doctor_id kontrolü
            if ($user->role === 'nurse' && $user->doctor_id) {
                $query->where('doctor_id', $user->doctor_id);
            }
            // Admin için tüm hastalar görünür (ek filtre yok)
        }
        
        return $query->limit(10)->get();
    }
    
    public function getFilteredPatientsProperty()
    {
        if (!$this->patientSearch || strlen($this->patientSearch) < 2) {
            return collect();
        }
        
        $searchTerm = strtolower($this->patientSearch);
        
        $query = Patient::where(function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhere('tc_identity', 'like', '%' . $this->patientSearch . '%')
                  ->orWhere('phone', 'like', '%' . $this->patientSearch . '%');
        });
        
        // Doktor bazlı filtreleme
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->limit(10)->get();
    }

    public function getAppointmentsByDateProperty()
    {
        if ($this->viewMode === 'weekly') {
            $startDate = Carbon::parse($this->currentWeek)->startOfWeek();
            $endDate = Carbon::parse($this->currentWeek)->endOfWeek();
        } else {
            $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
            $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();
        }
        
        $query = Appointment::with('patient')
            ->whereBetween('appointment_date', [$startDate, $endDate]);
            
        // Doktor bazlı filtreleme
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('patient_name', 'like', '%' . $this->search . '%')
                          ->orWhere('patient_phone', 'like', '%' . $this->search . '%')
                          ->orWhereHas('patient', function ($q) {
                              $q->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                ->orWhere('tc_identity', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->appointment_date->format('Y-m-d');
            });
    }
    
    public function getCurrentPeriodText()
    {
        if ($this->viewMode === 'weekly') {
            $start = Carbon::parse($this->currentWeek)->startOfWeek();
            $end = Carbon::parse($this->currentWeek)->endOfWeek();
            return $start->format('d.m.Y') . ' - ' . $end->format('d.m.Y');
        } else {
            $months = [
                1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
            ];
            return $months[$this->currentMonth] . ' ' . $this->currentYear;
        }
    }

    public function render()
    {
        return view('livewire.appointment-list', [
            'appointments' => $this->appointments,
            'patients' => $this->patients,
            'filteredPatients' => $this->filteredPatients,
            'appointmentsByDate' => $this->appointmentsByDate,
            'timeSlots' => $this->getTimeSlots(),
            'currentPeriodText' => $this->getCurrentPeriodText(),
            'searchResults' => $this->getSearchResults(),
            'searchedPatients' => $this->getSearchedPatients()
        ]);
    }

    public function getSearchResults()
    {
        if (!$this->search) {
            return collect();
        }

        return Appointment::where(function($query) {
            $query->where('patient_name', 'like', '%' . $this->search . '%')
                  ->orWhere('patient_phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function($q) {
                      $q->where('tc_identity', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  });
        })->get();
    }

    public function getSearchedPatients()
    {
        if (!$this->patientSearch || strlen($this->patientSearch) < 2) {
            return collect();
        }

        $searchTerm = strtolower($this->patientSearch);
        
        $query = Patient::where(function($query) use ($searchTerm) {
            $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhere('tc_identity', 'like', '%' . $this->patientSearch . '%')
                  ->orWhere('phone', 'like', '%' . $this->patientSearch . '%');
        });
        
        // Doktor bazlı filtreleme
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        } elseif (in_array($user->role, ['nurse', 'admin'])) {
            // Hemşire ve admin için doctor_id kontrolü
            if ($user->role === 'nurse' && $user->doctor_id) {
                $query->where('doctor_id', $user->doctor_id);
            }
            // Admin için tüm hastalar görünür (ek filtre yok)
        }
        
        return $query->limit(10)->get();
    }
    
    private function getDoctorIdForFiltering()
    {
        $user = Auth::user();
        if ($user->role === 'doctor') {
            return $user->id;
        } elseif ($user->doctor_id) {
            return $user->doctor_id;
        }
        return null;
    }
    
    // Notes Methods
    public function showNotes($appointmentId)
    {
        $this->selectedAppointmentForNotes = Appointment::find($appointmentId);
        $this->loadAppointmentNotes($appointmentId);
        $this->showNotesModal = true;
        $this->resetNoteForm();
    }

    public function closeNotesModal()
    {
        $this->showNotesModal = false;
        $this->selectedAppointmentForNotes = null;
        $this->appointmentNotes = [];
        $this->resetNoteForm();
        $this->editingNote = null;
    }

    public function loadAppointmentNotes($appointmentId)
    {
        $user = Auth::user();
        
        $this->appointmentNotes = AppointmentNote::where('appointment_id', $appointmentId)
            ->visibleTo($user)
            ->with(['user', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function editNote($noteId)
    {
        $note = AppointmentNote::find($noteId);
        
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
        $note = AppointmentNote::find($noteId);
        
        if (!$this->canDeleteNote($note)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Bu notu silme yetkiniz yok.'
            ]);
            return;
        }
        
        $note->delete();
        
        // Activities tablosuna kayıt ekle
        $user = auth()->user();
        $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
        
        Activity::create([
            'type' => 'appointment_note_deleted',
            'description' => 'Randevu notu silindi',
            'patient_id' => $this->selectedAppointmentForNotes->patient_id,
            'doctor_id' => $doctorId
        ]);
        
        $this->loadAppointmentNotes($this->selectedAppointmentForNotes->id);
        
        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Not başarıyla silindi.'
        ]);
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
            $note = AppointmentNote::find($this->editingNote);
            
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
            $user = auth()->user();
            $doctorId = $user->role === 'doctor' ? $user->id : ($user->role === 'secretary' ? $user->doctor_id : null);
            
            Activity::create([
                'type' => 'appointment_note_updated',
                'description' => 'Randevu notu güncellendi: ' . substr($this->newNote['content'], 0, 50) . (strlen($this->newNote['content']) > 50 ? '...' : ''),
                'patient_id' => $this->selectedAppointmentForNotes->patient_id,
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
            
            $appointmentNote = AppointmentNote::create([
                'appointment_id' => $this->selectedAppointmentForNotes->id,
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
                'type' => 'appointment_note_added',
                'description' => 'Randevu notu eklendi: ' . substr($this->newNote['content'], 0, 50) . (strlen($this->newNote['content']) > 50 ? '...' : ''),
                'patient_id' => $this->selectedAppointmentForNotes->patient_id,
                'doctor_id' => $doctorId
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Not başarıyla eklendi.'
            ]);
        }

        $this->loadAppointmentNotes($this->selectedAppointmentForNotes->id);
        $this->resetNoteForm();
        $this->editingNote = null;
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

    public function resetNoteForm()
    {
        $this->newNote = [
            'content' => '',
            'note_type' => 'general',
            'is_private' => false
        ];
    }

    public function getNoteTypeText($type)
    {
        return match($type) {
            'medical' => 'Tıbbi',
            'appointment' => 'Randevu',
            'followup' => 'Takip',
            'treatment' => 'Tedavi',
            'general' => 'Genel',
            default => 'Genel'
        };
    }

    public function getNoteTypeIcon($type)
    {
        return match($type) {
            'medical' => 'fas fa-stethoscope text-red-500',
            'appointment' => 'fas fa-calendar text-blue-500',
            'followup' => 'fas fa-eye text-green-500',
            'general' => 'fas fa-sticky-note text-yellow-500',
            default => 'fas fa-sticky-note text-gray-500'
        };
    }
}
