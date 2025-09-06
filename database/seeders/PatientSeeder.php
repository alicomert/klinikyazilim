<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = User::where('role', 'doctor')->get();
        
        $patients = [
            [
                'first_name' => 'Mehmet',
                'last_name' => 'Özdemir',
                'tc_identity' => '11111111111',
                'phone' => '0532 123 4567',
                'address' => 'Ankara, Çankaya',
                'birth_date' => '1985-03-15',
                'medications' => 'Hipertansiyon ilacı',
                'allergies' => 'Penisilin alerjisi',
                'complaints' => 'Karın ağrısı',
            ],
            [
                'first_name' => 'Fatma',
                'last_name' => 'Yıldız',
                'tc_identity' => '22222222222',
                'phone' => '0541 234 5678',
                'address' => 'İstanbul, Kadıköy',
                'birth_date' => '1992-07-22',
                'medications' => 'Vitamin D',
                'allergies' => 'Bilinen alerji yok',
                'complaints' => 'Baş ağrısı',
            ],
            [
                'first_name' => 'Ali',
                'last_name' => 'Kaya',
                'tc_identity' => '33333333333',
                'phone' => '0555 345 6789',
                'address' => 'İzmir, Konak',
                'birth_date' => '1978-11-08',
                'medications' => 'Diyabet ilacı',
                'allergies' => 'Aspirin alerjisi',
                'complaints' => 'Diz ağrısı',
            ],
            [
                'first_name' => 'Zeynep',
                'last_name' => 'Demir',
                'tc_identity' => '44444444444',
                'phone' => '0533 456 7890',
                'address' => 'Bursa, Nilüfer',
                'birth_date' => '1995-01-30',
                'medications' => 'Bilinen ilaç yok',
                'allergies' => 'Bilinen alerji yok',
                'complaints' => 'Göz ağrısı',
            ],
            [
                'first_name' => 'Mustafa',
                'last_name' => 'Çelik',
                'tc_identity' => '55555555555',
                'phone' => '0544 567 8901',
                'address' => 'Antalya, Muratpaşa',
                'birth_date' => '1988-09-12',
                'medications' => 'Kalp ilacı',
                'allergies' => 'Bilinen alerji yok',
                'complaints' => 'Nefes darlığı',
            ]
        ];

        foreach ($patients as $patientData) {
            $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
            
            Patient::firstOrCreate(
                ['tc_identity' => $patientData['tc_identity']],
                array_merge($patientData, [
                    'doctor_id' => $doctor->id,
                ])
            );
        }
    }
}