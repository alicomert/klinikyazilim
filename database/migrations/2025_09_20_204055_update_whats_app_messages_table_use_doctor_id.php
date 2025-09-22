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
        Schema::table('whats_app_messages', function (Blueprint $table) {
            // Add doctor_id column
            $table->unsignedBigInteger('doctor_id')->nullable()->after('id');
        });
        
        // Copy data from user_id to doctor_id
        DB::statement('UPDATE whats_app_messages SET doctor_id = user_id WHERE user_id IS NOT NULL');
        
        Schema::table('whats_app_messages', function (Blueprint $table) {
            // Make doctor_id not nullable
            $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            
            // Add foreign key constraint
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Drop user_id column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_messages', function (Blueprint $table) {
            // Add user_id column back
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            
            // Copy data from doctor_id to user_id
            DB::statement('UPDATE whats_app_messages SET user_id = doctor_id WHERE doctor_id IS NOT NULL');
            
            // Make user_id not nullable
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            
            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Drop doctor_id column
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};
