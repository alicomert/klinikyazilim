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
            $table->dropColumn(['value', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_types', function (Blueprint $table) {
            $table->string('value')->unique()->after('name');
            $table->text('description')->nullable()->after('value');
        });
    }
};