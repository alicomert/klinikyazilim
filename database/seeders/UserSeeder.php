<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Ahmet Yılmaz',
                'email' => 'ahmet.yilmaz@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Ayşe Demir',
                'email' => 'ayse.demir@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hemşire Fatma Kaya',
                'email' => 'fatma.kaya@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'nurse',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Hemşire Zeynep Özkan',
                'email' => 'zeynep.ozkan@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'nurse',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sekreter Elif Çelik',
                'email' => 'elif.celik@klinik.com',
                'password' => Hash::make('password'),
                'role' => 'secretary',
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
        
        // Hemşire ve sekreterleri doktorlara ata
        $doctors = User::where('role', 'doctor')->get();
        
        if ($doctors->count() > 0) {
            // Hemşireleri doktorlara ata
            $nurses = User::where('role', 'nurse')->whereNull('doctor_id')->get();
            foreach ($nurses as $index => $nurse) {
                $doctor = $doctors->get($index % $doctors->count());
                $nurse->update(['doctor_id' => $doctor->id]);
            }
            
            // Sekreterleri doktorlara ata
            $secretaries = User::where('role', 'secretary')->whereNull('doctor_id')->get();
            foreach ($secretaries as $index => $secretary) {
                $doctor = $doctors->get($index % $doctors->count());
                $secretary->update(['doctor_id' => $doctor->id]);
            }
        }
    }
}