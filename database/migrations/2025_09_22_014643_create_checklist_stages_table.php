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
        Schema::create('checklist_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_template_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nombre de la etapa
            $table->text('description')->nullable(); // Descripción de la etapa
            $table->integer('order_index')->default(0); // Orden de la etapa
            $table->boolean('is_required')->default(true); // Si la etapa es obligatoria
            $table->timestamps();

            // Índices
            $table->index(['checklist_template_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_stages');
    }
};
