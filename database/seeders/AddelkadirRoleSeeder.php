<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Role;

class AddelkadirRoleSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrNew(['name' => 'addelkadir']);
        if (!$role->exists) {
            $role->fill(['display_name' => 'Addelkadir'])->save();
        }
    }
}
