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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('pest_id')->nullable()->constrained('pests')->onDelete('set null');
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('set null');
            $table->decimal('dose', 10, 2)->nullable(); // Dosis aplicada
            $table->string('unit')->nullable(); // Unidad de medida (ml, g, kg, etc.)
            $table->string('method')->nullable(); // Método de aplicación
            $table->integer('safety_time_hours')->nullable(); // Tiempo de seguridad en horas
            $table->foreignId('evidence_media_id')->nullable()->constrained('media')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index('work_order_id');
            $table->index('pest_id');
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
