<?php

namespace Database\Seeders;

use App\Models\Product_category;
use Illuminate\Database\Seeder;

class product_categoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Product_category::all();
        if($rows->count() == 0){
            Product_category::create([
                'pro_cat_name' => "Савзаводлар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Мевалар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Сут махсулотлари",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Кўкатлар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Ёрмалар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Қандолат махсулотлари",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Шакар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Гўшт махсулотлари",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Парранда махсулотлари",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Балиқ",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Тайёр махсулотлар",
		        'pro_cat_image' => "...",
            ]);

            Product_category::create([
                'pro_cat_name' => "Ярим тайёр махсулотлар",
		        'pro_cat_image' => "...",
            ]);

        }
    }
}
