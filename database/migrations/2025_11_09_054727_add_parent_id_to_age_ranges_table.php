<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToAgeRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('age_ranges', function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->after('description')->constrained('age_ranges')->onDelete('cascade');
        });
    }
}
