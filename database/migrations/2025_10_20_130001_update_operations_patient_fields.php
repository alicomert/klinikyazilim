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
        // Make patient_id nullable and set foreign key to set null on delete
        Schema::table('operations', function (Blueprint $table) {
            // Drop existing foreign key to allow column change
            $table->dropForeign(['patient_id']);
        });
        Schema::table('operations', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable()->change();
        });
        Schema::table('operations', function (Blueprint $table) {
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });

        // Add patient_name column if not exists
        if (!Schema::hasColumn('operations', 'patient_name')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->string('patient_name')->nullable()->after('patient_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key to change column back
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });

        // Remove patient_name column if exists
        if (Schema::hasColumn('operations', 'patient_name')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->dropColumn('patient_name');
            });
        }

        // Revert patient_id to NOT NULL with cascade delete
        Schema::table('operations', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable(false)->change();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }
};