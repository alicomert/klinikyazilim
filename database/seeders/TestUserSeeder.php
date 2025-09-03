<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test kullanıcısı oluştur
        User::create([
            'name' => 'Test Kullanıcı',
            'email' => 'test@test.com',
            'password' => Hash::make('123456'),
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        // Hemşire test kullanıcısı
        User::create([
            'name' => 'Hemşire Test',
            'email' => 'hemsire@test.com',
            'password' => Hash::make('123456'),
            'role' => 'nurse',
            'email_verified_at' => now(),
        ]);

        // Sekreter test kullanıcısı
        User::create([
            'name' => 'Sekreter Test',
            'email' => 'sekreter@test.com',
            'password' => Hash::make('123456'),
            'role' => 'secretary',
            'email_verified_at' => now(),
        ]);
    }
}