<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlusMultiStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plus_multi_storages', function (Blueprint $table) {
            $table->id('id');
            $table->integer('day_id');
            $table->integer('kingarden_name_d');
            $table->integer('order_product_id');
            $table->integer('product_name_id');
            $table->double('product_weight', 8, 3);
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
        Schema::dropIfExists('plus_multi_storages');
    }
}
