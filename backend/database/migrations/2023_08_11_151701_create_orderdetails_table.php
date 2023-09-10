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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('order_id');
            $table->foreignId('product_id');
            $table->string('name');
            $table->string('sku');
            $table->integer('price');
            $table->string('image');
            $table->integer('count');
            $table->integer('discount');
            $table->string('discount_type');
            $table->string('property_type');
            $table->string('property_name');
            $table->integer('property_price');
            $table->string('property_color')->nullable();
            $table->integer('amount');
            $table->string('shipping_status');
            $table->integer('commission');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderdetails');
    }
};
