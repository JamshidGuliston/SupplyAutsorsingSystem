<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddelkadirRoleSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('roles')->where('id', 8)->exists();
        if ($exists) {
            return;
        }
        DB::table('roles')->insert([
            'id' => 8,
            'name' => 'addelkadir',
            'display_name' => 'Addelkadir',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
