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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            
            // Temel Bilgiler
            $table->string('first_name', 100)->index();
            $table->string('last_name', 100)->index();
            $table->string('tc_identity', 255)->unique()->index();
            $table->string('phone', 20)->index();
            $table->date('birth_date')->index();
            $table->text('address')->nullable();
            
            // Tıbbi Bilgiler (Şifrelenecek)
            $table->text('medications')->nullable(); // İlaç kullanımı
            $table->text('allergies')->nullable(); // Alerjik durum
            $table->text('previous_operations')->nullable(); // Geçirilen operasyonlar
            $table->text('complaints')->nullable(); // Şikayetleri
            $table->text('anamnesis')->nullable(); // Anamnez
            $table->text('physical_examination')->nullable(); // Fiziki muayene
            $table->text('planned_operation')->nullable(); // Karar verilen operasyon
            $table->text('chronic_conditions')->nullable(); // Kronik rahatsızlığı
            
            // Sistem Bilgileri
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_visit')->nullable()->index();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['first_name', 'last_name']);
            $table->index(['created_at']);
            $table->index(['is_active', 'created_at']);
        });
        
        // Set engine to InnoDB for better performance with large datasets
        DB::statement('ALTER TABLE patients ENGINE = InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
