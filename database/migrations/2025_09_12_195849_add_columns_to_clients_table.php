<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("clients", function (Blueprint $table) {
            $table->string("name")->after("id");
            $table->string("rut")->after("name");
            $table->string("email")->after("rut");
            $table->string("phone")->after("email");
            $table->string("address")->after("phone");
            $table->string("business_type")->nullable()->after("address");
            $table->string("contact_person")->nullable()->after("business_type");
        });
    }

    public function down(): void
    {
        Schema::table("clients", function (Blueprint $table) {
            $table->dropColumn([
                "name",
                "rut", 
                "email",
                "phone",
                "address",
                "business_type",
                "contact_person"
            ]);
        });
    }
};
