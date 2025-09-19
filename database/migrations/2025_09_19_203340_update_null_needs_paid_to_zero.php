<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mevcut null needs_paid değerlerini 0 olarak güncelle
        DB::table('patients')
            ->whereNull('needs_paid')
            ->update(['needs_paid' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi - 0 değerlerini null yap
        DB::table('patients')
            ->where('needs_paid', 0)
            ->update(['needs_paid' => null]);
    }
};
