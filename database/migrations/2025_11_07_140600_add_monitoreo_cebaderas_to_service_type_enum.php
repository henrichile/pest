<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el ENUM para incluir monitoreo-cebaderas y desinfeccion
        DB::statement("ALTER TABLE services MODIFY COLUMN service_type ENUM(
            'desratizacion',
            'desinsectacion',
            'sanitizacion',
            'desinfeccion',
            'fumigacion-de-jardines',
            'servicios-especiales',
            'monitoreo-cebaderas'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al ENUM anterior sin monitoreo-cebaderas
        DB::statement("ALTER TABLE services MODIFY COLUMN service_type ENUM(
            'desratizacion',
            'desinsectacion',
            'sanitizacion',
            'desinfeccion',
            'fumigacion-de-jardines',
            'servicios-especiales'
        )");
    }
};

