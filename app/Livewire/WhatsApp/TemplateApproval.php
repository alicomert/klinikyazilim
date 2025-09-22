<?php

namespace App\Livewire\WhatsApp;

use Livewire\Component;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TemplateApproval extends Component
{
    public $templates = [];
    public $configs = [];
    public $selectedConfig = '';
    public $newTemplate = [
        'name' => '',
        'category' => 'MARKETING',
        'language' => 'tr',
        'header_type' => 'TEXT',
        'header_text' => '',
        'body_text' => '',
        'footer_text' => '',
        'buttons' => []
    ];
    public $showModal = false;
    public $editingTemplate = null;
    public $approvalStatus = [];

    protected $rules = [
        'newTemplate.name' => 'required|string|max:512|regex:/^[a-z0-9_]+$/',
        'newTemplate.category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
        'newTemplate.language' => 'required|string|max:10',
        'newTemplate.body_text' => 'required|string|max:1024',
        'newTemplate.header_text' => 'nullable|string|max:60',
        'newTemplate.footer_text' => 'nullable|string|max:60',
        'selectedConfig' => 'required|exists:whats_app_configs,id'
    ];

    protected $validationAttributes = [
        'newTemplate.name' => 'şablon adı',
        'newTemplate.category' => 'kategori',
        'newTemplate.language' => 'dil',
        'newTemplate.body_text' => 'mesaj içeriği',
        'newTemplate.header_text' => 'başlık metni',
        'newTemplate.footer_text' => 'alt metin',
        'selectedConfig' => 'konfigürasyon'
    ];

    protected $messages = [
        'newTemplate.name.regex' => 'Şablon adı sadece küçük harf, rakam ve alt çizgi içerebilir.',
        'newTemplate.name.required' => 'Şablon adı zorunludur.',
        'newTemplate.body_text.required' => 'Mesaj içeriği zorunludur.',
        'newTemplate.body_text.max' => 'Mesaj içeriği en fazla 1024 karakter olabilir.',
        'newTemplate.header_text.max' => 'Başlık metni en fazla 60 karakter olabilir.',
        'newTemplate.footer_text.max' => 'Alt metin en fazla 60 karakter olabilir.'
    ];

    public function mount()
    {
        $this->loadTemplates();
        $this->loadConfigs();
        $this->resetForm();
    }

    public function loadTemplates()
    {
        $user = auth()->user();
        
        $query = WhatsAppTemplate::with(['config', 'doctor']);
        
        if ($user->role !== 'admin') {
            $query->where('doctor_id', $user->id);
        }
        
        $this->templates = $query->orderBy('created_at', 'desc')->get();
    }

    public function loadConfigs()
    {
        $user = auth()->user();
        
        $query = WhatsAppConfig::where('is_active', true);
        
        if ($user->role !== 'admin') {
            $query->where('doctor_id', $user->id);
        }
        
        $this->configs = $query->get();
    }

    public function create()
    {
        $this->validate();

        try {
            $config = WhatsAppConfig::findOrFail($this->selectedConfig);
            
            // Meta API'ye şablon gönder
            $response = $this->submitTemplateToMeta($config);
            
            if ($response['success']) {
                // Veritabanına kaydet
                $template = WhatsAppTemplate::create([
                    'name' => $this->newTemplate['name'],
                    'category' => $this->newTemplate['category'],
                    'language' => $this->newTemplate['language'],
                    'header_type' => $this->newTemplate['header_type'],
                    'header_text' => $this->newTemplate['header_text'],
                    'body_text' => $this->newTemplate['body_text'],
                    'footer_text' => $this->newTemplate['footer_text'],
                    'buttons' => json_encode($this->newTemplate['buttons']),
                    'status' => 'PENDING',
                    'meta_template_id' => $response['template_id'] ?? null,
                    'config_id' => $this->selectedConfig,
                    'doctor_id' => auth()->id()
                ]);

                session()->flash('message', 'Şablon başarıyla oluşturuldu ve Meta onayına gönderildi.');
                $this->resetForm();
                $this->loadTemplates();
                $this->showModal = false;
            } else {
                session()->flash('error', 'Şablon Meta API\'ye gönderilemedi: ' . $response['error']);
            }

        } catch (\Exception $e) {
            Log::error('Template creation error: ' . $e->getMessage());
            session()->flash('error', 'Şablon oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function submitTemplateToMeta($config)
    {
        try {
            $components = [];

            // Header component
            if (!empty($this->newTemplate['header_text'])) {
                $components[] = [
                    'type' => 'HEADER',
                    'format' => 'TEXT',
                    'text' => $this->newTemplate['header_text']
                ];
            }

            // Body component
            $components[] = [
                'type' => 'BODY',
                'text' => $this->newTemplate['body_text']
            ];

            // Footer component
            if (!empty($this->newTemplate['footer_text'])) {
                $components[] = [
                    'type' => 'FOOTER',
                    'text' => $this->newTemplate['footer_text']
                ];
            }

            // Buttons component
            if (!empty($this->newTemplate['buttons'])) {
                $components[] = [
                    'type' => 'BUTTONS',
                    'buttons' => $this->newTemplate['buttons']
                ];
            }

            $payload = [
                'name' => $this->newTemplate['name'],
                'category' => $this->newTemplate['category'],
                'language' => $this->newTemplate['language'],
                'components' => $components
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $config->access_token,
                'Content-Type' => 'application/json'
            ])->post("https://graph.facebook.com/v18.0/{$config->waba_id}/message_templates", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'template_id' => $data['id'] ?? null
                ];
            } else {
                $error = $response->json();
                return [
                    'success' => false,
                    'error' => $error['error']['message'] ?? 'Bilinmeyen hata'
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkApprovalStatus($templateId)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($templateId);
            $config = $template->config;

            if (!$config || !$template->meta_template_id) {
                session()->flash('error', 'Şablon bilgileri eksik.');
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $config->access_token
            ])->get("https://graph.facebook.com/v18.0/{$template->meta_template_id}");

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'UNKNOWN';
                
                $template->update(['status' => $status]);
                
                $this->approvalStatus[$templateId] = [
                    'status' => $status,
                    'checked_at' => now()->format('H:i:s')
                ];

                $this->loadTemplates();
                
                session()->flash('message', "Şablon durumu güncellendi: {$status}");
            } else {
                session()->flash('error', 'Durum kontrolü yapılamadı.');
            }

        } catch (\Exception $e) {
            Log::error('Template status check error: ' . $e->getMessage());
            session()->flash('error', 'Durum kontrolü sırasında hata oluştu.');
        }
    }

    public function delete($templateId)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($templateId);
            
            // Yetki kontrolü
            if (!$this->canDelete($template)) {
                session()->flash('error', 'Bu şablonu silme yetkiniz yok.');
                return;
            }

            $template->delete();
            $this->loadTemplates();
            
            session()->flash('message', 'Şablon başarıyla silindi.');

        } catch (\Exception $e) {
            session()->flash('error', 'Şablon silinirken hata oluştu.');
        }
    }

    public function canDelete($template)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $template->user_id === $user->id;
    }

    public function resetForm()
    {
        $this->newTemplate = [
            'name' => '',
            'category' => 'MARKETING',
            'language' => 'tr',
            'header_type' => 'TEXT',
            'header_text' => '',
            'body_text' => '',
            'footer_text' => '',
            'buttons' => []
        ];
        $this->selectedConfig = '';
        $this->editingTemplate = null;
    }

    public function render()
    {
        return view('livewire.whats-app.template-approval');
    }
}