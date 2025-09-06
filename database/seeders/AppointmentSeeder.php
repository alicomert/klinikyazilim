<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        
        if ($patients->count() === 0 || $doctors->count() === 0) {
            return;
        }

        $appointments = [
            [
                'appointment_date' => now()->addDays(1)->format('Y-m-d'),
                'appointment_time' => '09:00:00',
                'appointment_type' => 'consultation',
                'status' => 'scheduled',
                'notes' => 'Rutin kontrol muayenesi - Genel sağlık kontrolü',
                'doctor_name' => 'Dr. Ahmet Yılmaz',
            ],
            [
                'appointment_date' => now()->addDays(2)->format('Y-m-d'),
                'appointment_time' => '10:30:00',
                'appointment_type' => 'control',
                'status' => 'scheduled',
                'notes' => 'Diyabet kontrolü - Kan şekeri yüksekliği',
                'doctor_name' => 'Dr. Ayşe Demir',
            ],
            [
                'appointment_date' => now()->addDays(3)->format('Y-m-d'),
                'appointment_time' => '14:00:00',
                'appointment_type' => 'control',
                'status' => 'scheduled',
                'notes' => 'Hipertansiyon kontrolü - Tansiyon yüksekliği',
                'doctor_name' => 'Dr. Ahmet Yılmaz',
            ],
            [
                'appointment_date' => now()->addDays(4)->format('Y-m-d'),
                'appointment_time' => '11:15:00',
                'appointment_type' => 'consultation',
                'status' => 'scheduled',
                'notes' => 'Ameliyat öncesi değerlendirme - Apandisit şüphesi',
                'doctor_name' => 'Dr. Ayşe Demir',
            ],
            [
                'appointment_date' => now()->addDays(5)->format('Y-m-d'),
                'appointment_time' => '15:30:00',
                'appointment_type' => 'control',
                'status' => 'scheduled',
                'notes' => 'Kontrol muayenesi - Ameliyat sonrası kontrol',
                'doctor_name' => 'Dr. Ahmet Yılmaz',
            ],
            [
                'appointment_date' => now()->subDays(1)->format('Y-m-d'),
                'appointment_time' => '09:00:00',
                'appointment_type' => 'consultation',
                'status' => 'completed',
                'notes' => 'Muayene tamamlandı - Baş ağrısı',
                'doctor_name' => 'Dr. Ayşe Demir',
            ],
            [
                'appointment_date' => now()->subDays(2)->format('Y-m-d'),
                'appointment_time' => '13:45:00',
                'appointment_type' => 'consultation',
                'status' => 'completed',
                'notes' => 'Tedavi planı oluşturuldu - Eklem ağrısı',
                'doctor_name' => 'Dr. Ahmet Yılmaz',
            ],
            [
                'appointment_date' => now()->subDays(3)->format('Y-m-d'),
                'appointment_time' => '16:00:00',
                'appointment_type' => 'consultation',
                'status' => 'cancelled',
                'notes' => 'Hasta iptal etti - Mide ağrısı',
                'doctor_name' => 'Dr. Ayşe Demir',
            ]
        ];

        foreach ($appointments as $index => $appointmentData) {
            $patient = $patients->get($index % $patients->count());
            $doctor = $doctors->random();
            
            Appointment::create(array_merge($appointmentData, [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
            ]));
        }
    }
}