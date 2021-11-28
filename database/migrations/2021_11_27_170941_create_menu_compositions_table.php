<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_compositions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('one_day_menu_id')->references('id')->on('one_day_menus');
            $table->integer('menu_meal_time_id')->references('id')->on('meal_times');
            $table->integer('menu_food_id')->references('id')->on('food');
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
        Schema::dropIfExists('menu_compositions');
    }
}
