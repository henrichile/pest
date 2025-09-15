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
            $table->timestamp('pdf_generated_at')->nullable();
            $table->string('pdf_validation_id')->nullable();
            $table->string('pdf_integrity_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['pdf_generated_at', 'pdf_validation_id', 'pdf_integrity_hash']);
        });
    }
};
