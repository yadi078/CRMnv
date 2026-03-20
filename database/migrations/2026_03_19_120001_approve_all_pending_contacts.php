<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los contactos ya no pasan por cola de aprobación; normalizar registros antiguos.
     */
    public function up(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }
        if (! Schema::hasColumn('contacts', 'approval_status')) {
            return;
        }

        DB::table('contacts')
            ->where('approval_status', 'pendiente')
            ->update([
                'approval_status' => 'aprobado',
                'approved_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Irreversible: no se restaura el estado previo de aprobación.
    }
};
