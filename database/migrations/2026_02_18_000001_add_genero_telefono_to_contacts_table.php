<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'genero')) {
                $table->string('genero')->nullable()->after('nombre_completo');
            }
            if (!Schema::hasColumn('contacts', 'telefono')) {
                $table->string('telefono')->nullable()->after('celular');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'genero')) {
                $table->dropColumn('genero');
            }
            if (Schema::hasColumn('contacts', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });
    }
};
