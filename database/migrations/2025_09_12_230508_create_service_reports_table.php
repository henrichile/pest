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
        Schema::create("service_reports", function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->constrained()->onDelete("cascade");
            $table->foreignId("generated_by")->constrained("users")->onDelete("cascade");
            $table->string("report_number")->unique();
            $table->string("file_path");
            $table->string("qr_code")->unique();
            $table->json("report_data"); // Datos completos del reporte
            $table->timestamp("generated_at");
            $table->timestamps();
            
            $table->index(["service_id", "generated_at"]);
            $table->index("report_number");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("service_reports");
    }
};
