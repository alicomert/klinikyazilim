<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';
    
    protected $fillable = [
        'doctor_id',
        'name',
        'template_name',
        'language_code',
        'category',
        'components',
        'description',
        'status',
        'is_active',
        'is_approved',
        'variables'
    ];

    protected $casts = [
        'components' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_approved' => 'boolean'
    ];

    /**
     * Şablonun sahibi olan doktor
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Aktif şablonları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Onaylanmış şablonları getir
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Doktora ait şablonları getir
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Kategoriye göre şablonları getir
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Şablon durumu renklerini getir
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'PENDING' => 'yellow',
            'APPROVED' => 'green',
            'REJECTED' => 'red',
            'DISABLED' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Şablon durumu Türkçe açıklamasını getir
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'PENDING' => 'Beklemede',
            'APPROVED' => 'Onaylandı',
            'REJECTED' => 'Reddedildi',
            'DISABLED' => 'Devre Dışı',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Kategori Türkçe açıklamasını getir
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->category) {
            'MARKETING' => 'Pazarlama',
            'UTILITY' => 'Hizmet',
            'AUTHENTICATION' => 'Doğrulama',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Şablonun değişken sayısını getir
     */
    public function getVariableCountAttribute()
    {
        return is_array($this->variables) ? count($this->variables) : 0;
    }

    /**
     * Bu şablonla gönderilen mesajlar
     */
    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'whatsapp_template_id');
    }
}
