<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\EncryptableFields;
use Carbon\Carbon;

class PatientNote extends Model
{
    use EncryptableFields;
    
    protected $fillable = [
        'patient_id',
        'user_id',
        'note_type',
        'content',
        'is_private',
        'note_date',
        'last_updated'
    ];
    
    /**
     * Şifrelenecek hassas alanlar
     */
    protected $encryptable = [
        'content'
    ];
    
    protected $casts = [
        'note_date' => 'datetime',
        'last_updated' => 'datetime',
        'is_private' => 'boolean'
    ];
    
    /**
     * Hasta ilişkisi
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Kullanıcı (notu yazan) ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Tıbbi notlar
     */
    public function scopeMedicalNotes($query)
    {
        return $query->where('note_type', 'medical');
    }
    
    /**
     * Genel notlar
     */
    public function scopeGeneralNotes($query)
    {
        return $query->where('note_type', 'general');
    }
    

    
    /**
     * Özel notlar (sadece yazanın görebileceği)
     */
    public function scopePrivateNotes($query)
    {
        return $query->where('is_private', true);
    }
    
    /**
     * Genel notlar (herkesin görebileceği)
     */
    public function scopePublicNotes($query)
    {
        return $query->where('is_private', false);
    }
    
    /**
     * Belirli bir kullanıcının notları
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Tarih aralığına göre notlar
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('note_date', [$startDate, $endDate]);
    }
    
    /**
     * Son notları getir
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('note_date', '>=', Carbon::now()->subDays($days));
    }
}
