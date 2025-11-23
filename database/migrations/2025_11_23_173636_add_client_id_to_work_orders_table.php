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
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('client_id')->after('id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('site_id')->after('client_id')->nullable()->constrained('sites')->onDelete('cascade');
            $table->foreignId('service_id')->after('site_id')->nullable()->constrained('services')->onDelete('set null');
            $table->string('folio')->after('service_id')->unique()->nullable();
            $table->string('status')->after('folio')->default('pending');
            $table->dateTime('scheduled_date')->after('status')->nullable();
            $table->dateTime('started_at')->after('scheduled_date')->nullable();
            $table->dateTime('completed_at')->after('started_at')->nullable();
            $table->text('notes')->after('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['site_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn([
                'client_id',
                'site_id',
                'service_id',
                'folio',
                'status',
                'scheduled_date',
                'started_at',
                'completed_at',
                'notes'
            ]);
        });
    }
};
