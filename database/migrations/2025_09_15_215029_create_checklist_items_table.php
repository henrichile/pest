<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['text', 'number', 'select', 'checkbox', 'file']);
            $table->text('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(1);
            $table->foreignId('checklist_template_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('checklist_items');
    }
};
