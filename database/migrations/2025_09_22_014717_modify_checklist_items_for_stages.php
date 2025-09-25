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
        Schema::table('checklist_items', function (Blueprint $table) {
            // Agregar referencia a la etapa
            $table->foreignId('checklist_stage_id')->nullable()->after('checklist_template_id')->constrained()->onDelete('cascade');
            
            // Agregar orden dentro de la etapa
            $table->integer('order_index')->default(0)->after('is_required');
            
            // Índices
            $table->index(['checklist_stage_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropForeign(['checklist_stage_id']);
            $table->dropIndex(['checklist_stage_id', 'order_index']);
            $table->dropColumn(['checklist_stage_id', 'order_index']);
        });
    }
};
