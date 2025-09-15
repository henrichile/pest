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
            // Campos adicionales del checklist
            $table->boolean("installed_points_check")->default(false)->comment("Check puntos instalados");
            $table->boolean("existing_points_check")->default(false)->comment("Check puntos existentes");
            $table->boolean("spare_points_check")->default(false)->comment("Check puntos de repuesto");
            $table->boolean("bait_weight_check")->default(false)->comment("Check peso cebo instalado");
            $table->boolean("physical_installed_check")->default(false)->comment("Check puntos físicos instalados");
            $table->boolean("physical_existing_check")->default(false)->comment("Check puntos físicos existentes");
            $table->boolean("physical_spare_check")->default(false)->comment("Check puntos físicos de repuesto");
            
            // Producto aplicado seleccionado
            $table->string("applied_product")->nullable()->comment("Producto aplicado seleccionado");
            
            // Descripción del servicio
            $table->text("service_description")->nullable()->comment("Descripción del servicio y sugerencias");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->dropColumn([
                "installed_points_check",
                "existing_points_check", 
                "spare_points_check",
                "bait_weight_check",
                "physical_installed_check",
                "physical_existing_check",
                "physical_spare_check",
                "applied_product",
                "service_description"
            ]);
        });
    }
};
