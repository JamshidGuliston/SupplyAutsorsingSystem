<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKindgardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kindgardens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('region_id');
            $table->string('kingar_name');
            $table->string('kingar_password');
            $table->biginteger('telegram_user_id');
            $table->integer('worker_count');
            $table->boolean('hide');
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
        Schema::dropIfExists('kindgardens');
    }
}
