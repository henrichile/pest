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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address')->comment('Latitud GPS del servicio');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')->comment('Longitud GPS del servicio');
            $table->decimal('location_accuracy', 8, 2)->nullable()->after('longitude')->comment('Precisión de ubicación en metros');
            $table->timestamp('location_captured_at')->nullable()->after('location_accuracy')->comment('Fecha y hora de captura de ubicación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude', 
                'location_accuracy',
                'location_captured_at'
            ]);
        });
    }
};
