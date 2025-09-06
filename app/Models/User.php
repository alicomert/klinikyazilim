<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a doctor
     */
    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    /**
     * Check if user is a nurse
     */
    public function isNurse(): bool
    {
        return $this->role === 'nurse';
    }

    /**
     * Check if user is a secretary
     */
    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'admin' => 'Admin',
            'doctor' => 'Doktor',
            'nurse' => 'Hemşire',
            'secretary' => 'Sekreter',
            default => 'Bilinmeyen'
        };
    }

    /**
     * Doctor relationship (for nurses and secretaries)
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Staff relationship (nurses and secretaries under this doctor)
     */
    public function staff()
    {
        return $this->hasMany(User::class, 'doctor_id');
    }

    /**
     * Get all patients for this doctor or staff member
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    /**
     * Get the doctor ID for filtering data
     * Returns own ID if doctor, or doctor_id if staff
     */
    public function getDoctorIdForFiltering()
    {
        if ($this->isDoctor()) {
            return $this->id;
        }
        
        return $this->doctor_id;
    }

    /**
     * Check if user can access doctor's data
     */
    public function canAccessDoctorData($doctorId)
    {
        if ($this->isAdmin()) {
            return true;
        }
        
        if ($this->isDoctor()) {
            return $this->id == $doctorId;
        }
        
        return $this->doctor_id == $doctorId;
    }

    /**
     * Scope to get users by doctor
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where(function($q) use ($doctorId) {
            $q->where('id', $doctorId)
              ->orWhere('doctor_id', $doctorId);
        });
    }
}
