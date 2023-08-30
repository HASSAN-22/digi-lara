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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('weight_type',30);
            $table->string('is_free',10)->nullable();
            $table->string('is_freight',10)->nullable();
            $table->integer('fixed_price')->nullable();
            $table->integer('tax')->default(0);
            $table->integer('from_day');
            $table->integer('to_day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
