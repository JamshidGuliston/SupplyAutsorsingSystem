<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinusMultiStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minus_multi_storages', function (Blueprint $table) {
            $table->id('id');
            $table->integer('day_id');
            $table->integer('kingarden_name_id');
            $table->integer('kingar_menu_id');
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
        Schema::dropIfExists('minus_multi_storages');
    }
}
