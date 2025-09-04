<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AddPatientModal extends Component
{
    public $showModal = false;
    public $patientId = null;
    public $isEditMode = false;
    
    protected $listeners = ['open-patient-modal' => 'openModal'];
    
    // Temel Bilgiler
    public $first_name = '';
    public $last_name = '';
    public $tc_identity = '';
    public $phone = '';
    public $birth_date = '';
    public $address = '';

    
    // Tıbbi Bilgiler
    public $medications = '';
    public $allergies = '';
    public $previous_operations = '';
    public $complaints = '';
    public $anamnesis = '';
    public $physical_examination = '';
    public $planned_operation = '';
    public $chronic_conditions = '';
    
    protected function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'address' => 'nullable|string',

            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'previous_operations' => 'nullable|string',
            'complaints' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'planned_operation' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
        ];
        
        if ($this->isEditMode) {
            $rules['tc_identity'] = 'required|string|size:11|unique:patients,tc_identity,' . $this->patientId;
        } else {
            $rules['tc_identity'] = 'required|string|size:11|unique:patients,tc_identity';
        }
        
        return $rules;
    }
    
    protected $messages = [
        'first_name.required' => 'Ad alanı zorunludur.',
        'last_name.required' => 'Soyad alanı zorunludur.',
        'tc_identity.required' => 'TC Kimlik No zorunludur.',
        'tc_identity.size' => 'TC Kimlik No 11 haneli olmalıdır.',
        'tc_identity.unique' => 'Bu TC Kimlik No zaten kayıtlı.',
        'phone.required' => 'Telefon numarası zorunludur.',
        'birth_date.required' => 'Doğum tarihi zorunludur.',
        'birth_date.before' => 'Doğum tarihi bugünden önce olmalıdır.',

    ];
    
    public function openModal($patientId = null)
    {
        $this->showModal = true;
        $this->patientId = $patientId;
        $this->isEditMode = !is_null($patientId);
        
        if ($this->isEditMode) {
            $this->loadPatientData();
        } else {
            $this->resetForm();
        }
    }
    
    public function loadPatientData()
    {
        $patient = Patient::find($this->patientId);
        
        if ($patient) {
            $this->first_name = $patient->first_name;
            $this->last_name = $patient->last_name;
            $this->tc_identity = $patient->tc_identity;
            $this->phone = $patient->phone;
            $this->birth_date = $patient->birth_date;
            $this->address = $patient->address;

            $this->medications = $patient->medications;
            $this->allergies = $patient->allergies;
            $this->previous_operations = $patient->previous_operations;
            $this->complaints = $patient->complaints;
            $this->anamnesis = $patient->anamnesis;
            $this->physical_examination = $patient->physical_examination;
            $this->planned_operation = $patient->planned_operation;
            $this->chronic_conditions = $patient->chronic_conditions;
        }
    }
    
    public function closeModal()
    {
        $this->showModal = false;
        $this->patientId = null;
        $this->isEditMode = false;
        $this->resetForm();
        $this->resetValidation();
    }
    
    public function mount()
    {

    }
    
    public function resetForm()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->tc_identity = '';
        $this->phone = '';
        $this->birth_date = '';
        $this->address = '';

        $this->medications = '';
        $this->allergies = '';
        $this->previous_operations = '';
        $this->complaints = '';
        $this->anamnesis = '';
        $this->physical_examination = '';
        $this->planned_operation = '';
        $this->chronic_conditions = '';
    }
    
    public function save()
    {
        // Validation kontrolü
        $validatedData = $this->validate();
        
        try {
            // TC Kimlik numarasını temizle
            $this->tc_identity = preg_replace('/[^0-9]/', '', $this->tc_identity);
            
            // Telefon numarasını temizle
            $this->phone = preg_replace('/[^0-9]/', '', $this->phone);
            
            // Hasta verilerini hazırla
            $patientData = [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'tc_identity' => $this->tc_identity,
                'phone' => $this->phone,
                'birth_date' => $this->birth_date,
                'address' => $this->address,

                'medications' => $this->medications,
                'allergies' => $this->allergies,
                'previous_operations' => $this->previous_operations,
                'complaints' => $this->complaints,
                'anamnesis' => $this->anamnesis,
                'physical_examination' => $this->physical_examination,
                'planned_operation' => $this->planned_operation,
                'chronic_conditions' => $this->chronic_conditions,
            ];
            
            if ($this->isEditMode) {
                // Hasta güncelleme
                $patient = Patient::find($this->patientId);
                if ($patient) {
                    $patient->update($patientData);
                    $this->dispatch('patient-updated');
                    $this->dispatch('show-toast', [
                        'type' => 'success',
                        'message' => 'Hasta bilgileri başarıyla güncellendi!'
                    ]);
                } else {
                    throw new \Exception('Hasta bulunamadı');
                }
            } else {
                // Yeni hasta ekleme
                $patientData['is_active'] = true;
                $patientData['last_visit'] = now();
                
                $patient = Patient::create($patientData);
                
                if ($patient) {
                    // Aktivite kaydı oluştur
                    Activity::create([
                        'type' => 'new_patient_registration',
                        'description' => 'Yeni hasta kaydı ' . $patient->full_name . ' - ' . $patient->age . ' yaş',
                        'patient_id' => $patient->id
                    ]);
                    
                    $this->dispatch('patient-added');
                    $this->dispatch('show-toast', [
                        'type' => 'success',
                        'message' => 'Hasta başarıyla kaydedildi! ID: ' . $patient->id
                    ]);
                } else {
                    throw new \Exception('Hasta kaydedilemedi');
                }
            }
            
            $this->closeModal();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Lütfen tüm zorunlu alanları doldurun ve hataları düzeltin.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Hata: ' . $e->getMessage()
            ]);
        }
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function render()
    {
        return view('livewire.add-patient-modal');
    }
}
