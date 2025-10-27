<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MessageAutomationConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'user_id',
        'api_token',
        'phone_number',
        'message_template',
        'hours_before_appointment',
        'is_active',
        'send_speed',
        'campaign_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hours_before_appointment' => 'integer',
        'send_speed' => 'integer'
    ];

    /**
     * Konfigürasyonun sahibi olan doktor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Konfigürasyonu oluşturan kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Bu konfigürasyonla gönderilen mesajlar
     */
    public function logs()
    {
        return $this->hasMany(MessageAutomationLog::class, 'config_id');
    }

    /**
     * Kullanıcının bu konfigürasyonu düzenleyip düzenleyemeyeceğini kontrol eder
     */
    public function canEdit($user)
    {
        // Admin her şeyi düzenleyebilir
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor sadece kendi konfigürasyonlarını düzenleyebilir
        if ($user->role === 'doctor') {
            return $this->doctor_id === $user->id;
        }

        // Sekreter sadece bağlı olduğu doktorun konfigürasyonlarını düzenleyebilir
        if ($user->role === 'secretary') {
            return $this->doctor_id === $user->doctor_id;
        }

        return false;
    }

    /**
     * Kullanıcının bu konfigürasyonu silebilip silemeyeceğini kontrol eder
     */
    public function canDelete($user)
    {
        return $this->canEdit($user);
    }

    /**
     * Mesaj şablonundaki değişkenleri randevu bilgileriyle değiştirir
     */
    public function processMessageTemplate($appointment)
    {
        $message = $this->message_template;
        
        // Değişkenleri değiştir
        $message = str_replace('{hasta_adi}', $appointment->patient->name, $message);
        $message = str_replace('{doktor_adi}', $appointment->doctor->name, $message);
        $message = str_replace('{randevu_tarihi}', $appointment->appointment_date->format('d.m.Y'), $message);
        $message = str_replace('{randevu_saati}', $appointment->appointment_date->format('H:i'), $message);
        
        return $message;
    }

    /**
     * Aktif konfigürasyonları getirir
     */
    public static function getActiveConfigs($doctorId = null)
    {
        $query = self::where('is_active', true);
        
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        
        return $query->get();
    }

    /**
     * Kullanıcı rolüne göre konfigürasyonları filtreler
     */
    public static function getConfigsForUser($user)
    {
        $query = self::query();
        
        if ($user->role === 'admin') {
            // Admin tüm konfigürasyonları görebilir
            return $query->with(['doctor', 'user']);
        } elseif ($user->role === 'doctor') {
            // Doktor sadece kendi konfigürasyonlarını görebilir
            return $query->where('doctor_id', $user->id)->with(['doctor', 'user']);
        } elseif ($user->role === 'secretary') {
            // Sekreter sadece bağlı olduğu doktorun konfigürasyonlarını görebilir
            return $query->where('doctor_id', $user->doctor_id)->with(['doctor', 'user']);
        }
        
        return $query->whereRaw('1 = 0'); // Boş sonuç döndür
    }
}