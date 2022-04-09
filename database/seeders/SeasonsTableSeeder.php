<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Season::all();
        if($rows->count() == 0){
            Season::create([
                'season_name' => "Зима",
                'season_image' => "...",
                'hide' => 0
            ]);

            Season::create([
                'season_name' => "Весна",
                'season_image' => "...",
                'hide' => 0
            ]);

            Season::create([
                'season_name' => "Лето",
                'season_image' => "...",
                'hide' => 0
            ]);

            Season::create([
                'season_name' => "Осень",
                'season_image' => "...",
                'hide' => 0
            ]);
        }
    }
}
