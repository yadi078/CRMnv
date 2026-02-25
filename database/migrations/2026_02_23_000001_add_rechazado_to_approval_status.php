<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Añade 'rechazado' al enum approval_status en companies y users
     * para permitir denegar solicitudes desde el panel de administración.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return; // SQLite y otros: la columna suele ser string y acepta 'rechazado'
        }
        DB::statement("ALTER TABLE companies MODIFY COLUMN approval_status ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE users MODIFY COLUMN approval_status ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'aprobado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }
        DB::statement("ALTER TABLE companies MODIFY COLUMN approval_status ENUM('pendiente', 'aprobado') DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE users MODIFY COLUMN approval_status ENUM('pendiente', 'aprobado') DEFAULT 'aprobado'");
    }
};
