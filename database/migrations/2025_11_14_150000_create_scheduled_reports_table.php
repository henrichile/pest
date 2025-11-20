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
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('services'); // services, clients, technicians, financial
            $table->string('format')->default('pdf'); // pdf, csv, excel
            $table->string('frequency')->default('monthly'); // daily, weekly, monthly, quarterly, yearly
            $table->json('filters')->nullable(); // Filtros guardados (fechas, clientes, técnicos, etc.)
            $table->json('recipients')->nullable(); // Emails de destinatarios
            $table->boolean('is_active')->default(true);
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};

