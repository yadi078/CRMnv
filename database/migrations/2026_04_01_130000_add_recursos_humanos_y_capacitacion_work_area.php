<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('work_areas')) {
            return;
        }

        $now = now();
        DB::table('work_areas')->insertOrIgnore([
            'name' => 'RECURSOS HUMANOS Y CAPACITACION',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('work_areas')) {
            return;
        }

        DB::table('work_areas')->where('name', 'RECURSOS HUMANOS Y CAPACITACION')->delete();
    }
};
