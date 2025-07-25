<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGramToFoodCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('food_compositions', function (Blueprint $table) {
            $table->decimal('gram', 8, 3)->default(0)->after('product_name_id')->comment('Maxsulot miqdori gramda');
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
            $table->dropColumn('gram');
        });
    }
}
