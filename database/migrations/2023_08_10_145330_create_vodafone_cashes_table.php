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
        Schema::create('vodafone_cashes', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->char('patient_phone_number',11);
            $table->char('doctor_phone_number',11);
            $table->string('receipt_image');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vodafone_cashes');
    }
};
