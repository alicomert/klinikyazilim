<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Activity;
use App\Models\User;
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
    public $registration_date = '';
    public $needs_paid = '';
    
    // Ödeme Bilgileri
    public $payments = [];
    public $newPayment = [
        'payment_method' => 'nakit',
        'paid_amount' => '',
        'notes' => ''
    ];

    
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
            'registration_date' => 'required|date',
            'needs_paid' => 'nullable|numeric|min:0',
            'payments.*.payment_method' => 'required|in:nakit,kredi_karti,banka_havalesi,pos,diger',
            'payments.*.paid_amount' => 'required|numeric|min:0.01',
            'payments.*.notes' => 'nullable|string|max:500',

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
        'needs_paid.numeric' => 'Alınacak ücret sayısal olmalıdır.',
        'needs_paid.min' => 'Alınacak ücret 0 veya daha büyük olmalıdır.',
        'payments.*.payment_method.required' => 'Ödeme yöntemi seçilmelidir.',
        'payments.*.paid_amount.required' => 'Ödenen tutar girilmelidir.',
        'payments.*.paid_amount.numeric' => 'Ödenen tutar sayısal olmalıdır.',
        'payments.*.paid_amount.min' => 'Ödenen tutar 0.01 veya daha büyük olmalıdır.',

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
        $user = Auth::user();
        $patient = Patient::accessibleBy($user)->find($this->patientId);
        
        if ($patient) {
            $this->first_name = $patient->first_name;
            $this->last_name = $patient->last_name;
            $this->tc_identity = $patient->tc_identity;
            $this->phone = $patient->phone;
            $this->birth_date = $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '';
            $this->address = $patient->address;
            $this->registration_date = $patient->registration_date ? $patient->registration_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
            $this->needs_paid = $patient->needs_paid;
            
            // Mevcut ödemeleri yükle
            $this->payments = $patient->payments->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'payment_method' => $payment->payment_method,
                    'paid_amount' => $payment->paid_amount,
                    'notes' => $payment->notes,
                    'created_at' => $payment->created_at->format('d.m.Y H:i')
                ];
            })->toArray();

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
        $this->registration_date = now()->format('Y-m-d\TH:i');
        $this->needs_paid = '';
        
        // Ödeme bilgilerini sıfırla
        $this->payments = [];
        $this->newPayment = [
            'payment_method' => 'nakit',
            'paid_amount' => '',
            'notes' => ''
        ];

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
                'registration_date' => $this->registration_date,
                'needs_paid' => $this->needs_paid,

                'medications' => $this->medications,
                'allergies' => $this->allergies,
                'previous_operations' => $this->previous_operations,
                'complaints' => $this->complaints,
                'anamnesis' => $this->anamnesis,
                'physical_examination' => $this->physical_examination,
                'planned_operation' => $this->planned_operation,
                'chronic_conditions' => $this->chronic_conditions,
            ];
            
            $user = Auth::user();
            
            if ($this->isEditMode) {
                // Hasta güncelleme - erişim kontrolü
                $patient = Patient::accessibleBy($user)->find($this->patientId);
                if ($patient) {
                    $patient->update($patientData);
                    
                    // Yeni ödemeleri kaydet (sadece yeni eklenenler)
                    foreach ($this->payments as $payment) {
                        if (!isset($payment['id'])) {
                            $patient->payments()->create([
                                'user_id' => $user->id,
                                'payment_method' => $payment['payment_method'],
                                'paid_amount' => $payment['paid_amount'],
                                'notes' => $payment['notes']
                            ]);
                        }
                    }
                    
                    $this->dispatch('patient-updated');
                    $this->dispatch('show-toast', [
                        'type' => 'success',
                        'message' => 'Hasta bilgileri başarıyla güncellendi!'
                    ]);
                } else {
                    throw new \Exception('Hasta bulunamadı veya erişim yetkiniz yok');
                }
            } else {
                // Yeni hasta ekleme - doktor ID'si ata
                $patientData['is_active'] = true;
                $patientData['last_visit'] = now();
                $patientData['doctor_id'] = $user->getDoctorIdForFiltering();
                
                $patient = Patient::create($patientData);
                
                if ($patient) {
                    // Ödemeleri kaydet
                    foreach ($this->payments as $payment) {
                        $patient->payments()->create([
                            'user_id' => $user->id,
                            'payment_method' => $payment['payment_method'],
                            'paid_amount' => $payment['paid_amount'],
                            'notes' => $payment['notes']
                        ]);
                    }
                    
                    // Aktivite kaydı oluştur
                    Activity::create([
                        'type' => 'new_patient_registration',
                        'description' => 'Yeni hasta kaydı ' . $patient->full_name . ' - ' . $patient->age . ' yaş',
                        'patient_id' => $patient->id,
                        'doctor_id' => $this->getDoctorIdForFiltering()
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
        // needs_paid alanı için özel işlem
        if ($propertyName === 'needs_paid') {
            // Boş değerleri 0 yap
            if ($this->needs_paid === null || $this->needs_paid === '') {
                $this->needs_paid = 0;
            }
        }
        
        $this->validateOnly($propertyName);
    }
    
    public function addPayment()
    {
        // Yeni ödeme alanları boş değilse ekle
        if (!empty($this->newPayment['paid_amount']) && $this->newPayment['paid_amount'] > 0) {
            $this->payments[] = [
                'payment_method' => $this->newPayment['payment_method'],
                'paid_amount' => $this->newPayment['paid_amount'],
                'notes' => $this->newPayment['notes']
            ];
            
            // Yeni ödeme formunu sıfırla
            $this->newPayment = [
                'payment_method' => 'nakit',
                'paid_amount' => '',
                'notes' => ''
            ];
        }
    }
    
    public function removePayment($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments); // Dizini yeniden düzenle
    }
    
    public function getTotalPaidProperty()
    {
        return collect($this->payments)->sum('paid_amount');
    }
    
    public function getRemainingAmountProperty()
    {
        $needsPaid = (float) $this->needs_paid;
        $totalPaid = $this->getTotalPaidProperty();
        return max(0, $needsPaid - $totalPaid);
    }
    
    private function getDoctorIdForFiltering()
    {
        $user = Auth::user();
        if ($user->role === 'doctor') {
            return $user->id;
        } elseif ($user->doctor_id) {
            return $user->doctor_id;
        }
        return null;
    }
    
    public function render()
    {
        return view('livewire.add-patient-modal');
    }
}
