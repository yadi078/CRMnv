<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repara tabla contacts: columnas para denegar/aprobar eliminación con nota.
 * Ejecutar con: php artisan migrate
 *
 * Si ves "Unknown column 'deletion_resolution'", esta migración (o 2026_03_24) no se había aplicado.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'deletion_resolution')) {
                $table->string('deletion_resolution', 20)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolution_note')) {
                $table->text('deletion_resolution_note')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolved_at')) {
                $table->timestamp('deletion_resolved_at')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolved_by')) {
                $table->foreignId('deletion_resolved_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'deletion_decision_user_id')) {
                $table->foreignId('deletion_decision_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
