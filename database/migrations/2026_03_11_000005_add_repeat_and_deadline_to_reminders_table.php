<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->string('repeat', 20)->nullable()->after('all_day');
            $table->dateTime('deadline_at')->nullable()->after('repeat');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['repeat', 'deadline_at']);
        });
    }
};

