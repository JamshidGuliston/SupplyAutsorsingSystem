<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAgeNameIdColumFromFoodCompositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('food_compositions', function (Blueprint $table) {
            $table->dropColumn('age_name_id');
            $table->dropColumn('product_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('food_compositions', function (Blueprint $table) {
            //
        });
    }
}
