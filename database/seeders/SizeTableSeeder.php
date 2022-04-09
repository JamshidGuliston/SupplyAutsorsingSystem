<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Size::all();
        if($rows->count() == 0){
            Size::create([
                'size_name' => "кг"
            ]);

            Size::create([
                'size_name' => "шт"
            ]);

            Size::create([
                'size_name' => "литр"
            ]);
        }
    }
}
