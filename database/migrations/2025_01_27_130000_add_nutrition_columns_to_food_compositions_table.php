<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNutritionColumnsToFoodCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('food_compositions', function (Blueprint $table) {
            $table->decimal('weight_without_waste', 8, 2)->nullable()->after('gram')->comment('Chiqindisiz (gramm)');
            $table->decimal('proteins', 8, 2)->nullable()->after('weight_without_waste')->comment('Oqsillar (gramm)');
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
        Schema::table('food_compositions', function (Blueprint $table) {
            $table->dropColumn(['weight_without_waste', 'proteins', 'fats', 'carbohydrates', 'kcal']);
        });
    }
} 