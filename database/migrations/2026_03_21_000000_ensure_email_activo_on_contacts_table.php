<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repara BDs donde nunca corrió 2026_03_09_000002_add_email_activo_to_contacts_table
 * (error: Unknown column 'email_activo' in 'field list').
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contacts') || Schema::hasColumn('contacts', 'email_activo')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('email_activo')
                ->default(true)
                ->after('email')
                ->comment('Si es falso, el correo no se muestra en fichas ni listados');
        });

        DB::table('contacts')->update(['email_activo' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('contacts') || ! Schema::hasColumn('contacts', 'email_activo')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('email_activo');
        });
    }
};
