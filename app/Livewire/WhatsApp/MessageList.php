<?php

namespace App\Livewire\WhatsApp;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConfig;
use App\Models\WhatsAppTemplate;
use Carbon\Carbon;
use Exception;

class MessageList extends Component
{
    use WithPagination;

    // Public properties
    public $messagesList = [];
    public $configs = [];
    public $templates = [];
    public $newMessage = [];
    public $editingMessage = null;
    public $showModal = false;
    public $showDetailModal = false;
    public $selectedMessage = null;
    
    // Filters
    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $configFilter = '';
    public $templateFilter = '';

    // Validation rules
    protected $rules = [
        'newMessage.whatsapp_config_id' => 'required|exists:whatsapp_configs,id',
        'newMessage.whatsapp_template_id' => 'nullable|exists:whatsapp_templates,id',
        'newMessage.recipient_phone' => 'required|string|max:20',
        'newMessage.recipient_name' => 'required|string|max:255',
        'newMessage.message_content' => 'required|string',
        'newMessage.template_variables' => 'nullable|array'
    ];

    protected $validationAttributes = [
        'newMessage.whatsapp_config_id' => 'WhatsApp konfigürasyonu',
        'newMessage.whatsapp_template_id' => 'mesaj şablonu',
        'newMessage.recipient_phone' => 'alıcı telefon',
        'newMessage.recipient_name' => 'alıcı adı',
        'newMessage.message_content' => 'mesaj içeriği',
        'newMessage.template_variables' => 'şablon değişkenleri'
    ];

    protected $messages = [
        'newMessage.whatsapp_config_id.required' => 'WhatsApp konfigürasyonu seçimi zorunludur.',
        'newMessage.recipient_phone.required' => 'Alıcı telefon numarası zorunludur.',
        'newMessage.recipient_name.required' => 'Alıcı adı zorunludur.',
        'newMessage.message_content.required' => 'Mesaj içeriği zorunludur.'
    ];

    public function mount()
    {
        $this->loadMessages();
        $this->loadConfigs();
        $this->loadTemplates();
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.whats-app.message-list');
    }

    // Data loading methods
    public function loadMessages()
    {
        $query = WhatsAppMessage::with(['config', 'template', 'doctor']);
        
        // Role-based filtering
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('recipient_name', 'like', '%' . $this->search . '%')
                  ->orWhere('recipient_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('message_content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->configFilter) {
            $query->where('whatsapp_config_id', $this->configFilter);
        }

        if ($this->templateFilter) {
            $query->where('whatsapp_template_id', $this->templateFilter);
        }

        $this->messagesList = $query->orderBy('created_at', 'desc')->get();
    }

    public function loadConfigs()
    {
        $query = WhatsAppConfig::where('is_active', true);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $this->configs = $query->get();
    }

    public function loadTemplates()
    {
        $query = WhatsAppTemplate::where('is_active', true);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $this->templates = $query->get();
    }

    // CRUD Operations
    public function create()
    {
        try {
            $this->validate();

            WhatsAppMessage::create([
                'doctor_id' => auth()->id(),
                'whatsapp_config_id' => $this->newMessage['whatsapp_config_id'],
                'whatsapp_template_id' => $this->newMessage['whatsapp_template_id'] ?? null,
                'recipient_phone' => $this->newMessage['recipient_phone'],
                'recipient_name' => $this->newMessage['recipient_name'],
                'message_content' => $this->newMessage['message_content'],
                'template_variables' => $this->newMessage['template_variables'] ?? null,
                'status' => 'pending',
                'created_at' => now()
            ]);

            $this->resetForm();
            $this->loadMessages();
            $this->showModal = false;

            session()->flash('message', 'Mesaj başarıyla oluşturuldu.');

        } catch (Exception $e) {
            session()->flash('error', 'Mesaj oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function edit($messageId)
    {
        $message = WhatsAppMessage::findOrFail($messageId);
        
        // Yetki kontrolü
        if (!$this->canEdit($message)) {
            session()->flash('error', 'Bu mesajı düzenleme yetkiniz yok.');
            return;
        }

        $this->editingMessage = $message->id;
        $this->newMessage = [
            'whatsapp_config_id' => $message->whatsapp_config_id,
            'whatsapp_template_id' => $message->whatsapp_template_id,
            'recipient_phone' => $message->recipient_phone,
            'recipient_name' => $message->recipient_name,
            'message_content' => $message->message_content,
            'template_variables' => $message->template_variables
        ];
        $this->showModal = true;
    }

    public function update()
    {
        try {
            $this->validate();

            $message = WhatsAppMessage::findOrFail($this->editingMessage);
            
            if (!$this->canEdit($message)) {
                session()->flash('error', 'Bu mesajı düzenleme yetkiniz yok.');
                return;
            }

            $message->update([
                'whatsapp_config_id' => $this->newMessage['whatsapp_config_id'],
                'whatsapp_template_id' => $this->newMessage['whatsapp_template_id'] ?? null,
                'recipient_phone' => $this->newMessage['recipient_phone'],
                'recipient_name' => $this->newMessage['recipient_name'],
                'message_content' => $this->newMessage['message_content'],
                'template_variables' => $this->newMessage['template_variables'] ?? null,
                'updated_at' => now()
            ]);

            $this->resetEditForm();
            $this->loadMessages();

            session()->flash('message', 'Mesaj başarıyla güncellendi.');

        } catch (Exception $e) {
            session()->flash('error', 'Mesaj güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($messageId)
    {
        try {
            $message = WhatsAppMessage::findOrFail($messageId);
            
            if (!$this->canDelete($message)) {
                session()->flash('error', 'Bu mesajı silme yetkiniz yok.');
                return;
            }

            $message->delete();
            $this->loadMessages();

            session()->flash('message', 'Mesaj başarıyla silindi.');

        } catch (Exception $e) {
            session()->flash('error', 'Mesaj silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function showDetail($messageId)
    {
        $this->selectedMessage = WhatsAppMessage::with(['config', 'template', 'doctor'])->findOrFail($messageId);
        $this->showDetailModal = true;
    }

    // Permission methods
    public function canEdit($message)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $message->doctor_id === $user->id && in_array($message->status, ['pending', 'failed']);
    }

    public function canDelete($message)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $message->doctor_id === $user->id && in_array($message->status, ['pending', 'failed']);
    }

    // Helper methods
    public function resetForm()
    {
        $this->newMessage = [
            'whatsapp_config_id' => '',
            'whatsapp_template_id' => '',
            'recipient_phone' => '',
            'recipient_name' => '',
            'message_content' => '',
            'template_variables' => []
        ];
    }

    public function resetEditForm()
    {
        $this->editingMessage = null;
        $this->newMessage = [];
        $this->showModal = false;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->configFilter = '';
        $this->templateFilter = '';
        $this->loadMessages();
    }

    public function refreshMessages()
    {
        $this->loadMessages();
        session()->flash('message', 'Mesaj listesi yenilendi.');
    }

    // Computed properties
    public function getStatusOptionsProperty()
    {
        return [
            'pending' => 'Beklemede',
            'sent' => 'Gönderildi',
            'delivered' => 'Teslim Edildi',
            'read' => 'Okundu',
            'failed' => 'Başarısız'
        ];
    }
}