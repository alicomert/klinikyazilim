<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Doktor hesabı
        User::create([
            'name' => 'Dr. Mehmet Özkan',
            'email' => 'doktor@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'email_verified_at' => now(),
        ]);

        // Hemşire hesabı
        User::create([
            'name' => 'Ayşe Yılmaz',
            'email' => 'hemsire@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'nurse',
            'email_verified_at' => now(),
        ]);

        // Sekreter hesabı
        User::create([
            'name' => 'Fatma Demir',
            'email' => 'sekreter@klinik.com',
            'password' => Hash::make('password'),
            'role' => 'secretary',
            'email_verified_at' => now(),
        ]);
    }
}
