<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumberChildrensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('number_childrens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kingar_name_id');
            $table->integer('day_id');
            $table->integer('king_age_name_id');
            $table->integer('kingar_children_number');
            $table->integer('workers_count');
            $table->integer('kingar_menu_id');
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
        Schema::dropIfExists('number_childrens');
    }
}
