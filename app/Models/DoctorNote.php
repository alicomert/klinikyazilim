<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\EncryptableFields;
use Carbon\Carbon;

class DoctorNote extends Model
{
    use EncryptableFields;
    
    protected $fillable = [
        'user_id',
        'doctor_id',
        'title',
        'content',
        'note_type',
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
     * Genel notlar
     */
    public function scopeGeneralNotes($query)
    {
        return $query->where('note_type', 'general');
    }
    
    /**
     * Hatırlatma notları
     */
    public function scopeReminderNotes($query)
    {
        return $query->where('note_type', 'reminder');
    }
    
    /**
     * Önemli notlar
     */
    public function scopeImportantNotes($query)
    {
        return $query->where('note_type', 'important');
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
    
    /**
     * Kullanıcının erişebileceği notları getir
     */
    public function scopeAccessibleBy($query, $user)
    {
        if ($user->isAdmin()) {
            return $query; // Admin tüm notlara erişebilir
        }
        
        $doctorId = $user->getDoctorIdForFiltering();
        
        return $query->where('doctor_id', $doctorId);
    }
    
    /**
     * Kullanıcının görebileceği notları getir (private/public kontrolü ile)
     */
    public function scopeVisibleTo($query, $user)
    {
        return $query->where(function($q) use ($user) {
            // Public notları göster
            $q->where('is_private', false)
                // Veya kullanıcının kendi private notlarını göster
                ->orWhere(function($subQuery) use ($user) {
                    $subQuery->where('is_private', true)
                            ->where('user_id', $user->id);
                })
                // Veya doktor olmayan kullanıcılar için aynı doktora bağlı private notları göster
                ->orWhere(function($subQuery) use ($user) {
                    if ($user->role !== 'doctor' && $user->doctor_id) {
                        $subQuery->where('is_private', true)
                                ->where('doctor_id', $user->doctor_id);
                    }
                });
        });
    }
}