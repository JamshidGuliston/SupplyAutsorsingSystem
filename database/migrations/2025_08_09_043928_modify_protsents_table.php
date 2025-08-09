<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProtsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('protsents', function (Blueprint $table) {
            // month_id ustunini o'chirish
            $table->dropColumn('month_id');
            // Yangi ustunlar qo'shish
            $table->double('eater_cost')->after('region_id')->nullable();
            $table->date('start_date')->after('eater_cost')->nullable();
            $table->date('end_date')->after('start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('protsents', function (Blueprint $table) {
            // Yangi ustunlarni o'chirish
            $table->dropColumn(['eater_cost', 'start_date', 'end_date']);
            
            // month_id ustunini qaytarish
            $table->integer('month_id')->after('region_id');
        });
    }
}
