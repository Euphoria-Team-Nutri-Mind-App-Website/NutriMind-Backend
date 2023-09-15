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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('national_id');
            $table->string('credit_card_number')->nullable();
            $table->string('vodafone_cash')->nullable();
            $table->enum('gender',['male','female'])->default('male');
            $table->string('image')->default('https://static.vecteezy.com/system/resources/thumbnails/009/734/564/small/default-avatar-profile-icon-of-social-media-user-vector.jpg');
            $table->string('qualification');
            $table->integer('experience_years');
            $table->integer('price');
            $table->integer('rate')->nullable();
            $table->string('verfication_code')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
