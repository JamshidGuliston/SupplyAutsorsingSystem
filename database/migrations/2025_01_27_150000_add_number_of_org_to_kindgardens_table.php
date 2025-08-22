<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberOfOrgToKindgardensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->string('number_of_org')->nullable()->after('worker_age_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kindgardens', function (Blueprint $table) {
            $table->dropColumn('number_of_org');
        });
    }
} 