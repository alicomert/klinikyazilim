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
            if (!Schema::hasColumn('operation_types', 'process')) {
                $table->enum('process', ['surgery', 'mesotherapy', 'botox', 'filler'])->nullable()->after('name');
                $table->index('process');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_types', function (Blueprint $table) {
            if (Schema::hasColumn('operation_types', 'process')) {
                $table->dropIndex(['process']);
                $table->dropColumn('process');
            }
        });
    }
};
