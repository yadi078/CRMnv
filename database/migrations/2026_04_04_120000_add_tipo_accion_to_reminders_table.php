<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reminders')) {
            return;
        }

        Schema::table('reminders', function (Blueprint $table) {
            if (! Schema::hasColumn('reminders', 'tipo_accion')) {
                $table->string('tipo_accion', 32)->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reminders')) {
            return;
        }

        Schema::table('reminders', function (Blueprint $table) {
            if (Schema::hasColumn('reminders', 'tipo_accion')) {
                $table->dropColumn('tipo_accion');
            }
        });
    }
};
