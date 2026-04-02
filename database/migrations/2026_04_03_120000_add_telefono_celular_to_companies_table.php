<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'telefono')) {
                $table->string('telefono', 50)->nullable()->after('estado');
            }
            if (! Schema::hasColumn('companies', 'celular')) {
                $table->string('celular', 50)->nullable()->after('telefono');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'celular')) {
                $table->dropColumn('celular');
            }
            if (Schema::hasColumn('companies', 'telefono')) {
                $table->dropColumn('telefono');
            }
        });
    }
};
