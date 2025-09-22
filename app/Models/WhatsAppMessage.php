<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    protected $table = 'whats_app_messages';
    
    protected $fillable = [
        'doctor_id',
        'whatsapp_config_id',
        'whatsapp_template_id',
        'recipient_phone',
        'recipient_name',
        'message_content',
        'template_variables',
        'status',
        'whatsapp_message_id',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'template_variables' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime'
    ];

    /**
     * Mesajın sahibi olan doktor
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Mesajın gönderildiği WhatsApp konfigürasyonu
     */
    public function config(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConfig::class, 'whatsapp_config_id');
    }

    /**
     * Mesajda kullanılan şablon
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whatsapp_template_id');
    }

    /**
     * Gönderilmiş mesajları getir
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Başarısız mesajları getir
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Bekleyen mesajları getir
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Doktora ait mesajları getir
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Mesaj durumu rengini getir
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'sent' => 'blue',
            'delivered' => 'green',
            'read' => 'green',
            'failed' => 'red',
            default => 'gray'
        };
    }

    /**
     * Mesaj durumu Türkçe açıklamasını getir
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Beklemede',
            'sent' => 'Gönderildi',
            'delivered' => 'Teslim Edildi',
            'read' => 'Okundu',
            'failed' => 'Başarısız',
            default => 'Bilinmiyor'
        };
    }
}
