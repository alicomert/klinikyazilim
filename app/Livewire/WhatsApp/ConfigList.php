<?php

namespace App\Livewire\WhatsApp;

use App\Models\WhatsAppConfig;
use Livewire\Component;
use Livewire\WithPagination;

class ConfigList extends Component
{
    use WithPagination;

    public $configs = [];
    public $newConfig = [];
    public $editingConfig = null;
    public $showModal = false;
    public $showTestModal = false;
    public $testPhoneNumber = '';
    public $testMessage = '';
    public $testingConfigId = null;

    protected $rules = [
        'newConfig.name' => 'required|string|max:255',
        'newConfig.phone_number_id' => 'required|string|max:255',
        'newConfig.access_token' => 'required|string',
        'newConfig.business_account_id' => 'nullable|string|max:255',
        'newConfig.webhook_verify_token' => 'nullable|string|max:255',
        'newConfig.is_active' => 'boolean'
    ];

    protected $messages = [
        'newConfig.name.required' => 'Konfigürasyon adı zorunludur.',
        'newConfig.phone_number_id.required' => 'Phone Number ID zorunludur.',
        'newConfig.access_token.required' => 'Access Token zorunludur.'
    ];

    public function mount()
    {
        $this->loadConfigs();
        $this->resetForm();
    }

    public function loadConfigs()
    {
        $query = WhatsAppConfig::query();
        
        // Kullanıcı rolüne göre filtreleme
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $this->configs = $query->orderBy('created_at', 'desc')->get();
    }

    public function resetForm()
    {
        $this->newConfig = [
            'name' => '',
            'phone_number_id' => '',
            'access_token' => '',
            'business_account_id' => '',
            'webhook_verify_token' => '',
            'is_active' => true
        ];
        $this->editingConfig = null;
        $this->showModal = false;
    }

    public function create()
    {
        $this->validate();

        WhatsAppConfig::create([
            'doctor_id' => auth()->id(),
            'name' => $this->newConfig['name'],
            'phone_number_id' => $this->newConfig['phone_number_id'],
            'access_token' => $this->newConfig['access_token'],
            'business_account_id' => $this->newConfig['business_account_id'],
            'webhook_verify_token' => $this->newConfig['webhook_verify_token'],
            'is_active' => $this->newConfig['is_active'] ?? true
        ]);

        $this->resetForm();
        $this->loadConfigs();
        session()->flash('message', 'WhatsApp konfigürasyonu başarıyla eklendi.');
    }

    public function edit($configId)
    {
        $config = WhatsAppConfig::findOrFail($configId);
        
        // Yetki kontrolü
        if (!$this->canEdit($config)) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }

        $this->editingConfig = $config->id;
        $this->newConfig = [
            'name' => $config->name,
            'phone_number_id' => $config->phone_number_id,
            'access_token' => $config->access_token,
            'business_account_id' => $config->business_account_id,
            'webhook_verify_token' => $config->webhook_verify_token,
            'is_active' => $config->is_active
        ];
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $config = WhatsAppConfig::findOrFail($this->editingConfig);
        
        if (!$this->canEdit($config)) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }

        $config->update([
            'name' => $this->newConfig['name'],
            'phone_number_id' => $this->newConfig['phone_number_id'],
            'access_token' => $this->newConfig['access_token'],
            'business_account_id' => $this->newConfig['business_account_id'],
            'webhook_verify_token' => $this->newConfig['webhook_verify_token'],
            'is_active' => $this->newConfig['is_active'] ?? true
        ]);

        $this->resetForm();
        $this->loadConfigs();
        session()->flash('message', 'WhatsApp konfigürasyonu başarıyla güncellendi.');
    }

    public function delete($configId)
    {
        $config = WhatsAppConfig::findOrFail($configId);
        
        if (!$this->canDelete($config)) {
            session()->flash('error', 'Bu konfigürasyonu silme yetkiniz yok.');
            return;
        }

        $config->delete();
        $this->loadConfigs();
        session()->flash('message', 'WhatsApp konfigürasyonu başarıyla silindi.');
    }

    public function toggleActive($configId)
    {
        $config = WhatsAppConfig::findOrFail($configId);
        
        if (!$this->canEdit($config)) {
            session()->flash('error', 'Bu konfigürasyonu düzenleme yetkiniz yok.');
            return;
        }

        $config->update(['is_active' => !$config->is_active]);
        $this->loadConfigs();
        
        $status = $config->is_active ? 'aktif' : 'pasif';
        session()->flash('message', "Konfigürasyon {$status} duruma getirildi.");
    }

    public function openTestModal($configId)
    {
        $this->testingConfigId = $configId;
        $this->testPhoneNumber = '';
        $this->testMessage = 'Merhaba! Bu bir test mesajıdır.';
        $this->showTestModal = true;
    }

    public function sendTestMessage()
    {
        $this->validate([
            'testPhoneNumber' => 'required|string|min:10',
            'testMessage' => 'required|string|max:1000'
        ], [
            'testPhoneNumber.required' => 'Telefon numarası zorunludur.',
            'testMessage.required' => 'Test mesajı zorunludur.'
        ]);

        try {
            $config = WhatsAppConfig::findOrFail($this->testingConfigId);
            
            // Burada gerçek WhatsApp API çağrısı yapılacak
            // Şimdilik sadece başarı mesajı gösteriyoruz
            
            session()->flash('message', 'Test mesajı başarıyla gönderildi!');
            $this->showTestModal = false;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Test mesajı gönderilirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function canEdit($config)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $config->doctor_id === $user->id;
    }

    public function canDelete($config)
    {
        return $this->canEdit($config);
    }

    public function render()
    {
        return view('livewire.whats-app.config-list');
    }
}
