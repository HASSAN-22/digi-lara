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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('brand_id');
            $table->foreignId('category_id');
            $table->foreignId('guarantee_id');
            $table->string('ir_name')->unique();
            $table->string('en_name')->unique()->nullable();
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->integer('price');
            $table->integer('amazing_offer_percent')->nullable();
            $table->string('amazing_offer_status')->nullable();
            $table->string('amazing_offer_expire')->nullable();
            $table->integer('amazing_price')->nullable();
            $table->integer('guarantee_month')->nullable();
            $table->integer('packing_length');
            $table->integer('packing_width');
            $table->integer('packing_height');
            $table->integer('packing_weight');
            $table->integer('physical_length');
            $table->integer('physical_width');
            $table->integer('physical_height');
            $table->integer('physical_weight');
            $table->integer('count');
            $table->string('image');
            $table->string('is_original');
            $table->text('strengths')->nullable();
            $table->text('weak_points')->nullable();
            $table->text('description');
            $table->text('more_description')->nullable();
            $table->string('publish');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('guarantee_id')->references('id')->on('guarantees')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
