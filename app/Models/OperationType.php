<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationType extends Model
{
    protected $fillable = [
        'name',
        'value',
        'description',
        'is_active',
        'sort_order',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // İlişkiler
    public function operationDetails(): HasMany
    {
        return $this->hasMany(OperationDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc')->orderBy('name');
    }

    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('created_by', $doctorId);
    }

    // Accessor'lar
    public function getActiveDetailsAttribute()
    {
        return $this->operationDetails()->active()->ordered()->get();
    }
}
