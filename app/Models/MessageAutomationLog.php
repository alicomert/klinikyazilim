<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MessageAutomationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_id',
        'appointment_id',
        'patient_id',
        'doctor_id',
        'user_id',
        'phone_number',
        'message_content',
        'status',
        'sent_at',
        'response_data',
        'error_message',
        'wamessage_report_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'response_data' => 'array'
    ];

    /**
     * Mesajın gönderildiği konfigürasyon
     */
    public function config()
    {
        return $this->belongsTo(MessageAutomationConfig::class, 'config_id');
    }

    /**
     * Mesajın gönderildiği randevu
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    /**
     * Mesajın gönderildiği hasta
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Mesajın sahibi olan doktor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Mesajı gönderen kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mesajın başarılı olup olmadığını kontrol eder
     */
    public function isSuccessful()
    {
        return $this->status === 'sent';
    }

    /**
     * Mesajın başarısız olup olmadığını kontrol eder
     */
    public function isFailed()
    {
        return in_array($this->status, ['failed', 'error']);
    }

    /**
     * Mesajın beklemede olup olmadığını kontrol eder
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Kullanıcının bu mesajı görüp göremeyeceğini kontrol eder
     */
    public function canView($user)
    {
        // Admin her şeyi görebilir
        if ($user->role === 'admin') {
            return true;
        }

        // Doktor sadece kendi mesajlarını görebilir
        if ($user->role === 'doctor') {
            return $this->doctor_id === $user->id;
        }

        // Sekreter sadece bağlı olduğu doktorun mesajlarını görebilir
        if ($user->role === 'secretary') {
            return $this->doctor_id === $user->doctor_id;
        }

        return false;
    }

    /**
     * Kullanıcı rolüne göre mesajları filtreler
     */
    public static function getLogsForUser($user)
    {
        $query = self::query();
        
        if ($user->role === 'admin') {
            // Admin tüm mesajları görebilir
            return $query->with(['config', 'appointment', 'patient', 'doctor', 'user']);
        } elseif ($user->role === 'doctor') {
            // Doktor sadece kendi mesajlarını görebilir
            return $query->where('doctor_id', $user->id)->with(['config', 'appointment', 'patient', 'doctor', 'user']);
        } elseif ($user->role === 'secretary') {
            // Sekreter sadece bağlı olduğu doktorun mesajlarını görebilir
            return $query->where('doctor_id', $user->doctor_id)->with(['config', 'appointment', 'patient', 'doctor', 'user']);
        }
        
        return $query->whereRaw('1 = 0'); // Boş sonuç döndür
    }

    /**
     * Bugünkü mesajları getirir
     */
    public static function getTodayLogs($doctorId = null)
    {
        $query = self::whereDate('sent_at', today());
        
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        
        return $query->count();
    }

    /**
     * Bu ayki mesajları getirir
     */
    public static function getThisMonthLogs($doctorId = null)
    {
        $query = self::whereMonth('sent_at', now()->month)
                    ->whereYear('sent_at', now()->year);
        
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        
        return $query->count();
    }

    /**
     * Başarı oranını hesaplar
     */
    public static function getSuccessRate($doctorId = null)
    {
        $query = self::query();
        
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        
        $total = $query->count();
        $successful = $query->where('status', 'sent')->count();
        
        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }
}