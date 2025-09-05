<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Activity;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function getStatsProperty()
    {
        $currentMonth = $this->convertToTurkishMonth(Carbon::now()->format('m.Y'));
        $currentYear = Carbon::now()->year;
        $lastMonth = $this->convertToTurkishMonth(Carbon::now()->subMonth()->format('m.Y'));
        $lastYear = Carbon::now()->subYear()->year;

        // Bu ay operasyonları
        $thisMonthOperations = Operation::where('registration_period', $currentMonth)->count();
        
        // Geçen ay operasyonları
        $lastMonthOperations = Operation::where('registration_period', $lastMonth)->count();
        
        // Bu yıl operasyonları (registration_period'a göre)
        $thisYearOperations = Operation::where('registration_period', 'like', '% ' . $currentYear)->count();
        
        // Geçen yıl operasyonları (registration_period'a göre)
        $lastYearOperations = Operation::where('registration_period', 'like', '% ' . $lastYear)->count();
        
        // Toplam operasyonlar
        $totalOperations = Operation::count();
        
        // Toplam hastalar
        $totalPatients = Patient::count();
        
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
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $period = $this->convertToTurkishMonth($date->format('m.Y'));
            $monthName = $period;
            
            $months[] = $monthName;
            $data[] = Operation::where('registration_period', $period)->count();
        }
        
        return [
            'labels' => $months,
            'data' => $data
        ];
    }
    
    public function getProcedureDistributionProperty()
    {
        $currentMonth = $this->convertToTurkishMonth(Carbon::now()->format('m.Y'));
        
        $surgery = Operation::where('process', 'surgery')
            ->where('registration_period', $currentMonth)
            ->count();
            
        $mesotherapy = Operation::where('process', 'mesotherapy')
            ->where('registration_period', $currentMonth)
            ->count();
            
        $botox = Operation::where('process', 'botox')
            ->where('registration_period', $currentMonth)
            ->count();
            
        $filler = Operation::where('process', 'filler')
            ->where('registration_period', $currentMonth)
            ->count();
            
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

    public function getRecentActivitiesProperty()
    {
        return Activity::with('patient')
            ->latest()
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

    public function render()
    {
        return view('livewire.dashboard');
    }
}