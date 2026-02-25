<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Evita enviar la misma notificación de recordatorio/vencido más de una vez.
     */
    public function up(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->timestamp('notification_sent_at')->nullable()->after('completado_at')->comment('Última notificación de recordatorio/vencido enviada');
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropColumn('notification_sent_at');
        });
    }
};
