<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'process',
        'process_detail',
        'process_date',
        'registration_period',
        'created_by'
    ];

    protected $casts = [
        'process_date' => 'date'
    ];

    // İlişkiler
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OperationNote::class);
    }

    // Scope'lar
    public function scopeByProcess($query, $process)
    {
        return $query->where('process', $process);
    }



    public function scopeByRegistrationPeriod($query, $period)
    {
        return $query->where('registration_period', $period);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // Accessor'lar
    public function getProcessLabelAttribute()
    {
        return match($this->process) {
            'surgery' => 'Ameliyat',
            'mesotherapy' => 'Mezoterapi',
            'botox' => 'Botoks',
            'filler' => 'Dolgu',
            default => $this->process
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Planlandı',
            'in_progress' => 'Devam Ediyor',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi',
            default => $this->status
        };
    }
}
