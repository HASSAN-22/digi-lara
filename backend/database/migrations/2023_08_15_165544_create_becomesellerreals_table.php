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
        Schema::create('becomesellerreals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('becomeseller_id');
            $table->string('name');
            $table->string('last_name');
            $table->string('birth_day');
            $table->string('gender');
            $table->string('identity_card_number');
            $table->string('national_identity_number');
            $table->timestamps();
            $table->foreign('becomeseller_id')->references('id')->on('becomesellers')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('becomesellerreals');
    }
};
