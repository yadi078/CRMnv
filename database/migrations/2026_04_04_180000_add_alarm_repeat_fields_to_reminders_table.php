<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repetición de alarma (distinta de la columna "repeat" diario/semanal/mensual).
     */
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->boolean('alarm_repeat_enabled')->default(false);
            $table->unsignedSmallInteger('alarm_repeat_interval_minutes')->nullable();
            $table->string('alarm_repeat_type', 32)->nullable();
            $table->unsignedInteger('alarm_repeat_value')->nullable();
            $table->timestamp('alarm_confirmed_at')->nullable();
            $table->timestamp('alarm_last_ring_at')->nullable();
            $table->unsignedInteger('alarm_rings_count')->default(0);
            $table->timestamp('alarm_window_started_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn([
                'alarm_repeat_enabled',
                'alarm_repeat_interval_minutes',
                'alarm_repeat_type',
                'alarm_repeat_value',
                'alarm_confirmed_at',
                'alarm_last_ring_at',
                'alarm_rings_count',
                'alarm_window_started_at',
            ]);
        });
    }
};
