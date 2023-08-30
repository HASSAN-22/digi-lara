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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('parent_id');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->string('type');
            $table->string('image')->nullable();
            $table->string('status');
            $table->string('weight_type',30)->nullable();
            $table->string('icon')->nullable();
            $table->integer('commission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
