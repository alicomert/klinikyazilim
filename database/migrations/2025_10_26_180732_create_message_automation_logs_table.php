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
        Schema::create('message_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('config_id');
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('user_id');
            $table->string('phone_number');
            $table->text('message_content');
            $table->enum('status', ['pending', 'sent', 'failed', 'error'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->string('wamessage_report_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('config_id')->references('id')->on('message_automation_configs')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['doctor_id', 'status']);
            $table->index(['appointment_id', 'status']);
            $table->index('sent_at');
            $table->index('config_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_automation_logs');
    }
};
