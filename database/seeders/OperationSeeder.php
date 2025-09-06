<?php

namespace Database\Seeders;

use App\Models\Operation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = User::where('role', 'doctor')->get();
        
        if ($patients->count() === 0 || $doctors->count() === 0) {
            return;
        }

        $operations = [
            [
                'process' => 'surgery',
                'process_detail' => 'Laparoskopik apandisektomi işlemi. Hasta genel anestezi altında operasyona alındı.',
                'process_date' => now()->addDays(2)->format('Y-m-d'),
                'registration_period' => now()->format('Y-m'),
                'estimated_duration' => 90,
                'notes' => 'Hasta ameliyat öncesi 8 saat açlık gerekli.',
                'status' => 'scheduled',
            ],
            [
                'process' => 'surgery',
                'process_detail' => 'Sol göz katarakt ameliyatı. Fakoemülsifikasyon yöntemi kullanıldı.',
                'process_date' => now()->addDays(5)->format('Y-m-d'),
                'registration_period' => now()->format('Y-m'),
                'estimated_duration' => 45,
                'notes' => 'Ameliyat sonrası göz damlası kullanımı gerekli.',
                'status' => 'scheduled',
            ],
            [
                'process' => 'surgery',
                'process_detail' => 'Sağ diz menisküs yırtığı onarımı. Artroskopik yöntem kullanıldı.',
                'process_date' => now()->addDays(7)->format('Y-m-d'),
                'registration_period' => now()->format('Y-m'),
                'estimated_duration' => 60,
                'notes' => 'Ameliyat sonrası fizik tedavi programı başlatılacak.',
                'status' => 'scheduled',
            ],
            [
                'process' => 'mesotherapy',
                'process_detail' => 'Yüz bölgesi mezoterapisi. Cilt gençleştirme amaçlı.',
                'process_date' => now()->addDays(10)->format('Y-m-d'),
                'registration_period' => now()->format('Y-m'),
                'estimated_duration' => 30,
                'notes' => 'İşlem sonrası güneşten korunma gerekli.',
                'status' => 'scheduled',
            ],
            [
                'process' => 'botox',
                'process_detail' => 'Alın ve göz çevresi botoks uygulaması.',
                'process_date' => now()->addDays(14)->format('Y-m-d'),
                'registration_period' => now()->format('Y-m'),
                'estimated_duration' => 20,
                'notes' => 'İşlem sonrası 4 saat yatmamalı.',
                'status' => 'scheduled',
            ]
        ];

        foreach ($operations as $index => $operationData) {
            $patient = $patients->get($index % $patients->count());
            $doctor = $doctors->random();
            
            Operation::create(array_merge($operationData, [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'created_by' => $doctor->id,
            ]));
        }
    }
}