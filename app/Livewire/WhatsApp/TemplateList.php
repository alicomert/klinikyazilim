<?php

namespace App\Livewire\WhatsApp;

use Livewire\Component;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppConfig;
use Illuminate\Validation\ValidationException;
use Exception;

class TemplateList extends Component
{
    // Public properties
    public $templates = [];
    public $configs = [];
    public $newTemplate = [];
    public $editingTemplate = null;
    public $showModal = false;
    public $showPreviewModal = false;
    public $previewTemplate = null;
    public $previewVariables = [];

    // Validation rules
    protected $rules = [
        'newTemplate.name' => 'required|string|max:255',
        'newTemplate.category' => 'required|string|in:appointment,reminder,marketing,notification,other',
        'newTemplate.content' => 'required|string',
        'newTemplate.variables' => 'nullable|string',
        'newTemplate.description' => 'nullable|string',
        'newTemplate.is_active' => 'boolean',
        'newTemplate.is_approved' => 'boolean'
    ];

    protected $validationAttributes = [
        'newTemplate.name' => 'şablon adı',
        'newTemplate.category' => 'kategori',
        'newTemplate.content' => 'mesaj içeriği',
        'newTemplate.variables' => 'değişkenler',
        'newTemplate.description' => 'açıklama'
    ];

    protected $messages = [
        'newTemplate.name.required' => 'Şablon adı zorunludur.',
        'newTemplate.category.required' => 'Kategori seçimi zorunludur.',
        'newTemplate.content.required' => 'Mesaj içeriği zorunludur.',
        'newTemplate.category.in' => 'Geçersiz kategori seçimi.'
    ];

    public function mount()
    {
        $this->loadTemplates();
        $this->loadConfigs();
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.whats-app.template-list');
    }

    // Data loading methods
    public function loadTemplates()
    {
        $query = WhatsAppTemplate::with('doctor');
        
        // Role-based filtering
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $this->templates = $query->orderBy('created_at', 'desc')->get();
    }

    public function loadConfigs()
    {
        $query = WhatsAppConfig::where('is_active', true);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('doctor_id', auth()->id());
        }
        
        $this->configs = $query->get();
    }

    // CRUD operations
    public function create()
    {
        try {
            $this->validate();

            WhatsAppTemplate::create([
                'name' => $this->newTemplate['name'],
                'category' => $this->newTemplate['category'],
                'content' => $this->newTemplate['content'],
                'variables' => $this->newTemplate['variables'] ?? null,
                'description' => $this->newTemplate['description'] ?? null,
                'is_active' => $this->newTemplate['is_active'] ?? true,
                'is_approved' => auth()->user()->role === 'admin' ? ($this->newTemplate['is_approved'] ?? false) : false,
                'doctor_id' => auth()->id(),
                'created_at' => now()
            ]);

            $this->resetForm();
            $this->loadTemplates();
            $this->showModal = false;

            session()->flash('message', 'Şablon başarıyla oluşturuldu.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            session()->flash('error', 'Şablon oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function edit($templateId)
    {
        $template = WhatsAppTemplate::findOrFail($templateId);
        
        if (!$this->canEdit($template)) {
            session()->flash('error', 'Bu şablonu düzenleme yetkiniz yok.');
            return;
        }

        $this->editingTemplate = $template->id;
        $this->newTemplate = [
            'name' => $template->name,
            'category' => $template->category,
            'content' => $template->content,
            'variables' => $template->variables,
            'description' => $template->description,
            'is_active' => $template->is_active,
            'is_approved' => $template->is_approved
        ];
        $this->showModal = true;
    }

    public function update()
    {
        try {
            $this->validate();

            $template = WhatsAppTemplate::findOrFail($this->editingTemplate);
            
            if (!$this->canEdit($template)) {
                session()->flash('error', 'Bu şablonu düzenleme yetkiniz yok.');
                return;
            }

            $updateData = [
                'name' => $this->newTemplate['name'],
                'category' => $this->newTemplate['category'],
                'content' => $this->newTemplate['content'],
                'variables' => $this->newTemplate['variables'] ?? null,
                'description' => $this->newTemplate['description'] ?? null,
                'is_active' => $this->newTemplate['is_active'] ?? true,
                'updated_at' => now()
            ];

            // Only admin can change approval status
            if (auth()->user()->role === 'admin') {
                $updateData['is_approved'] = $this->newTemplate['is_approved'] ?? false;
            }

            $template->update($updateData);

            $this->resetEditForm();
            $this->loadTemplates();

            session()->flash('message', 'Şablon başarıyla güncellendi.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            session()->flash('error', 'Şablon güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($templateId)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($templateId);
            
            if (!$this->canDelete($template)) {
                session()->flash('error', 'Bu şablonu silme yetkiniz yok.');
                return;
            }

            $template->delete();
            $this->loadTemplates();

            session()->flash('message', 'Şablon başarıyla silindi.');

        } catch (Exception $e) {
            session()->flash('error', 'Şablon silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function toggleActive($templateId)
    {
        try {
            $template = WhatsAppTemplate::findOrFail($templateId);
            
            if (!$this->canEdit($template)) {
                session()->flash('error', 'Bu şablonu düzenleme yetkiniz yok.');
                return;
            }

            $template->update([
                'is_active' => !$template->is_active,
                'updated_at' => now()
            ]);

            $this->loadTemplates();

            $status = $template->is_active ? 'aktifleştirildi' : 'pasifleştirildi';
            session()->flash('message', "Şablon başarıyla {$status}.");

        } catch (Exception $e) {
            session()->flash('error', 'Şablon durumu değiştirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function toggleApproval($templateId)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                session()->flash('error', 'Şablon onaylama yetkiniz yok.');
                return;
            }

            $template = WhatsAppTemplate::findOrFail($templateId);
            
            $template->update([
                'is_approved' => !$template->is_approved,
                'updated_at' => now()
            ]);

            $this->loadTemplates();

            $status = $template->is_approved ? 'onaylandı' : 'onayı kaldırıldı';
            session()->flash('message', "Şablon başarıyla {$status}.");

        } catch (Exception $e) {
            session()->flash('error', 'Şablon onay durumu değiştirilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    // Preview functionality
    public function preview($templateId)
    {
        $template = WhatsAppTemplate::findOrFail($templateId);
        $this->previewTemplate = $template;
        $this->previewVariables = [];
        
        // Extract variables from template content
        if ($template->variables) {
            $variables = explode(',', $template->variables);
            foreach ($variables as $variable) {
                $this->previewVariables[trim($variable)] = '';
            }
        }
        
        $this->showPreviewModal = true;
    }

    public function getPreviewContent()
    {
        if (!$this->previewTemplate) {
            return '';
        }

        $content = $this->previewTemplate->content;
        
        foreach ($this->previewVariables as $variable => $value) {
            $content = str_replace('{{' . $variable . '}}', $value ?: '[' . $variable . ']', $content);
        }
        
        return $content;
    }

    // Form management
    public function resetForm()
    {
        $this->newTemplate = [
            'name' => '',
            'category' => 'appointment',
            'content' => '',
            'variables' => '',
            'description' => '',
            'is_active' => true,
            'is_approved' => false
        ];
    }

    public function resetEditForm()
    {
        $this->editingTemplate = null;
        $this->newTemplate = [];
        $this->showModal = false;
    }

    // Permission methods
    public function canCreate()
    {
        $user = auth()->user();
        return in_array($user->role, ['admin', 'doctor', 'nurse', 'secretary']);
    }

    public function canEdit($template)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $template->doctor_id === $user->id;
    }

    public function canDelete($template)
    {
        return $this->canEdit($template);
    }

    public function canApprove()
    {
        return auth()->user()->role === 'admin';
    }

    // Helper methods
    public function getCategoryOptions()
    {
        return [
            'appointment' => 'Randevu',
            'reminder' => 'Hatırlatma',
            'marketing' => 'Pazarlama',
            'notification' => 'Bildirim',
            'other' => 'Diğer'
        ];
    }

    public function getVariableHelp()
    {
        return [
            '{{patient_name}}' => 'Hasta adı',
            '{{doctor_name}}' => 'Doktor adı',
            '{{appointment_date}}' => 'Randevu tarihi',
            '{{appointment_time}}' => 'Randevu saati',
            '{{clinic_name}}' => 'Klinik adı',
            '{{clinic_phone}}' => 'Klinik telefonu',
            '{{operation_name}}' => 'Operasyon adı',
            '{{payment_amount}}' => 'Ödeme tutarı'
        ];
    }
}
