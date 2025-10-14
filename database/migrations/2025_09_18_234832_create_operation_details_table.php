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
        if (!Schema::hasTable('operation_details')) {
            Schema::create('operation_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operation_type_id'); // Hangi işlem türüne ait
                $table->string('name'); // Detay adı (örn: "Burun Estetiği", "Yüz Mezoterapisi")
                $table->text('description')->nullable(); // Açıklama
                $table->boolean('is_active')->default(true); // Aktif/pasif durumu
                $table->integer('sort_order')->default(0); // Sıralama
                $table->unsignedBigInteger('created_by')->nullable(); // Oluşturan kullanıcı
                $table->timestamps();
                
                $table->foreign('operation_type_id')->references('id')->on('operation_types')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['operation_type_id', 'is_active', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_details');
    }
};
