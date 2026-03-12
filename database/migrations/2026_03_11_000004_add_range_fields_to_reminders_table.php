<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade campos de rango de fecha/hora a los recordatorios.
     */
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dateTime('start_at')->nullable()->after('description');
            $table->dateTime('end_at')->nullable()->after('start_at');
            $table->boolean('all_day')->default(false)->after('end_at');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['start_at', 'end_at', 'all_day']);
        });
    }
};

