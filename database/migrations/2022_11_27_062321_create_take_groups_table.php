<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('take_groups', function (Blueprint $table) {
            $table->id();
            $table->integer("contur_id");
            $table->integer("day_id");
            $table->integer("taker_id");
            $table->integer("outside_id");
            $table->string("title");
            $table->string("description");
            $table->boolean("group_hide");
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
        Schema::dropIfExists('take_groups');
    }
}
