<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'patient_name',
        'patient_phone',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'notes',
        'status',
        'doctor_name',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function getPatientDisplayNameAttribute(): string
    {
        return $this->patient ? $this->patient->first_name . ' ' . $this->patient->last_name : $this->patient_name;
    }

    public function getPatientDisplayPhoneAttribute(): string
    {
        return $this->patient ? $this->patient->phone : $this->patient_phone;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'no_show' => 'yellow',
            default => 'gray'
        };
    }

    public function getAppointmentTypeColorAttribute(): string
    {
        return match($this->appointment_type) {
            'consultation' => 'green',
            'operation' => 'red',
            'control' => 'blue',
            'botox' => 'purple',
            'filler' => 'pink',
            default => 'gray'
        };
    }
}
