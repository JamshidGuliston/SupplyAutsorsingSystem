<?php

namespace Database\Seeders;

use App\Models\typeofwork;
use Illuminate\Database\Seeder;

class TypeOfWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = typeofwork::all();
        if($rows->count() == 0){
            typeofwork::create([
                'type_name' => "Yetkazib beruvchi",
            ]);
            typeofwork::create([
                'type_name' => "Oddiy do'kon",
            ]);
        }
    }
}
