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
            $table->increments('id');
            $table->integer('food_name_id')->references('id')->on('food');
            $table->integer('product_name_id')->references('id')->on('products');
            // $table->double('product_weight', 8, 3);
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
