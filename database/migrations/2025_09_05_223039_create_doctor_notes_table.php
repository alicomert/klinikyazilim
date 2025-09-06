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
        Schema::create('doctor_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Notu yazan kullanıcı
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade'); // Hangi doktora ait
            $table->string('title')->nullable(); // Not başlığı
            $table->text('content'); // Not içeriği (şifrelenecek)
            $table->enum('note_type', ['general', 'reminder', 'important'])->default('general');
            $table->boolean('is_private')->default(false);
            $table->timestamp('note_date')->useCurrent();
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            // İndeksler
            $table->index(['doctor_id', 'note_date']);
            $table->index(['user_id', 'note_date']);
            $table->index(['note_type', 'is_private']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_notes');
    }
};
