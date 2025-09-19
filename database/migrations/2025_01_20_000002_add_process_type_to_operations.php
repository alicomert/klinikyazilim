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
            $table->unsignedBigInteger('process_type')->nullable()->after('process');
            $table->foreign('process_type')->references('id')->on('operation_types')->onDelete('set null');
            $table->index('process_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['process_type']);
            $table->dropIndex(['process_type']);
            $table->dropColumn('process_type');
        });
    }
};