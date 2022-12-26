<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpendCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spend_cashes', function (Blueprint $table) {
            $table->id();
            $table->integer('allcost_id');
            $table->integer('day_id');
            $table->integer('summ');
            $table->integer('description');
            $table->integer('status');
            $table->boolean('summ_hide');
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
        Schema::dropIfExists('spend_cashes');
    }
}
