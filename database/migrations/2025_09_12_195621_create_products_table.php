<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("active_ingredient");
            $table->enum("service_type", ["desratizacion", "desinsectacion", "sanitizacion"]);
            $table->string("sag_registration")->nullable();
            $table->string("isp_registration")->nullable();
            $table->integer("stock")->default(0);
            $table->string("unit")->default("unidad");
            $table->text("description")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("products");
    }
};
