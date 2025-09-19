<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OperationType;
use App\Models\OperationDetail;

class OperationTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Mevcut işlem türlerini oluştur
        $operationTypes = [
            [
                'name' => 'Ameliyat',
                'value' => 'ameliyat',
                'description' => 'Cerrahi operasyonlar',
                'sort_order' => 1,
                'details' => [
                    'Burun Estetiği',
                    'Göğüs Büyütme',
                    'Karın Germe',
                    'Yüz Germe',
                    'Liposuction',
                    'Göz Kapağı Estetiği'
                ]
            ],
            [
                'name' => 'Mezoterapi',
                'value' => 'mezoterapi',
                'description' => 'Mezoterapi uygulamaları',
                'sort_order' => 2,
                'details' => [
                    'Yüz Mezoterapisi',
                    'Saç Mezoterapisi',
                    'Vücut Mezoterapisi',
                    'Selülit Mezoterapisi',
                    'Göz Altı Mezoterapisi'
                ]
            ],
            [
                'name' => 'Botoks',
                'value' => 'botoks',
                'description' => 'Botoks uygulamaları',
                'sort_order' => 3,
                'details' => [
                    'Alın Botoksu',
                    'Göz Çevresi Botoksu',
                    'Kaş Arası Botoksu',
                    'Masaj Kası Botoksu',
                    'Boyun Botoksu'
                ]
            ],
            [
                'name' => 'Dolgu',
                'value' => 'dolgu',
                'description' => 'Dolgu uygulamaları',
                'sort_order' => 4,
                'details' => [
                    'Dudak Dolgusu',
                    'Yanak Dolgusu',
                    'Çene Dolgusu',
                    'Burun Dolgusu',
                    'Göz Altı Dolgusu'
                ]
            ]
        ];

        foreach ($operationTypes as $typeData) {
            $details = $typeData['details'];
            unset($typeData['details']);
            
            $operationType = OperationType::create($typeData);
            
            // Her işlem türü için detayları oluştur
            foreach ($details as $index => $detailName) {
                OperationDetail::create([
                    'operation_type_id' => $operationType->id,
                    'name' => $detailName,
                    'description' => $detailName . ' işlemi',
                    'sort_order' => $index + 1,
                    'is_active' => true
                ]);
            }
        }
    }
}
