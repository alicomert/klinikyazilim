<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\EncryptableFields;
use Carbon\Carbon;

class OperationNote extends Model
{
    use EncryptableFields;
    
    protected $fillable = [
        'operation_id',
        'doctor_id',
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
     * Operasyon ilişkisi
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }
    
    /**
     * Kullanıcı (notu yazan) ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Doktor ilişkisi
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
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

    /**
     * Belirli bir doktorun notları
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }
}
