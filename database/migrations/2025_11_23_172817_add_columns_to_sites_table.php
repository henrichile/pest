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
        Schema::table('sites', function (Blueprint $table) {
            $table->foreignId('client_id')->after('id')->constrained('clients')->onDelete('cascade');
            $table->string('name')->after('client_id');
            $table->string('address')->nullable()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('region')->nullable()->after('city');
            $table->string('country')->nullable()->after('region');
            $table->string('postal_code')->nullable()->after('country');
            $table->string('type')->nullable()->after('postal_code');
            $table->text('notes')->nullable()->after('type');
            $table->boolean('is_active')->default(true)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id',
                'name',
                'address',
                'city',
                'region',
                'country',
                'postal_code',
                'type',
                'notes',
                'is_active'
            ]);
        });
    }
};
