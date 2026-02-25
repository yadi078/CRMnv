<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hace el RFC opcional (nullable) en companies.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE companies MODIFY COLUMN rfc VARCHAR(13) NULL UNIQUE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE companies MODIFY COLUMN rfc VARCHAR(13) NOT NULL UNIQUE');
    }
};
