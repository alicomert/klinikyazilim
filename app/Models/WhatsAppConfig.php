<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConfig extends Model
{
    protected $table = 'whatsapp_configs';
    
    protected $fillable = [
        'doctor_id',
        'name',
        'phone_number_id',
        'access_token',
        'business_account_id',
        'webhook_verify_token',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array'
    ];

    /**
     * WhatsApp konfigürasyonunun sahibi olan doktor
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Aktif konfigürasyonları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Doktora ait konfigürasyonları getir
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Access token'ı şifreli olarak sakla
     */
    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = encrypt($value);
    }

    /**
     * Access token'ı çözümle
     */
    public function getAccessTokenAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Webhook verify token'ı şifreli olarak sakla
     */
    public function setWebhookVerifyTokenAttribute($value)
    {
        $this->attributes['webhook_verify_token'] = encrypt($value);
    }

    /**
     * Webhook verify token'ı çözümle
     */
    public function getWebhookVerifyTokenAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    /**
     * Bu konfigürasyonla gönderilen mesajlar
     */
    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'whatsapp_config_id');
    }
}
