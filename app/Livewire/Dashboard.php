<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Activity;
use App\Models\Appointment;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $operationTrendPeriod = '6months';
    public $procedurePeriod = 'current_month';
    public function getStatsProperty()
    {
        $user = auth()->user();
        $currentMonth = $this->convertToTurkishMonth(Carbon::now()->format('m.Y'));
        $currentYear = Carbon::now()->year;
        $lastMonth = $this->convertToTurkishMonth(Carbon::now()->subMonth()->format('m.Y'));
        $lastYear = Carbon::now()->subYear()->year;

        // Operation query with doctor filtering
        $operationQuery = Operation::query();
        if ($user->role === 'doctor') {
            $operationQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $operationQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin sees all operations

        // Patient query with doctor filtering
        $patientQuery = Patient::query();
        if ($user->role === 'doctor') {
            $patientQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $patientQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin sees all patients

        // Bu ay operasyonları
        $thisMonthOperations = (clone $operationQuery)->where('registration_period', $currentMonth)->count();
        
        // Geçen ay operasyonları
        $lastMonthOperations = (clone $operationQuery)->where('registration_period', $lastMonth)->count();
        
        // Bu yıl operasyonları (registration_period'a göre)
        $thisYearOperations = (clone $operationQuery)->where('registration_period', 'like', '% ' . $currentYear)->count();
        
        // Geçen yıl operasyonları (registration_period'a göre)
        $lastYearOperations = (clone $operationQuery)->where('registration_period', 'like', '% ' . $lastYear)->count();
        
        // Toplam operasyonlar
        $totalOperations = (clone $operationQuery)->count();
        
        // Toplam hastalar
        $totalPatients = $patientQuery->count();
        
        // Yüzde hesaplamaları
        $monthlyPercentageChange = $lastMonthOperations > 0 
            ? round((($thisMonthOperations - $lastMonthOperations) / $lastMonthOperations) * 100, 1)
            : 0;
            
        $yearlyPercentageChange = $lastYearOperations > 0 
            ? round((($thisYearOperations - $lastYearOperations) / $lastYearOperations) * 100, 1)
            : 0;

        return [
            'this_month_operations' => $thisMonthOperations,
            'total_patients' => $totalPatients,
            'total_operations' => $totalOperations,
            'monthly_percentage_change' => $monthlyPercentageChange,
            'yearly_percentage_change' => $yearlyPercentageChange,
            'current_month' => $currentMonth,
            'current_year' => $currentYear
        ];
    }
    
    public function getMonthlyOperationTrendProperty()
    {
        $user = auth()->user();
        $months = [];
        $data = [];
        
        $monthsCount = $this->operationTrendPeriod === '12months' ? 11 : 5;
        
        for ($i = $monthsCount; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $period = $this->convertToTurkishMonth($date->format('m.Y'));
            $monthName = $period;
            
            $operationQuery = Operation::where('registration_period', $period);
            if ($user->role === 'doctor') {
                $operationQuery->where('doctor_id', $user->id);
            } elseif ($user->role === 'secretary') {
                $operationQuery->where('doctor_id', $user->doctor_id);
            }
            // Admin sees all operations
            
            $months[] = $monthName;
            $data[] = $operationQuery->count();
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }
    
    public function getProcedureDistributionProperty()
    {
        $user = auth()->user();
        
        if ($this->procedurePeriod === 'current_month') {
            $currentMonth = $this->convertToTurkishMonth(Carbon::now()->format('m.Y'));
            $baseQuery = Operation::where('registration_period', $currentMonth);
        } else { // last_3_months
            $periods = [];
            for ($i = 2; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $periods[] = $this->convertToTurkishMonth($date->format('m.Y'));
            }
            $baseQuery = Operation::whereIn('registration_period', $periods);
        }
        
        if ($user->role === 'doctor') {
            $baseQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $baseQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin sees all operations
        
        $surgery = (clone $baseQuery)->where('process', 'surgery')->count();
        $mesotherapy = (clone $baseQuery)->where('process', 'mesotherapy')->count();
        $botox = (clone $baseQuery)->where('process', 'botox')->count();
        $filler = (clone $baseQuery)->where('process', 'filler')->count();
            
        return [
            'labels' => ['Ameliyat', 'Mezoterapi', 'Botoks', 'Dolgu'],
            'data' => [$surgery, $mesotherapy, $botox, $filler]
        ];
    }
    
    private function convertToTurkishMonth($period)
    {
        $months = [
            '01' => 'Ocak',
            '02' => 'Şubat', 
            '03' => 'Mart',
            '04' => 'Nisan',
            '05' => 'Mayıs',
            '06' => 'Haziran',
            '07' => 'Temmuz',
            '08' => 'Ağustos',
            '09' => 'Eylül',
            '10' => 'Ekim',
            '11' => 'Kasım',
            '12' => 'Aralık'
        ];
        
        $parts = explode('.', $period);
        $month = $parts[0];
        $year = $parts[1];
        
        return $months[$month] . ' ' . $year;
    }

    public function getTodayAppointmentsProperty()
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        $appointmentQuery = Appointment::with('patient')
            ->where('appointment_date', '>=', $today)
            ->where('status', '!=', 'cancelled');
            
        if ($user->role === 'doctor') {
            $appointmentQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $appointmentQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin sees all appointments
        
        return $appointmentQuery
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(4)
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'time' => Carbon::parse($appointment->appointment_time)->format('H:i'),
                    'patient_name' => $appointment->patient_name ?: ($appointment->patient ? $appointment->patient->first_name . ' ' . $appointment->patient->last_name : 'Bilinmeyen'),
                    'appointment_type' => $appointment->appointment_type,
                    'appointment_type_text' => $this->getAppointmentTypeText($appointment->appointment_type),
                    'status' => $appointment->status,
                    'color' => $this->getAppointmentTypeColor($appointment->appointment_type),
                    'bg_color' => $this->getAppointmentTypeBgColor($appointment->appointment_type)
                ];
            });
    }
    
    private function getAppointmentTypeText($type)
    {
        return match($type) {
            'consultation' => 'Konsültasyon',
            'operation' => 'Operasyon',
            'control' => 'Kontrol',
            'botox' => 'Botoks',
            'filler' => 'Dolgu',
            default => 'Muayene'
        };
    }
    
    private function getAppointmentTypeColor($type)
    {
        return match($type) {
            'consultation' => 'green',
            'operation' => 'red',
            'control' => 'blue',
            'botox' => 'purple',
            'filler' => 'yellow',
            default => 'gray'
        };
    }
    
    private function getAppointmentTypeBgColor($type)
    {
        return match($type) {
            'consultation' => 'green-50',
            'operation' => 'red-50',
            'control' => 'blue-50',
            'botox' => 'purple-50',
            'filler' => 'yellow-50',
            default => 'gray-50'
        };
    }

    public function getRecentActivitiesProperty()
    {
        $user = auth()->user();
        
        $activityQuery = Activity::with('patient')->latest();
        
        if ($user->role === 'doctor') {
            $activityQuery->where('doctor_id', $user->id);
        } elseif ($user->role === 'secretary') {
            $activityQuery->where('doctor_id', $user->doctor_id);
        }
        // Admin sees all activities
        
        return $activityQuery
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'patient_name' => $activity->patient ? $activity->patient->first_name . ' ' . $activity->patient->last_name : 'Sistem',
                    'created_at' => $activity->created_at,
                    'time_ago' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity->type),
                    'color' => $this->getActivityColor($activity->type)
                ];
            });
    }
    
    private function getActivityIcon($type)
    {
        return match($type) {
            'new_patient_registration' => 'fas fa-user-plus',
            'operation_added' => 'fas fa-procedures',
            'operation_updated' => 'fas fa-edit',
            'operation_note_added' => 'fas fa-sticky-note',
            'patient_note_added' => 'fas fa-comment',
            'patient_updated' => 'fas fa-user-edit',
            default => 'fas fa-info-circle'
        };
    }
    
    private function getActivityColor($type)
    {
        return match($type) {
            'new_patient_registration' => 'purple',
            'operation_added' => 'green',
            'operation_updated' => 'blue',
            'operation_note_added' => 'yellow',
            'patient_note_added' => 'indigo',
            'patient_updated' => 'orange',
            default => 'gray'
        };
    }
    
    public function getActivityTitle($type)
    {
        return match($type) {
            'new_patient_registration' => 'Yeni hasta kaydı',
            'operation_added' => 'Yeni operasyon eklendi',
            'operation_updated' => 'Operasyon güncellendi',
            'operation_note_added' => 'Operasyon notu eklendi',
            'patient_note_added' => 'Hasta notu eklendi',
            'patient_updated' => 'Hasta bilgileri güncellendi',
            default => 'Sistem aktivitesi'
        };
    }

    public function mount()
    {
        // Component yüklendiğinde grafikleri yenile
        $this->dispatch('refreshCharts');
    }
    
    public function refreshCharts()
    {
        // Manuel grafik yenileme
        $this->dispatch('refreshCharts');
    }
    
    public function updatedOperationTrendPeriod()
    {
        $this->dispatch('refreshCharts');
    }
    
    public function updatedProcedurePeriod()
    {
        $this->dispatch('refreshCharts');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}