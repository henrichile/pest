<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("service_products", function (Blueprint $table) {
            $table->id();
            $table->foreignId("service_id")->constrained()->onDelete("cascade");
            $table->foreignId("product_id")->constrained()->onDelete("cascade");
            $table->integer("quantity")->default(1);
            $table->datetime("used_at")->nullable();
            $table->timestamps();
            
            $table->unique(["service_id", "product_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("service_products");
    }
};
