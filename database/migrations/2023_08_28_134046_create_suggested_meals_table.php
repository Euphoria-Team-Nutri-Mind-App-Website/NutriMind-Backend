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
        Schema::create('suggested_meals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('details');
            $table->double('calories');
            $table->double('protein');
            $table->double('fats');
            $table->double('carbs');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggested_meals');
    }
};
