<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop and recreate api_token as TEXT to support long tokens
        Schema::table('message_automation_configs', function (Blueprint $table) {
            if (Schema::hasColumn('message_automation_configs', 'api_token')) {
                $table->dropColumn('api_token');
            }
        });

        Schema::table('message_automation_configs', function (Blueprint $table) {
            $table->text('api_token');
        });
    }

    public function down(): void
    {
        // Revert to VARCHAR(255)
        Schema::table('message_automation_configs', function (Blueprint $table) {
            if (Schema::hasColumn('message_automation_configs', 'api_token')) {
                $table->dropColumn('api_token');
            }
        });

        Schema::table('message_automation_configs', function (Blueprint $table) {
            $table->string('api_token', 255);
        });
    }
};