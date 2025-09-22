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
        // Check if doctor_id column exists, if not add it
        if (!Schema::hasColumn('whatsapp_configs', 'doctor_id')) {
            Schema::table('whatsapp_configs', function (Blueprint $table) {
                $table->unsignedBigInteger('doctor_id')->nullable()->after('id');
                $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['doctor_id', 'is_active']);
            });
            
            // Update existing records to have a default doctor_id (first admin user)
            $firstAdmin = \App\Models\User::where('role', 'admin')->first();
            if ($firstAdmin) {
                \Illuminate\Support\Facades\DB::table('whatsapp_configs')
                    ->whereNull('doctor_id')
                    ->update(['doctor_id' => $firstAdmin->id]);
            }
            
            // Make doctor_id not nullable
            Schema::table('whatsapp_configs', function (Blueprint $table) {
                $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_configs', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_configs', 'doctor_id')) {
                $table->dropForeign(['doctor_id']);
                $table->dropColumn('doctor_id');
            }
        });
    }
};
