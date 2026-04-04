<?php

use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('nombre_consultor', 255)->nullable()->after('created_by')
                ->comment('Nombre del ejecutivo al registrar la venta (copia para la ficha)');
        });

        Sale::query()->whereNull('nombre_consultor')->with('creator')->chunkById(100, function ($sales): void {
            foreach ($sales as $sale) {
                $name = $sale->creator?->name;
                if ($name !== null && trim($name) !== '') {
                    $sale->update(['nombre_consultor' => $name]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('nombre_consultor');
        });
    }
};
