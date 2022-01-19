<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuDayIdToMenuCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_compositions', function (Blueprint $table) {
            $table->Integer('menu_day_id')->after('menu_meal_time_id');
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
            $table->dropColumn('menu_day_id');
        });
    }
}
