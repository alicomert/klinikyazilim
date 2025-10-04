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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->enum('payment_method', ['nakit', 'kredi_karti', 'banka_havalesi', 'pos', 'cek', 'diger'])->default('nakit');
            $table->decimal('paid_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // İndeksler
            $table->index(['patient_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};