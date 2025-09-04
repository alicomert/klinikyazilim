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
        Schema::create('operation_notes', function (Blueprint $table) {
            $table->id();
            
            // İlişkiler
            $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Notu yazan kişi
            
            // Manuel indexler
            $table->index('operation_id');
            $table->index('user_id');
            
            // Not Bilgileri
            $table->enum('note_type', ['general', 'medical', 'procedure', 'followup'])->index(); // Not türü
            $table->text('content'); // Not içeriği
            $table->boolean('is_private')->default(false)->index(); // Sadece doktorların kullanabileceği gizli notlar
            
            // Tarih Takibi
            $table->timestamp('note_date')->useCurrent()->index(); // Not tarihi
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate()->index(); // Son güncelleme
            $table->timestamps();
            
            // Performans için indexler
            $table->index(['operation_id', 'note_type']);
            $table->index(['operation_id', 'note_date']);
            $table->index(['user_id', 'note_date']);
            $table->index(['created_at']);
        });
        
        // InnoDB engine for better performance
        DB::statement('ALTER TABLE operation_notes ENGINE = InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_notes');
    }
};
