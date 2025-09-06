<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DoctorPanel extends Component
{
    public function getStatsProperty()
    {
        $user = Auth::user();
        
        // Kullanıcı rolüne göre filtreleme
        $patientQuery = Patient::query();
        $appointmentQuery = Appointment::query();
        $operationQuery = Operation::query();
        
        if ($user->role === 'doctor') {
            $patientQuery->where('doctor_id', $user->id);
            $appointmentQuery->where('doctor_id', $user->id);
            $operationQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $patientQuery->where('doctor_id', $user->doctor_id);
            $appointmentQuery->where('doctor_id', $user->doctor_id);
            $operationQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin tüm verileri görebilir
        
        // Bu haftaki hastalar (son 7 gün)
        $weeklyPatients = (clone $patientQuery)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        // Geçen haftaki hastalar
        $lastWeekPatients = (clone $patientQuery)
            ->whereBetween('created_at', [
                Carbon::now()->subDays(14),
                Carbon::now()->subDays(7)
            ])
            ->count();
            
        // Bu haftaki randevular
        $weeklyAppointments = (clone $appointmentQuery)
            ->whereBetween('appointment_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->where('status', '!=', 'cancelled')
            ->count();
            
        // Bu aydaki operasyonlar
        $monthlyOperations = (clone $operationQuery)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
            
        // Yeni hasta sayısı hesaplama
        $newPatients = $weeklyPatients - $lastWeekPatients;
        
        return [
            'weekly_patients' => $weeklyPatients,
            'new_patients' => $newPatients,
            'weekly_appointments' => $weeklyAppointments,
            'monthly_operations' => $monthlyOperations
        ];
    }
    
    public function getRecentPatientsProperty()
    {
        $user = Auth::user();
        
        $query = Patient::query();
        
        if ($user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $query->where('doctor_id', $user->doctor_id);
        }
        
        return $query->latest()
            ->take(5)
            ->get()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->first_name . ' ' . $patient->last_name,
                    'phone' => $patient->phone,
                    'created_at' => $patient->created_at,
                    'age' => $patient->birth_date ? Carbon::parse($patient->birth_date)->age : null
                ];
            });
    }

    public function render()
    {
        return view('livewire.doctor-panel');
    }
}