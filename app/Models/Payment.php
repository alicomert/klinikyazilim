<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\EncryptableFields;

class Payment extends Model
{
    use HasFactory, EncryptableFields;

    protected $fillable = [
        'user_id',
        'patient_id',
        'payment_method',
        'paid_amount',
        'notes'
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $encryptable = [
        'notes'
    ];

    /**
     * Ödemeyi yapan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ödeme yapılan hasta
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Ödeme yöntemi etiketleri
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'nakit' => 'Nakit',
            'kredi_karti' => 'Kredi Kartı',
            'banka_havalesi' => 'Banka Havalesi',
            'pos' => 'POS',
            'diger' => 'Diğer',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Kullanıcı rolüne göre erişim kontrolü
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->role === 'admin') {
            return $query;
        }
        
        if ($user->role === 'doctor') {
            return $query->whereHas('patient', function($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }
        
        return $query->where('user_id', $user->id);
    }
}