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
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->comment('Konfigürasyon adı');
            $table->string('phone_number_id')->comment('WhatsApp Phone Number ID');
            $table->text('access_token')->comment('WhatsApp Access Token');
            $table->string('business_account_id')->nullable()->comment('WhatsApp Business Account ID');
            $table->string('webhook_verify_token')->nullable()->comment('Webhook doğrulama token');
            $table->boolean('is_active')->default(true)->comment('Aktif durumu');
            $table->json('settings')->nullable()->comment('Ek ayarlar');
            $table->timestamps();
            
            $table->index(['doctor_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_configs');
    }
};
