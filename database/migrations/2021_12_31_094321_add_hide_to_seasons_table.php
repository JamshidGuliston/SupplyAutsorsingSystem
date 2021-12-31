<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideToSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->boolean('hide')->after('season_image');
        });
    }

    /**
     * Reverse the migrations.
     *php artisan migrate:rollback --path=/database/migrations/2021_12_31_094321_add_hide_to_seasons_table.php
     *php artisan make:migration add_hide_to_seasons_table --table=seasons
     * @return void
     */
    public function down()
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn('hide');
        });
    }
}
