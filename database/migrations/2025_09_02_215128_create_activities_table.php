<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Aktivite tipi (örn: 'new_patient_registration')
            $table->text('description'); // Aktivite açıklaması (örn: 'Yeni hasta kaydı Eren Demir - 25 yaş')
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('cascade'); // Hasta ID'si
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
