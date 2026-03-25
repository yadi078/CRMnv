<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columnas de resolución de eliminación en contactos.
 * Debe ejecutarse después de 2026_03_20_000100 (donde existe deletion_reason).
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
                $table->string('deletion_resolution', 20)->nullable()->after('deletion_reason');
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolution_note')) {
                $table->text('deletion_resolution_note')->nullable()->after('deletion_resolution');
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolved_at')) {
                $table->timestamp('deletion_resolved_at')->nullable()->after('deletion_resolution_note');
            }
            if (! Schema::hasColumn('contacts', 'deletion_resolved_by')) {
                $table->foreignId('deletion_resolved_by')->nullable()->after('deletion_resolved_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('contacts', 'deletion_decision_user_id')) {
                $table->foreignId('deletion_decision_user_id')->nullable()->after('deletion_resolved_by')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['deletion_resolved_by']);
            $table->dropForeign(['deletion_decision_user_id']);
            $table->dropColumn([
                'deletion_resolution',
                'deletion_resolution_note',
                'deletion_resolved_at',
                'deletion_resolved_by',
                'deletion_decision_user_id',
            ]);
        });
    }
};
