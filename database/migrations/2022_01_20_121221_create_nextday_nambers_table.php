<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNextdayNambersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nextday_nambers', function (Blueprint $table) {
            $table->id();
            $table->integer('kingar_name_id');
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
        Schema::dropIfExists('nextday_nambers');
    }
}
