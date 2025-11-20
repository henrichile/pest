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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default company settings (only if they don't exist)
        if (!DB::table('settings')->where('key', 'company_name')->exists()) {
            DB::table('settings')->insert([
                ['key' => 'company_name', 'value' => 'PestController', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'company_rut', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'company_address', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'company_phone', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'company_email', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

