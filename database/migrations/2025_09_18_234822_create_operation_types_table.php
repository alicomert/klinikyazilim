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
        if (!Schema::hasTable('operation_types')) {
            Schema::create('operation_types', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // İşlem türü adı (örn: "Ameliyat", "Mezoterapi")
                $table->string('value')->unique(); // İşlem türü değeri (örn: "surgery", "mesotherapy")
                $table->text('description')->nullable(); // Açıklama
                $table->boolean('is_active')->default(true); // Aktif/pasif durumu
                $table->integer('sort_order')->default(0); // Sıralama
                $table->unsignedBigInteger('created_by')->nullable(); // Oluşturan kullanıcı
                $table->timestamps();
                
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['is_active', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('operation_types')) {
            Schema::dropIfExists('operation_types');
        }
    }
};
