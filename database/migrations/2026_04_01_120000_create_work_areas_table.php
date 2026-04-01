<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();
        $areas = [
            'ADMINISTRACION',
            'ADMINISTRACION DEL PERSONAL',
            'RECURSOS HUMANOS',
            'CAPACITACION',
            'DESARROLLO ORGANIZACIONAL',
            'ALMACEN',
            'ALMACEN Y COMPRAS',
            'COMPRAS',
            'LOGISTICA',
            'OPERACIONES',
            'TRAFICO',
            'CALIDAD',
            'PRODUCCION',
            'CALIDAD Y PRODUCCION',
            'SEGURIDAD E HIGIENE',
            'MANTENIMIENTO',
            'COMERCIALIZACION',
            'MARKETING',
            'VENTAS',
            'ATENCION A CLIENTES',
            'DESPACHOS CONTABLES',
            'CONTABILIDAD',
            'FINANZAS',
            'TESORERIA',
            'AUDITORIA',
            'CONTROL INTERNO',
            'DESPACHO DE ABOGADOS',
            'JURIDICO',
            'DIRECCION',
            'GERENCIA',
            'PLANEACION',
            'PROYECTOS',
            'INGENIERIA',
            'SISTEMAS',
            'TECNOLOGIAS DE LA INFORMACION',
            'INTELIGENCIA DE NEGOCIOS',
            'INNOVACION Y DESARROLLO',
            'COMERCIO EXTERIOR',
            'RECURSOS MATERIALES',
            'ENERGIA',
            'ESCUELAS',
            'GOBIERNO',
            'HOSPITALES',
        ];

        DB::table('work_areas')->insert(
            collect($areas)->map(fn (string $name) => [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('work_areas');
    }
};
