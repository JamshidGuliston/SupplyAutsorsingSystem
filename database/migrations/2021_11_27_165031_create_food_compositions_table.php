<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_compositions', function (Blueprint $table) {
            $table->increments('food_com_id');
            $table->integer('food_name_id')->references('food_id')->on('food');
            $table->integer('product_name_id')->references('product_name_id')->on('products');
            $table->integer('age_name_id')->references('age_id')->on('age_ranges');
            $table->integer('product_weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_compositions');
    }
}
