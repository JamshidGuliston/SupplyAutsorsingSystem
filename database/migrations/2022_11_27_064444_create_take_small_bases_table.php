<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakeSmallBasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('take_small_bases', function (Blueprint $table) {
            $table->id();
            $table->integer("kindgarden_id");
            $table->integer("takegroup_id");
            $table->integer("product_id");
            $table->double('weight', 8,3);
            $table->integer("cost");
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
        Schema::dropIfExists('take_small_bases');
    }
}
