<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductNameIdToMenuCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_compositions', function (Blueprint $table) {
            $table->integer('product_name_id')->after('menu_food_id');
            $table->integer('age_range_id')->after('product_name_id');
            $table->double('weight'. 8, 5)->after('age_range_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_compositions', function (Blueprint $table) {
            //
        });
    }
}
