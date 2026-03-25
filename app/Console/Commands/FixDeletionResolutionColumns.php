<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repara BD cuando falta deletion_resolution en contacts/companies (error al denegar eliminación).
 * No depende del estado de la tabla migrations.
 */
class FixDeletionResolutionColumns extends Command
{
    protected $signature = 'crm:fix-deletion-columns';

    protected $description = 'Crea columnas deletion_resolution* en contacts y companies si no existen';

    public function handle(): int
    {
        $this->info('Revisando tablas contacts y companies...');

        $fixed = $this->fixTable('contacts') || $this->fixTable('companies');

        if ($fixed) {
            $this->newLine();
            $this->info('Listo. Vuelve a intentar «Denegar eliminación» en el navegador.');
        } else {
            $this->info('No hacía falta: las columnas ya estaban creadas.');
        }

        return self::SUCCESS;
    }

    protected function fixTable(string $table): bool
    {
        if (! Schema::hasTable($table)) {
            $this->warn("La tabla «{$table}» no existe; se omite.");

            return false;
        }

        $before = [
            Schema::hasColumn($table, 'deletion_resolution'),
            Schema::hasColumn($table, 'deletion_resolution_note'),
            Schema::hasColumn($table, 'deletion_resolved_at'),
            Schema::hasColumn($table, 'deletion_resolved_by'),
            Schema::hasColumn($table, 'deletion_decision_user_id'),
        ];

        if (! in_array(false, $before, true)) {
            $this->line("  {$table}: OK (ya tenía todas las columnas).");

            return false;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table) {
            if (! Schema::hasColumn($table, 'deletion_resolution')) {
                $blueprint->string('deletion_resolution', 20)->nullable();
            }
            if (! Schema::hasColumn($table, 'deletion_resolution_note')) {
                $blueprint->text('deletion_resolution_note')->nullable();
            }
            if (! Schema::hasColumn($table, 'deletion_resolved_at')) {
                $blueprint->timestamp('deletion_resolved_at')->nullable();
            }
            if (! Schema::hasColumn($table, 'deletion_resolved_by')) {
                $blueprint->foreignId('deletion_resolved_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn($table, 'deletion_decision_user_id')) {
                $blueprint->foreignId('deletion_decision_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        $this->info("  {$table}: columnas de resolución de eliminación añadidas.");

        return true;
    }
}
