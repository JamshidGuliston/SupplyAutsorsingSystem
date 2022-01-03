<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddLargeWerehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_large_werehouses', function (Blueprint $table) {
            $table->id();
            $table->integer('add_group_id');
            $table->integer('shop_id');
            $table->integer('product_id');
            $table->double('weight', 8, 3);
            $table->integer('cost');
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
        Schema::dropIfExists('add_large_werehouses');
    }
}
