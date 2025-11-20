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
        Schema::table('pests', function (Blueprint $table) {
            if (!Schema::hasColumn('pests', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('pests', 'category')) {
                $table->string('category')->after('name');
            }
            if (!Schema::hasColumn('pests', 'technical_notes')) {
                $table->text('technical_notes')->nullable()->after('category');
            }
            if (!Schema::hasColumn('pests', 'control_methods')) {
                $table->json('control_methods')->nullable()->after('technical_notes');
            }
            if (!Schema::hasColumn('pests', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('control_methods');
            }
            if (!Schema::hasColumn('pests', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pests', function (Blueprint $table) {
            $table->dropColumn(['name', 'category', 'technical_notes', 'control_methods', 'is_active', 'deleted_at']);
        });
    }
};

