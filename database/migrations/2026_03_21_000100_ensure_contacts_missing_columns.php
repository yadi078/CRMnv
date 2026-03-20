<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alinea tabla contacts con el modelo cuando migraciones no corrieron en orden
 * (p. ej. Unknown column 'razon_social', 'status_color', etc.).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            if (! Schema::hasColumn('contacts', 'genero')) {
                $table->string('genero')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'telefono')) {
                $table->string('telefono')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'email_activo')) {
                $table->boolean('email_activo')->default(true);
            }
            if (! Schema::hasColumn('contacts', 'fecha_cumpleanos')) {
                $table->date('fecha_cumpleanos')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'razon_social')) {
                $table->string('razon_social', 255)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'nombre_comercial')) {
                $table->string('nombre_comercial', 255)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'calle_numero')) {
                $table->string('calle_numero', 500)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'colonia_cp')) {
                $table->string('colonia_cp', 255)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'rfc')) {
                $table->string('rfc', 20)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'regimen_fiscal')) {
                $table->string('regimen_fiscal', 255)->nullable();
            }
            if (! Schema::hasColumn('contacts', 'status_color')) {
                $table->string('status_color', 50)->default('seguimiento');
            }
            if (! Schema::hasColumn('contacts', 'approval_status')) {
                $table->string('approval_status', 32)->default('pendiente');
            }
            if (! Schema::hasColumn('contacts', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'motivo_rechazo')) {
                $table->text('motivo_rechazo')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'deletion_pending')) {
                $table->boolean('deletion_pending')->default(false);
            }
            if (! Schema::hasColumn('contacts', 'deletion_requested_by')) {
                $table->foreignId('deletion_requested_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'deletion_requested_at')) {
                $table->timestamp('deletion_requested_at')->nullable();
            }
            if (! Schema::hasColumn('contacts', 'deletion_reason')) {
                $table->text('deletion_reason')->nullable();
            }
        });

        if (Schema::hasColumn('contacts', 'email_activo')) {
            DB::table('contacts')->whereNull('email_activo')->update(['email_activo' => true]);
        }
    }

    public function down(): void
    {
        // Sin revertir: evita romper datos si ya se usó el esquema nuevo.
    }
};
