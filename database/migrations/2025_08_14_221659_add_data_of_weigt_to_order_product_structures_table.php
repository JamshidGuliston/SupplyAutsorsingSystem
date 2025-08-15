<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataOfWeigtToOrderProductStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_product_structures', function (Blueprint $table) {
            $table->json('data_of_weight')->nullable()->after('product_weight');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_product_structures', function (Blueprint $table) {
            //
        });
    }
}
