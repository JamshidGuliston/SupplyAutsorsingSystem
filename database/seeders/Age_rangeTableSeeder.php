<?php

namespace Database\Seeders;

use App\Models\Age_range;
use Illuminate\Database\Seeder;

class Age_rangeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Age_range::all();
        if($rows->count() == 0){
            Age_range::create([
                'age_name' => "4-7 лет"
            ]);
    
            Age_range::create([
                'age_name' => "3-4 лет"
            ]);
    
            Age_range::create([
                'age_name' => "количество детей в кратковр.гр."
            ]);
        }
    }
}
