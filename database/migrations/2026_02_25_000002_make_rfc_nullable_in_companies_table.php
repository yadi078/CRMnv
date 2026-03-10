<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hace el RFC opcional (nullable) en companies.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE companies MODIFY COLUMN rfc VARCHAR(13) NULL UNIQUE');
        }
        // SQLite no soporta MODIFY COLUMN; la columna sigue siendo NOT NULL en SQLite.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE companies MODIFY COLUMN rfc VARCHAR(13) NOT NULL UNIQUE');
        }
    }
};
