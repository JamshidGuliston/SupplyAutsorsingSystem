<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            YearSeeder::class,
            Age_rangeTableSeeder::class,
            Meal_timeTableSeeder::class,
            SeasonsTableSeeder::class,
            UsersTableSeeder::class,
            SizeTableSeeder::class,
            Food_categoryTableSeeder::class,
            product_categoriesTableSeeder::class,
            noyuksTableSeeder::class,
            Norm_categoryTableSeeder::class,
            NormTableSeeder::class,
            TypeOfWorkSeeder::class,
        ]);
    }
}
