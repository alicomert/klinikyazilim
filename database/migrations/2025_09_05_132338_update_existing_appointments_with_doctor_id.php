<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Appointment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mevcut appointments verilerini güncelle
        // Eğer sistemde sadece bir doktor varsa, tüm appointments'ları ona ata
        $doctors = User::where('role', 'doctor')->get();
        
        if ($doctors->count() === 1) {
            // Tek doktor varsa, tüm appointments'ları ona ata
            $doctor = $doctors->first();
            DB::table('appointments')
                ->whereNull('doctor_id')
                ->update(['doctor_id' => $doctor->id]);
        } else {
            // Birden fazla doktor varsa, admin tarafından manuel atama gerekebilir
            // Bu durumda appointments'lar null olarak kalabilir
            // veya varsayılan bir doktora atanabilir
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi - doctor_id'leri null yap
        DB::table('appointments')->update(['doctor_id' => null]);
    }
};
