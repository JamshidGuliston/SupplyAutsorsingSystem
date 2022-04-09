<?php

namespace Database\Seeders;

use App\Models\Food_category;
use Illuminate\Database\Seeder;

class Food_categoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Food_category::all();
        if($rows->count() == 0){
            
            Food_category::create([
                'food_cat_name' => "Таомлар",
            ]);
            
            Food_category::create([
                'food_cat_name' => "Салатлар",
            ]);

            Food_category::create([
                'food_cat_name' => "Мевалар",
            ]);

            Food_category::create([
                'food_cat_name' => "Ичимликлар",
            ]);

            Food_category::create([
                'food_cat_name' => "Ёрма",
            ]);

        }
    }
}
