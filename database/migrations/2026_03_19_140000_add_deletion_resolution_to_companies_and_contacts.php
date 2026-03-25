<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo empresas aquí: en contacts, `deletion_reason` se crea en 2026_03_20_000100;
        // tocar contacts antes rompe el orden de migraciones (columna inexistente).
        Schema::table('companies', function (Blueprint $table) {
            $table->string('deletion_resolution', 20)->nullable()->after('deletion_requested_at');
            $table->text('deletion_resolution_note')->nullable()->after('deletion_resolution');
            $table->timestamp('deletion_resolved_at')->nullable()->after('deletion_resolution_note');
            $table->foreignId('deletion_resolved_by')->nullable()->after('deletion_resolved_at')->constrained('users')->nullOnDelete();
            $table->foreignId('deletion_decision_user_id')->nullable()->after('deletion_resolved_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
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
