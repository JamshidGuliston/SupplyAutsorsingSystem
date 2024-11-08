<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableForUniqueKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plus_multi_storages', function (Blueprint $table) {
            $table->unique(['order_product_id', 'kingarden_name_d', 'product_name_id'], 'order_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plus_multi_storages', function (Blueprint $table) {
            //
        });
    }
}
