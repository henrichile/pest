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
        Schema::table('services', function (Blueprint $table) {
            $table->enum('service_type', [
                'desratizacion',
                'desinsectacion',
                'sanitizacion',
                'fumigacion-de-jardines',
                'servicios-especiales'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Revertir a string, asumiendo que ese era el tipo anterior.
            // Ajusta 'string' si el tipo original era otro.
            $table->string('service_type')->change();
        });
    }
};
