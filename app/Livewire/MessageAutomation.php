<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MessageAutomationConfig;
use App\Models\MessageAutomationLog;
use App\Services\WaMessageService;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MessageAutomation extends Component
{
    use WithPagination;

    // Form properties
    public $showModal = false;
    public $editingConfig = null;
    public $newConfig = [];
    

    
    // Stats
    public $showStats = true;

    // Reports (GET-REPORTS)
    public $showReports = true;
    public $reportsFilters = [
        'start_date' => '',
        'end_date' => '',
        'state' => '0',
        'type' => '0',
        'page' => 1,
        'count' => 10,
        'report_id' => ''
    ];
    public $reports = null;
    public $reportsError = null;
    public $expandedReportIndex = null;

    protected $rules = [
        'newConfig.api_token' => 'required|string',
        'newConfig.phone_number' => 'required|string',
        'newConfig.message_template' => 'required|string',
        'newConfig.hours_before_appointment' => 'required|integer|min:1|max:168',
        'newConfig.send_speed' => 'required|integer|min:1|max:60',
        'newConfig.campaign_name' => 'nullable|string|max:255'
    ];

    protected $validationAttributes = [
        'newConfig.api_token' => 'API Token',
        'newConfig.phone_number' => 'Telefon Numarası',
        'newConfig.message_template' => 'Mesaj Şablonu',
        'newConfig.hours_before_appointment' => 'Randevu Öncesi Saat',
        'newConfig.send_speed' => 'Gönderim Hızı',
        'newConfig.campaign_name' => 'Kampanya Adı'
    ];

    public function mount()
    {
        $this->resetForm();

        // Raporlar varsayılan 1 ay (açılır açılmaz dolu gelsin)
        $this->reportsFilters['start_date'] = now()->subMonth()->toDateString();
        $this->reportsFilters['end_date'] = now()->toDateString();

        // Rapor bölümünü açık başlat
        $this->showReports = true;

        // İlk yüklemede raporları çek
        $this->fetchReports();
    }

    public function resetForm()
    {
        $this->newConfig = [
            'api_token' => '',
            'phone_number' => '',
            'message_template' => 'Merhaba {hasta_adi}, {randevu_tarihi} tarihinde saat {randevu_saati}\'de Dr. {doktor_adi} ile randevunuz bulunmaktadır. Lütfen randevunuzu unutmayınız.',
            'hours_before_appointment' => 24,
            'send_speed' => 10,
            'campaign_name' => 'Randevu Hatırlatma',
            'is_active' => true
        ];
        $this->editingConfig = null;
    }

    public function create()
    {
        $this->validate();
        
        $user = Auth::user();
        
        // Doctor ID belirleme
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;
        
        MessageAutomationConfig::create([
            'doctor_id' => $doctorId,
            'user_id' => $user->id,
            'api_token' => $this->newConfig['api_token'],
            'phone_number' => $this->newConfig['phone_number'],
            'message_template' => $this->newConfig['message_template'],
            'hours_before_appointment' => $this->newConfig['hours_before_appointment'],
            'send_speed' => $this->newConfig['send_speed'],
            'campaign_name' => $this->newConfig['campaign_name'],
            'is_active' => $this->newConfig['is_active'] ?? true
        ]);
        
        $this->resetForm();
        $this->showModal = false;
        
        session()->flash('message', 'Mesaj otomasyonu başarıyla eklendi.');
    }

    public function edit($configId)
    {
        $config = MessageAutomationConfig::findOrFail($configId);
        
        // Yetki kontrolü
        if (!$config->canEdit(Auth::user())) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }
        
        $this->editingConfig = $config->id;
        $this->newConfig = [
            'api_token' => $config->api_token,
            'phone_number' => $config->phone_number,
            'message_template' => $config->message_template,
            'hours_before_appointment' => $config->hours_before_appointment,
            'send_speed' => $config->send_speed,
            'campaign_name' => $config->campaign_name,
            'is_active' => $config->is_active
        ];
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();
        
        $config = MessageAutomationConfig::findOrFail($this->editingConfig);
        
        // Yetki kontrolü
        if (!$config->canEdit(Auth::user())) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }
        
        $config->update([
            'api_token' => $this->newConfig['api_token'],
            'phone_number' => $this->newConfig['phone_number'],
            'message_template' => $this->newConfig['message_template'],
            'hours_before_appointment' => $this->newConfig['hours_before_appointment'],
            'send_speed' => $this->newConfig['send_speed'],
            'campaign_name' => $this->newConfig['campaign_name'],
            'is_active' => $this->newConfig['is_active'] ?? true
        ]);
        
        $this->resetForm();
        $this->showModal = false;
        
        session()->flash('message', 'Mesaj otomasyonu başarıyla güncellendi.');
    }

    public function delete($configId)
    {
        $config = MessageAutomationConfig::findOrFail($configId);
        
        // Yetki kontrolü
        if (!$config->canDelete(Auth::user())) {
            session()->flash('error', 'Bu konfigürasyonu silme yetkiniz yok.');
            return;
        }
        
        $config->delete();
        
        session()->flash('message', 'Mesaj otomasyonu başarıyla silindi.');
    }

    public function toggleStatus($configId)
    {
        $config = MessageAutomationConfig::findOrFail($configId);
        
        // Yetki kontrolü
        if (!$config->canEdit(Auth::user())) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }
        
        $config->update(['is_active' => !$config->is_active]);
        
        $status = $config->is_active ? 'aktif' : 'pasif';
        session()->flash('message', "Mesaj otomasyonu {$status} duruma getirildi.");
    }

    /* testConnection kaldırıldı (Reg ID gereksiz olduğundan bağlantı testi özelliği devre dışı bırakıldı) */

    public function getConfigsProperty()
    {
        return MessageAutomationConfig::getConfigsForUser(Auth::user())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getLogsProperty()
    {
        return MessageAutomationLog::getLogsForUser(Auth::user())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getStatsProperty()
    {
        $user = Auth::user();
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        $service = new WaMessageService();
        $stats = $service->getDailyStats($doctorId);

        // Aktif konfigürasyon sayısını ekle ve görünüm için anahtarları normalize et
        $activeCount = MessageAutomationConfig::getActiveConfigs($doctorId)->count();

        return [
            'active_configs' => $activeCount,
            'today_sent' => $stats['today'] ?? 0,
            'month_sent' => $stats['this_month'] ?? 0,
            'success_rate' => $stats['success_rate'] ?? 0,
        ];
    }

    public function canCreate()
    {
        $user = Auth::user();
        return in_array($user->role, ['admin', 'doctor', 'secretary']);
    }

    public function render()
    {
        return view('livewire.message-automation');
    }

    public function fetchReports()
    {
        $this->reportsError = null;
        $this->reports = null;

        $user = Auth::user();
        $doctorId = $user->role === 'doctor' ? $user->id : $user->doctor_id;

        // Aktif bir konfigürasyon bul (API token alınacak)
        $config = MessageAutomationConfig::getActiveConfigs($doctorId)->first();
        if (!$config) {
            $this->reportsError = 'Önce bir WhatsApp konfigürasyonu eklemelisiniz.';
            return;
        }

        $service = new WaMessageService();
        $result = $service->getReports($this->reportsFilters, $config->api_token);

        if (is_array($result) && isset($result['error'])) {
            $this->reportsError = $result['error'];
        } else {
            $this->reports = $result;
        }
    }

    public function toggleReportDetails($index)
    {
        if ($this->expandedReportIndex === $index) {
            $this->expandedReportIndex = null;
        } else {
            $this->expandedReportIndex = $index;
        }
    }
}
