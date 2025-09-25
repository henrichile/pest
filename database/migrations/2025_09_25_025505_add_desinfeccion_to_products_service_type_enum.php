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
        // Modificar el enum para incluir 'desinfeccion'
        DB::statement("ALTER TABLE products MODIFY COLUMN service_type ENUM('desratizacion', 'desinsectacion', 'sanitizacion', 'desinfeccion')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el enum al estado anterior
        DB::statement("ALTER TABLE products MODIFY COLUMN service_type ENUM('desratizacion', 'desinsectacion', 'sanitizacion')");
    }
};
