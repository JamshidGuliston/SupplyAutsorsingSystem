<?php

namespace Database\Seeders;

use App\Models\Month;
use App\Models\Year;
use Illuminate\Database\Seeder;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Year::all();
        $start = 2021;
        $moths = [["Yanvar", "January"], ["Fevral", "February"], ["Mart", "March"], ["Aprel", "April"], ["May", "May"], ["Iyun", "June"], ["Iyul", "July"], ["Avgust", "August"], ["Sentabr", "September"], ["Oktabr", "October"], ["Noyabr", "November"], ["Dekabr", "December"]];
        if($rows->count() == 0){
            for($t = 1; $t<50; $t++){
                $y = Year::create([
                    'year_name' => $start + $t,
                    'year_active' => 0
                ]);
                foreach($moths as $moth){
                    Month::create([
                        'month_name' => $moth[0],
                        'month_en' => $moth[1],
                        'yearid' => $y->id,
                        'month_active' => 0
                    ]); 
                }
            }
        }
    }
}
