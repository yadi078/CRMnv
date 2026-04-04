<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('forma_pago', 500)->nullable()->change();
            $table->string('orden_compra', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('forma_pago', 100)->nullable()->change();
            $table->string('orden_compra', 100)->nullable()->change();
        });
    }
};
