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
        Schema::create("service_observations", function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->constrained()->onDelete("cascade");
            $table->string("bait_station_code")->nullable()->comment("Código de la cebadera (N° CE)");
            $table->integer("observation_number")->comment("Número de observación (N° OBS)");
            $table->text("detail")->comment("Detalle escrito");
            $table->string("photo_path")->nullable()->comment("Ruta de la foto");
            $table->text("complementary_observation")->nullable()->comment("Observación complementaria");
            $table->timestamps();
            
            $table->index(["service_id", "observation_number"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("service_observations");
    }
};
