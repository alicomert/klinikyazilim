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
        Schema::table('operation_types', function (Blueprint $table) {
            // Drop columns only if they exist
            if (Schema::hasColumn('operation_types', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('operation_types', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_types', function (Blueprint $table) {
            // Recreate columns only if they do not exist
            if (!Schema::hasColumn('operation_types', 'value')) {
                $table->string('value')->unique()->after('name');
            }
            if (!Schema::hasColumn('operation_types', 'description')) {
                $table->text('description')->nullable()->after('value');
            }
        });
    }
};