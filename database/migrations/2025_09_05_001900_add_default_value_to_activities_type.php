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
        // Activities tablosundaki type alanına default value ekle
        DB::statement("ALTER TABLE activities MODIFY COLUMN type VARCHAR(255) DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Default value'yu kaldır
        DB::statement("ALTER TABLE activities MODIFY COLUMN type VARCHAR(255)");
    }
};