<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToTitlemenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('titlemenus', function (Blueprint $table) {
            $table->string('short_name')->nullable()->after('menu_name');
            $table->integer('order_number')->nullable()->after('short_name');
        });
    }   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('titlemenus', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('order_number');
        });
    }
}
