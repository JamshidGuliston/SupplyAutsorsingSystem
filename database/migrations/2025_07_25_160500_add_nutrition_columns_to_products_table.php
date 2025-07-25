<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNutritionColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('proteins', 8, 3)->default(0)->after('hide')->comment('Oqsillar (100gr uchun)');
            $table->decimal('fats', 8, 3)->default(0)->after('proteins')->comment('Yog\'lar (100gr uchun)');
            $table->decimal('carbohydrates', 8, 3)->default(0)->after('fats')->comment('Uglevods (100gr uchun)');
            $table->decimal('kcal', 8, 3)->default(0)->after('carbohydrates')->comment('Kaloriya (100gr uchun)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['proteins', 'fats', 'carbohydrates', 'kcal']);
        });
    }
} 