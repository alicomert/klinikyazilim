<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\EncryptableFields;
use Carbon\Carbon;

class Patient extends Model
{
    use EncryptableFields;
    
    protected $fillable = [
        'doctor_id',
        'first_name',
        'last_name',
        'tc_identity',
        'phone',
        'birth_date',
        'address',
        'medications',
        'allergies',
        'previous_operations',
        'complaints',
        'anamnesis',
        'physical_examination',
        'planned_operation',
        'chronic_conditions',
        'is_active',
        'last_visit'
    ];
    
    /**
     * Şifrelenecek hassas alanlar - Sadece kritik veriler
     */
    protected $encryptable = [
        'tc_identity',        // TC kimlik - yasal zorunluluk
        'medications',        // İlaç bilgileri - hassas tıbbi veri
        'allergies',          // Alerji bilgileri - kritik sağlık verisi
        'chronic_conditions'  // Kronik hastalıklar - hassas tıbbi veri
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'last_visit' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    /**
     * Hasta notları ilişkisi
     */
    public function notes(): HasMany
    {
        return $this->hasMany(PatientNote::class);
    }
    
    /**
     * Tıbbi notlar
     */
    public function medicalNotes(): HasMany
    {
        return $this->hasMany(PatientNote::class)->where('note_type', 'medical');
    }
    
    /**
     * Personel notları
     */
    public function staffNotes(): HasMany
    {
        return $this->hasMany(PatientNote::class)->where('note_type', 'staff');
    }
    
    /**
     * Hasta aktiviteleri
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Doktor ilişkisi
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    
    /**
     * Tam adı döndür
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    /**
     * Yaşı hesapla
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date ? $this->birth_date->age : 0;
    }
    
    /**
     * TC kimlik formatla
     */
    public function getFormattedTcAttribute(): string
    {
        $tc = $this->tc_identity;
        return substr($tc, 0, 3) . '***' . substr($tc, -2);
    }
    
    /**
     * Telefon formatla
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 2) . ' ' . substr($phone, 9, 2);
        }
        return $this->phone;
    }
    
    /**
     * Aktif hastaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * İsme göre arama
     */
    public function scopeSearchByName($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', '%' . $search . '%')
              ->orWhere('last_name', 'like', '%' . $search . '%');
        });
    }
    
    /**
     * TC kimlik ile arama
     */
    public function scopeSearchByTc($query, $tc)
    {
        return $query->where('tc_identity', 'like', '%' . $tc . '%');
    }
    
    /**
     * Telefon ile arama
     */
    public function scopeSearchByPhone($query, $phone)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        return $query->where('phone', 'like', '%' . $cleanPhone . '%');
    }

    /**
     * Doktora göre filtreleme
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Kullanıcının erişebileceği hastaları getir
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->isAdmin()) {
            return $query; // Admin tüm hastalara erişebilir
        }
        
        $doctorId = $user->getDoctorIdForFiltering();
        return $query->where('doctor_id', $doctorId);
    }
}
