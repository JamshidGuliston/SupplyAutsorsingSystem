<?php

namespace Database\Seeders;

use App\Models\Noyuks;
use Illuminate\Database\Seeder;

class noyuksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Noyuks::all();
        if($rows->count() == 0){
            Noyuks::create([
                'm_name' => "кунлик нормаси",
                'm_short' => "Норма",
            ]);

            Noyuks::create([
                'm_name' => "Оқсил",
                'm_short' => "Б",
            ]);

            Noyuks::create([
                'm_name' => "Ёғ",
                'm_short' => "Ж",
            ]);

            Noyuks::create([
                'm_name' => "Углевод",
                'm_short' => "У",
            ]);

            Noyuks::create([
                'm_name' => "Ккал",
                'm_short' => "Эн/ц",
            ]);
        }
    }
}
