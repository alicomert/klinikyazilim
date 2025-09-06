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
        Schema::create('appointment_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('note_type', ['medical', 'general', 'appointment', 'followup', 'treatment'])->default('general');
            $table->text('content');
            $table->boolean('is_private')->default(false);
            $table->timestamp('note_date')->useCurrent();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
            
            // Indexes
            $table->index(['appointment_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['doctor_id', 'created_at']);
            $table->index('note_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_notes');
    }
};
