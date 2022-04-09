<?php

namespace Database\Seeders;

use App\Models\Meal_time;
use Illuminate\Database\Seeder;

class Meal_timeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Meal_time::all();
        if($rows->count() == 0){
            Meal_time::create([
                'meal_time_name' => "Первый завтрак"
            ]);

            Meal_time::create([
                'meal_time_name' => "Второй завтрак"
            ]);

            Meal_time::create([
                'meal_time_name' => "ОБЕД"
            ]);

            Meal_time::create([
                'meal_time_name' => "ПОЛДНИК"
            ]);
            
        }
    }
}
