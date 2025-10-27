<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('message_automation_configs', 'reg_id')) {
            Schema::table('message_automation_configs', function (Blueprint $table) {
                $table->dropColumn('reg_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('message_automation_configs', function (Blueprint $table) {
            $table->string('reg_id')->nullable();
        });
    }
};