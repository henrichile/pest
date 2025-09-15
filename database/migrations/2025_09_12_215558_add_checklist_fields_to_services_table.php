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
        Schema::table("services", function (Blueprint $table) {
            // Checklist de puntos
            $table->integer("installed_points")->nullable()->comment("Puntos instalados");
            $table->integer("existing_points")->nullable()->comment("Puntos existentes");
            $table->integer("spare_points")->nullable()->comment("Puntos de repuesto");
            $table->decimal("bait_weight", 8, 2)->nullable()->comment("Peso de cebo instalado (g)");
            
            // Productos aplicados
            $table->text("applied_products")->nullable()->comment("Productos aplicados");
            
            // Resultados observados (JSON para selección múltiple)
            $table->json("observed_results")->nullable()->comment("Resultados observados");
            
            // Campos adicionales
            $table->integer("total_installed_points")->nullable()->comment("Puntos instalados totales");
            $table->decimal("total_consumption_activity", 8, 2)->nullable()->comment("Actividad de consumo total");
            $table->text("treated_sites")->nullable()->comment("Sitios tratados");
            
            // Estado del checklist
            $table->boolean("checklist_completed")->default(false)->comment("Checklist completado");
            $table->timestamp("checklist_completed_at")->nullable()->comment("Fecha de completado del checklist");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->dropColumn([
                "installed_points",
                "existing_points", 
                "spare_points",
                "bait_weight",
                "applied_products",
                "observed_results",
                "total_installed_points",
                "total_consumption_activity",
                "treated_sites",
                "checklist_completed",
                "checklist_completed_at"
            ]);
        });
    }
};
