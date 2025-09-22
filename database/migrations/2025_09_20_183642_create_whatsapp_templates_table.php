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
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Şablon adı');
            $table->string('template_name')->comment('WhatsApp şablon adı');
            $table->string('language_code')->default('tr')->comment('Dil kodu');
            $table->enum('category', ['MARKETING', 'UTILITY', 'AUTHENTICATION'])->default('UTILITY')->comment('Şablon kategorisi');
            $table->json('components')->nullable()->comment('Şablon bileşenleri (header, body, footer, buttons)');
            $table->text('description')->nullable()->comment('Şablon açıklaması');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'DISABLED'])->default('PENDING')->comment('Onay durumu');
            $table->boolean('is_active')->default(true)->comment('Aktif durumu');
            $table->json('variables')->nullable()->comment('Şablon değişkenleri');
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['template_name', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
