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
        Schema::create('work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->json('start_geo')->nullable(); // Coordenadas de inicio {lat, lng}
            $table->json('end_geo')->nullable(); // Coordenadas de fin {lat, lng}
            $table->integer('duration_seconds')->nullable(); // Duración en segundos
            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index(['work_order_id', 'user_id']);
            $table->index('user_id');
            $table->index('start_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_sessions');
    }
};
