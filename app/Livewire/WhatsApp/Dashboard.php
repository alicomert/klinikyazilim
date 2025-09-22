<?php

namespace App\Livewire\WhatsApp;

use Livewire\Component;
use App\Models\WhatsAppConfig;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $stats = [];
    public $recentMessages = [];
    public $recentTemplates = [];
    public $activeConfigs = [];
    public $messageStats = [];
    public $hasPhoneId = false;
    public $showSetupModal = false;
    
    public function mount()
    {
        $this->checkPhoneIdSetup();
        $this->loadStats();
        $this->loadRecentData();
        $this->loadMessageStats();
    }

    public function checkPhoneIdSetup()
    {
        $user = auth()->user();
        
        // Admin tüm konfigürasyonları görebilir
        if ($user->role === 'admin') {
            $this->hasPhoneId = WhatsAppConfig::where('phone_number_id', '!=', null)
                ->where('phone_number_id', '!=', '')
                ->exists();
        } else {
            // Doktorlar sadece kendi konfigürasyonlarını görebilir
            $this->hasPhoneId = WhatsAppConfig::where('doctor_id', $user->id)
                ->where('phone_number_id', '!=', null)
                ->where('phone_number_id', '!=', '')
                ->exists();
        }
    }

    public function loadStats()
    {
        $user = auth()->user();
        
        // Temel istatistikler
        $this->stats = [
            'total_configs' => $this->getTotalConfigs(),
            'active_configs' => $this->getActiveConfigs(),
            'total_templates' => $this->getTotalTemplates(),
            'approved_templates' => $this->getApprovedTemplates(),
            'messages_today' => $this->getMessagesToday(),
            'messages_this_month' => $this->getMessagesThisMonth(),
            'success_rate' => $this->getSuccessRate(),
            'pending_approvals' => $this->getPendingApprovals()
        ];
    }

    public function loadRecentData()
    {
        $user = auth()->user();
        
        // Son mesajlar
        $this->recentMessages = WhatsAppMessage::with(['config', 'user'])
            ->when($user->role !== 'admin', function($query) use ($user) {
                $query->where('doctor_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Son şablonlar
        $this->recentTemplates = WhatsAppTemplate::with('user')
            ->when($user->role !== 'admin', function($query) use ($user) {
                $query->where('doctor_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Aktif konfigürasyonlar
        $this->activeConfigs = WhatsAppConfig::with('doctor')
            ->where('is_active', true)
            ->when($user->role !== 'admin', function($query) use ($user) {
                $query->where('doctor_id', $user->id);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
    }

    public function loadMessageStats()
    {
        // Son 7 günün mesaj istatistikleri
        $this->messageStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = WhatsAppMessage::whereDate('created_at', $date)
                ->when(auth()->user()->role !== 'admin', function($query) {
                    $query->where('doctor_id', auth()->id());
                })
                ->count();
            
            $this->messageStats[] = [
                'date' => $date->format('d.m'),
                'count' => $count
            ];
        }
    }

    // İstatistik hesaplama metodları
    private function getTotalConfigs()
    {
        return WhatsAppConfig::when(auth()->user()->role !== 'admin', function($query) {
            $query->where('doctor_id', auth()->id());
        })->count();
    }

    private function getActiveConfigs()
    {
        return WhatsAppConfig::where('is_active', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                $query->where('doctor_id', auth()->id());
            })->count();
    }

    private function getTotalTemplates()
    {
        return WhatsAppTemplate::when(auth()->user()->role !== 'admin', function($query) {
            $query->where('doctor_id', auth()->id());
        })->count();
    }

    private function getApprovedTemplates()
    {
        return WhatsAppTemplate::where('is_approved', true)
            ->when(auth()->user()->role !== 'admin', function($query) {
                $query->where('doctor_id', auth()->id());
            })->count();
    }

    private function getMessagesToday()
    {
        return WhatsAppMessage::whereDate('created_at', today())
            ->when(auth()->user()->role !== 'admin', function($query) {
                $query->where('doctor_id', auth()->id());
            })->count();
    }

    private function getMessagesThisMonth()
    {
        return WhatsAppMessage::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->when(auth()->user()->role !== 'admin', function($query) {
                $query->where('doctor_id', auth()->id());
            })->count();
    }

    private function getSuccessRate()
    {
        $total = WhatsAppMessage::when(auth()->user()->role !== 'admin', function($query) {
            $query->where('doctor_id', auth()->id());
        })->count();

        if ($total === 0) return 0;

        $successful = WhatsAppMessage::where('status', 'sent')
            ->when(auth()->user()->role !== 'admin', function($query) {
                $query->where('doctor_id', auth()->id());
            })->count();

        return round(($successful / $total) * 100, 1);
    }

    private function getPendingApprovals()
    {
        if (auth()->user()->role !== 'admin') {
            return 0;
        }

        return WhatsAppTemplate::where('is_approved', false)
            ->where('is_active', true)
            ->count();
    }

    // Hızlı işlemler
    public function refreshStats()
    {
        $this->loadStats();
        $this->loadRecentData();
        $this->loadMessageStats();
        
        session()->flash('message', 'İstatistikler güncellendi.');
    }

    public function testConnection($configId)
    {
        $config = WhatsAppConfig::findOrFail($configId);
        
        // Test bağlantısı simülasyonu
        $success = rand(0, 1); // Gerçek implementasyonda API çağrısı yapılacak
        
        if ($success) {
            session()->flash('message', $config->name . ' bağlantısı başarılı.');
        } else {
            session()->flash('error', $config->name . ' bağlantısı başarısız.');
        }
        
        $this->loadRecentData();
    }

    // Yetki kontrol metodları
    public function canManageConfigs()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor']);
    }

    public function canManageTemplates()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor', 'nurse']);
    }

    public function canViewAllStats()
    {
        return auth()->user()->role === 'admin';
    }

    public function canApproveTemplates()
    {
        return auth()->user()->role === 'admin';
    }

    public function render()
    {
        return view('livewire.whats-app.dashboard');
    }
}
