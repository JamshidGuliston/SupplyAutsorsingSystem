<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBycostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bycosts', function (Blueprint $table) {
            $table->id();
            $table->integer('day_id');
            $table->integer('region_name_id');
            $table->integer('praduct_name_id');
            $table->integer('price_cost');
            $table->integer('tax_product');
            $table->double('waste_number', 8,3);
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
        Schema::dropIfExists('bycosts');
    }
}
