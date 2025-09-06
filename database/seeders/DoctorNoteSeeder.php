<?php

namespace Database\Seeders;

use App\Models\DoctorNote;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorNoteSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $users = User::whereIn('role', ['doctor', 'nurse', 'secretary'])->get();
        
        if ($patients->count() === 0 || $users->count() === 0) {
            return;
        }

        $notes = [
            [
                'title' => 'Ameliyat Öncesi Hazırlık',
                'content' => 'Hasta ameliyat öncesi değerlendirme tamamlandı. Kan tahlilleri normal. Anestezi onayı alındı.',
                'note_type' => 'important',
                'is_private' => false,
            ],
            [
                'title' => 'Günlük Takip Notu',
                'content' => 'Hastanın genel durumu iyi. Vital bulgular stabil. İlaç tedavisine devam.',
                'note_type' => 'general',
                'is_private' => false,
            ],
            [
                'title' => 'Taburcu Planı',
                'content' => 'Hasta yarın taburcu edilebilir. Evde bakım talimatları verildi. Kontrol randevusu 1 hafta sonra.',
                'note_type' => 'important',
                'is_private' => false,
            ],
            [
                'title' => 'İlaç Değişikliği',
                'content' => 'Hastanın mevcut ilacına alerjik reaksiyon gösterdi. Alternatif ilaç başlandı.',
                'note_type' => 'important',
                'is_private' => false,
            ],
            [
                'title' => 'Konsültasyon Notu',
                'content' => 'Kardiyoloji konsültasyonu istendi. EKG çekildi, sonuç bekleniyor.',
                'note_type' => 'general',
                'is_private' => false,
            ],
            [
                'title' => 'Özel Not - Aile Görüşmesi',
                'content' => 'Hasta ailesi ile görüşüldü. Durum hakkında bilgi verildi. Endişeleri giderildi.',
                'note_type' => 'reminder',
                'is_private' => true,
            ],
            [
                'title' => 'Hemşire Gözlem Notu',
                'content' => 'Gece vardiyasında hasta huzursuzdu. Ağrı kesici verildi. Sabah daha iyi.',
                'note_type' => 'general',
                'is_private' => false,
            ],
            [
                'title' => 'Laboratuvar Sonuçları',
                'content' => 'Kan tahlili sonuçları geldi. Hemoglobin düşük, demir eksikliği var. Tedavi başlandı.',
                'note_type' => 'important',
                'is_private' => false,
            ],
            [
                'title' => 'Fizik Tedavi Planı',
                'content' => 'Ameliyat sonrası fizik tedavi programı hazırlandı. Haftada 3 gün, 4 hafta sürecek.',
                'note_type' => 'reminder',
                'is_private' => false,
            ],
            [
                'title' => 'Acil Durum Notu',
                'content' => 'Hasta gece ani nefes darlığı yaşadı. Oksijen verildi, durumu stabilize oldu.',
                'note_type' => 'important',
                'is_private' => false,
            ]
        ];

        foreach ($notes as $index => $noteData) {
            $user = $users->random();
            $doctor = User::where('role', 'doctor')->inRandomOrder()->first();
            
            DoctorNote::create(array_merge($noteData, [
                'user_id' => $user->id,
                'doctor_id' => $doctor->id,
                'note_date' => now()->subDays(rand(0, 30)),
                'last_updated' => now()->subDays(rand(0, 15)),
            ]));
        }
    }
}