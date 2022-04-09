<?php

namespace Database\Seeders;

use App\Models\Month;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Month::all();
        if($rows->count() == 0){
            Month::create([
                'month_name' => "Январ",
                'month_en' => "Yanvar",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Феврал",
                'month_en' => "Fevral",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Март",
                'month_en' => "Маrt",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Апрел",
                'month_en' => "Aprel",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Май",
                'month_en' => "Маy",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Июн",
                'month_en' => "Iyun",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Июл",
                'month_en' => "Iyul",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Август",
                'month_en' => "Аvgust",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Сентиябр",
                'month_en' => "Sentabr",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Октиябр",
                'month_en' => "Oktabr",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Ноябр",
                'month_en' => "Noyabr",
                'month_active' => 0
            ]);

            Month::create([
                'month_name' => "Декабр",
                'month_en' => "Dekabr",
                'month_active' => 0
            ]);
        }
    }
}
