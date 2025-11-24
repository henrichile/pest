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
        // Verificar si el tipo de servicio ya existe
        $exists = DB::table('service_types')
            ->where('slug', 'monitoreo-cebaderas')
            ->exists();

        // Solo insertar si no existe
        if (!$exists) {
            DB::table('service_types')->insert([
                'name' => 'Monitoreo de Cebaderas',
                'slug' => 'monitoreo-cebaderas',
                'description' => 'Monitoreo y control de estaciones cebaderas para roedores',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el tipo de servicio "Monitoreo de Cebaderas"
        DB::table('service_types')
            ->where('slug', 'monitoreo-cebaderas')
            ->delete();
    }
};
