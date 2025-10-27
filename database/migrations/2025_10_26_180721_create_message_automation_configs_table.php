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
        Schema::create('message_automation_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('user_id');
            $table->string('api_token');
            $table->string('reg_id');
            $table->string('phone_number');
            $table->text('message_template');
            $table->integer('hours_before_appointment')->default(24);
            $table->boolean('is_active')->default(true);
            $table->integer('send_speed')->default(1);
            $table->string('campaign_name')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['doctor_id', 'is_active']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_automation_configs');
    }
};
