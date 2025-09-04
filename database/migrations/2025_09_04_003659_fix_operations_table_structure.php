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
        Schema::table('operations', function (Blueprint $table) {
            // process_detail sütununu ekle
            $table->text('process_detail')->after('process');
            
            // Gereksiz sütunları kaldır
            $table->dropColumn(['status', 'actual_duration', 'started_at', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Kaldırılan sütunları geri ekle
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->integer('actual_duration')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // process_detail'i kaldır
            $table->dropColumn('process_detail');
        });
    }
};
