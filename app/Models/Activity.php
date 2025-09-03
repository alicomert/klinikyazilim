<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'description',
        'patient_id',
    ];

    /**
     * Aktivitenin ait olduğu hasta
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
