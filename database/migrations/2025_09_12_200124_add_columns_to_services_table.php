<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->foreignId("client_id")->constrained()->onDelete("cascade")->after("id");
            $table->enum("service_type", ["desratizacion", "desinsectacion", "sanitizacion"])->after("client_id");
            $table->datetime("scheduled_date")->after("service_type");
            $table->string("address")->after("scheduled_date");
            $table->enum("priority", ["alta", "media", "baja"])->after("address");
            $table->enum("status", ["pendiente", "en_progreso", "vencido", "finalizado"])->default("pendiente")->after("priority");
            $table->text("description")->nullable()->after("status");
            $table->foreignId("assigned_to")->nullable()->constrained("users")->onDelete("set null")->after("description");
            $table->datetime("started_at")->nullable()->after("assigned_to");
            $table->datetime("completed_at")->nullable()->after("started_at");
        });
    }

    public function down(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->dropForeign(["assigned_to"]);
            $table->dropColumn([
                "client_id",
                "service_type",
                "scheduled_date", 
                "address",
                "priority",
                "status",
                "description",
                "assigned_to",
                "started_at",
                "completed_at"
            ]);
        });
    }
};
