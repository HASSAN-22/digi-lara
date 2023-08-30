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
        Schema::create('legalinfos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('province_id');
            $table->foreignId('city_id');
            $table->string('organization_name')->unique();
            $table->string('economic_code')->unique()->nullable();
            $table->string('national_id')->unique();
            $table->string('registration_id')->unique();
            $table->string('phone')->unique();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalinfos');
    }
};
