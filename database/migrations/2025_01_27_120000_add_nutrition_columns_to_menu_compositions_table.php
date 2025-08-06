<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNutritionColumnsToMenuCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_compositions', function (Blueprint $table) {
            $table->decimal('waste_free', 8, 2)->nullable()->after('weight')->comment('Chiqindisiz (gramm)');
            $table->decimal('proteins', 8, 2)->nullable()->after('waste_free')->comment('Oqsillar (gramm)');
            $table->decimal('fats', 8, 2)->nullable()->after('proteins')->comment('Yog\'lar (gramm)');
            $table->decimal('carbohydrates', 8, 2)->nullable()->after('fats')->comment('Uglevodlar (gramm)');
            $table->decimal('kcal', 8, 2)->nullable()->after('carbohydrates')->comment('Kaloriya');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_compositions', function (Blueprint $table) {
            $table->dropColumn(['waste_free', 'proteins', 'fats', 'carbohydrates', 'kcal']);
        });
    }
} 