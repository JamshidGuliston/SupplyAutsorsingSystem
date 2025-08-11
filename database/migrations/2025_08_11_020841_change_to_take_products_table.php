<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeToTakeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('take_products', function (Blueprint $table) {
            $table->integer('takegroup_id')->nullable()->change();
            $table->integer('sale_id')->nullable()->after('takegroup_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('take_products', function (Blueprint $table) {
            $table->dropColumn('sale_id');
            $table->dropSoftDeletes();
        });
    }
}
