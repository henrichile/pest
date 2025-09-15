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
            // Etapa actual del checklist
            $table->string("checklist_stage")->default("points")->comment("Etapa actual del checklist: points, products, results, observations, sites, description");
            $table->json("checklist_data")->nullable()->comment("Datos temporales del checklist por etapa");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->dropColumn([
                "checklist_stage",
                "checklist_data"
            ]);
        });
    }
};
