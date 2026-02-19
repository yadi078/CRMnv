<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     * 
     * Agrega campo de aprobación a usuarios.
     * Los usuarios normales quedan en estado 'pendiente' hasta aprobación del administrador.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('approval_status', ['pendiente', 'aprobado'])->default('aprobado')->after('email')->comment('Estado de aprobación del usuario');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approval_status')->comment('Administrador que aprobó al usuario');
            $table->timestamp('approved_at')->nullable()->after('approved_by')->comment('Fecha de aprobación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at']);
        });
    }
};
