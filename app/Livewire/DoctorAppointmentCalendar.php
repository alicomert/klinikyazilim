<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// Türkçe tarih formatları için Carbon locale ayarı
Carbon::setLocale('tr');

class DoctorAppointmentCalendar extends Component
{
    public $currentDate;
    public $viewMode = 'daily'; // daily, weekly
    public $appointments = [];
    public $selectedAppointment = null;
    public $showAppointmentModal = false;
    public $showPatientModal = false;
    public $showOperationsModal = false;
    public $selectedPatient = null;
    public $patientOperations = [];
    
    public function mount()
    {
        $this->currentDate = Carbon::today();
        $this->findNextAppointmentDate();
        $this->loadAppointments();
    }
    
    public function findNextAppointmentDate()
    {
        $user = Auth::user();
        
        // Bugünden itibaren en yakın randevulu günü bul
        $nextAppointment = Appointment::where('doctor_id', $user->id)
            ->where('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->first();
            
        if ($nextAppointment) {
            $this->currentDate = Carbon::parse($nextAppointment->appointment_date);
        }
    }
    
    public function loadAppointments()
    {
        $user = Auth::user();
        
        if ($this->viewMode === 'daily') {
            $this->appointments = Appointment::with(['patient', 'operation'])
                ->where('doctor_id', $user->id)
                ->whereDate('appointment_date', $this->currentDate)
                ->orderBy('appointment_time', 'asc')
                ->get();
        } else {
            // Haftalık görünüm
            $startOfWeek = $this->currentDate->copy()->startOfWeek();
            $endOfWeek = $this->currentDate->copy()->endOfWeek();
            
            $this->appointments = Appointment::with(['patient', 'operation'])
                ->where('doctor_id', $user->id)
                ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
                ->orderBy('appointment_date', 'asc')
                ->orderBy('appointment_time', 'asc')
                ->get();
        }
    }
    
    public function previousDate()
    {
        if ($this->viewMode === 'daily') {
            $this->currentDate = $this->currentDate->copy()->subDay();
        } else {
            $this->currentDate = $this->currentDate->copy()->subWeek();
        }
        $this->loadAppointments();
    }
    
    public function nextDate()
    {
        if ($this->viewMode === 'daily') {
            $this->currentDate = $this->currentDate->copy()->addDay();
        } else {
            $this->currentDate = $this->currentDate->copy()->addWeek();
        }
        $this->loadAppointments();
    }
    
    public function goToToday()
    {
        $this->currentDate = Carbon::today();
        $this->loadAppointments();
    }
    
    public function switchViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->loadAppointments();
    }
    
    public function showAppointmentDetails($appointmentId)
    {
        $this->selectedAppointment = Appointment::with(['patient', 'operation'])
            ->find($appointmentId);
        $this->showAppointmentModal = true;
    }
    
    public function closeAppointmentModal()
    {
        $this->selectedAppointment = null;
        $this->showAppointmentModal = false;
    }

    public function showPatientDetails($patientId)
    {
        $this->selectedPatient = \App\Models\Patient::find($patientId);
        $this->showPatientModal = true;
    }

    public function closePatientModal()
    {
        $this->showPatientModal = false;
        $this->selectedPatient = null;
    }

    public function showPatientOperations($patientId)
    {
        $this->selectedPatient = \App\Models\Patient::find($patientId);
        $this->patientOperations = \App\Models\Operation::where('patient_id', $patientId)
            ->with(['doctor', 'creator'])
            ->orderBy('process_date', 'desc')
            ->get();
        $this->showOperationsModal = true;
    }

    public function closeOperationsModal()
    {
        $this->showOperationsModal = false;
        $this->selectedPatient = null;
        $this->patientOperations = [];
    }
    
    public function getFormattedDateProperty()
    {
        if ($this->viewMode === 'daily') {
            return $this->currentDate->translatedFormat('d F Y, l');
        } else {
            $startOfWeek = $this->currentDate->copy()->startOfWeek();
            $endOfWeek = $this->currentDate->copy()->endOfWeek();
            return $startOfWeek->translatedFormat('d M') . ' - ' . $endOfWeek->translatedFormat('d M Y');
        }
    }
    
    public function getWeekDaysProperty()
    {
        $days = [];
        $startOfWeek = $this->currentDate->copy()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayAppointments = $this->appointments->filter(function($appointment) use ($date) {
                return Carbon::parse($appointment->appointment_date)->isSameDay($date);
            });
            
            $days[] = [
                'date' => $date,
                'dayName' => $date->translatedFormat('l'), // Türkçe gün adı
                'dayShort' => $date->translatedFormat('D'), // Türkçe kısa gün adı
                'appointments' => $dayAppointments,
                'isToday' => $date->isToday(),
                'hasAppointments' => $dayAppointments->count() > 0
            ];
        }
        
        return $days;
    }
    
    public function render()
    {
        return view('livewire.doctor-appointment-calendar');
    }
}
