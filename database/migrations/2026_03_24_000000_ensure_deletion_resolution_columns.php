<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asegura columnas de resolución de solicitudes de eliminación (empresa/contacto).
 * Útil si la migración 2026_03_19_140000 no corrió o falló a mitad.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (! Schema::hasColumn('companies', 'deletion_resolution')) {
                    $table->string('deletion_resolution', 20)->nullable();
                }
                if (! Schema::hasColumn('companies', 'deletion_resolution_note')) {
                    $table->text('deletion_resolution_note')->nullable();
                }
                if (! Schema::hasColumn('companies', 'deletion_resolved_at')) {
                    $table->timestamp('deletion_resolved_at')->nullable();
                }
                if (! Schema::hasColumn('companies', 'deletion_resolved_by')) {
                    $table->foreignId('deletion_resolved_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('companies', 'deletion_decision_user_id')) {
                    $table->foreignId('deletion_decision_user_id')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('contacts')) {
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
    }

    public function down(): void
    {
        // No revertir: evita perder datos si ya se usó el flujo de denegación.
    }
};
