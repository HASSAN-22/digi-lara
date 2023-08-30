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
        Schema::create('becomesellerlegals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('becomeseller_id');
            $table->string('company_name');
            $table->string('company_type');
            $table->string('registration_number');
            $table->string('national_identity_number');
            $table->string('economic_number');
            $table->text('authorized_representative')->nullable();
            $table->text('address');
            $table->timestamps();
            $table->foreign('becomeseller_id')->references('id')->on('becomesellers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('becomesellerlegals');
    }
};
