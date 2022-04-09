<?php

namespace Database\Seeders;

use App\Models\Norm;
use Illuminate\Database\Seeder;

class NormTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Norm::all();
        if($rows->count() == 0){
            // 1.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 7.7,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 3,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 49.8,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 257.00,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 90,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 6.93,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 2.7,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 44.82,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 231.30,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 50,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 3.85,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 1.5,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 24.9,
            ]);

            Norm::create([
                'norm_cat_id' => 1,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 128.50,
            ]);
            // 1.cat end
            // 2.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 30,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 3.18,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.39,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 20.31,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 97.47,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 25,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 2.65,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.33,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 16.93,
            ]);

            Norm::create([
                'norm_cat_id' => 2,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 81.29,
            ]);
            // 2.cat end
            // 3.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 3,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.003,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 2.39,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 9.57,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 3,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.003,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 2.39,
            ]);

            Norm::create([
                'norm_cat_id' => 3,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 9.57,
            ]);
            // 3.cat end
            // 4.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 45,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 6.21,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 1.8,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 27,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 142.56,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 35,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 4.83,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.84,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 21,
            ]);

            Norm::create([
                'norm_cat_id' => 4,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 110.88,
            ]);
            // 4.cat end
            // 5.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 25,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 24.95,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 99.80,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 19.96,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 79.84,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 5,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 4.99,
            ]);

            Norm::create([
                'norm_cat_id' => 5,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 19.96,
            ]);
            // 5.cat end
            // 6.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.03,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 6.81,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 27.36,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.03,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 6.81,
            ]);

            Norm::create([
                'norm_cat_id' => 6,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 27.36,
            ]);
            // 6.cat end
            // 7.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.1,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 16.5,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.16,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 149.54,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 15,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.08,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 12.38,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.12,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 112.22,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 0.05,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 8.25,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 0.08,
            ]);

            Norm::create([
                'norm_cat_id' => 7,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 74.77,
            ]);
            // 7.cat end
            // 8.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 8,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 7.99,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 71.91,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 7,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 6.99,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 8,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 62.91,
            ]);
            // 8.cat end
            // 9.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 200,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 5.6,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 6.4,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 9.4,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 117.60,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 180,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 5.04,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 5.76,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 8.46,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 105.84,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 2.8,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 3.2,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 4.7,
            ]);

            Norm::create([
                'norm_cat_id' => 9,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 58.80,
            ]);
            // 9.cat end
            // 10.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 2.8,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 3.2,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 4.1,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 56.40,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 2.8,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 3.2,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 4.1,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 56.40,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 2.8,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 3.2,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 4.1,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 56.40,
            ]);
            // 10.cat end
            // 11.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 5,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.12,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 1.5,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.16,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 14.62,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 5,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.12,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 1.5,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.16,
            ]);

            Norm::create([
                'norm_cat_id' => 11,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 14.62,
            ]);
            // 11.cat end
            // 12.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 3.34,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 1.8,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.4,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 31.16,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 3.34,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 1.8,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.4,
            ]);

            Norm::create([
                'norm_cat_id' => 12,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 31.16,
            ]);
            // 12.cat end
            // 13.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 2.5,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 2.5,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 32.50,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 8,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 2,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 2,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 26.00,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 5,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 1.25,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 1.25,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 13,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 16.25,
            ]);
            // 13.cat end
            // 14.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 80,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 14.88,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 12.8,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 174.72,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 60,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 11.16,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 9.6,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 14,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 131.04,
            ]);
            // 14.cat end
            // 15.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 3.64,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 16.8,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 20,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 3.64,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 15,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 16.18,
            ]);
            // 15.cat end
            // 16.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 0.5,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 6.35,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 5.75,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.35,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 78.55,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 0.5,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 6.35,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 5.75,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.35,
            ]);

            Norm::create([
                'norm_cat_id' => 16,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 78.55,
            ]);
            // 16.cat end
            // 17.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 120,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 2.4,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.48
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 20.76,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 96.96,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 2,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.4,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 17.3,
            ]);

            Norm::create([
                'norm_cat_id' => 17,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 80.80,
            ]);
            // 17.cat end
            // 18.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 180,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 2.34,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 10.8,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 54.18,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 150,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 1.95,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.15,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 9,
            ]);

            Norm::create([
                'norm_cat_id' => 18,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 45.15,
            ]);
            // 18.cat end
            // 19.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 150,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 1.62,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.6,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 19.8,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 91.08,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 130,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 1.404,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.52,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 17.16,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 78.94,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 100,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 1.08,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 0.4,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 13.2,
            ]);

            Norm::create([
                'norm_cat_id' => 19,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 60.72,
            ]);
            // 19.cat end
            // 20.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 5.5,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 22.72,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 10,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 5.5,
            ]);

            Norm::create([
                'norm_cat_id' => 20,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 22.72,
            ]);
            // 20.cat end
            // 21.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 0.3,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.06,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.02,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 0.32,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 0.3,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.06,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.02,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 0.32,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 0.3,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 0.06,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 0.02,
            ]);

            Norm::create([
                'norm_cat_id' => 21,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 0.32,
            ]);
            // 21.cat end
            // 22.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 2,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.48,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.35,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.55,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 7.27,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 2,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.48,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.35,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.558,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 7.30,
            ]);
            // qisqa
            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 3,
                'noyuk_id' => 1,
                'norm_weight' => 1,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 3,
                'noyuk_id' => 2,
                'norm_weight' => 0.24,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 3,
                'noyuk_id' => 3,
                'norm_weight' => 0.18,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 3,
                'noyuk_id' => 4,
                'norm_weight' => 0.28,
            ]);

            Norm::create([
                'norm_cat_id' => 22,
                'norm_age_id' => 3,
                'noyuk_id' => 5,
                'norm_weight' => 3.70,
            ]);
            // 22.cat end
            // 23.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 5,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 0,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 4,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0,
            ]);

            Norm::create([
                'norm_cat_id' => 10,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 0,
            ]);
            // 23.cat end
            // 24.cat
            // 4-7 yosh
            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 1,
                'noyuk_id' => 1,
                'norm_weight' => 1,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 1,
                'noyuk_id' => 2,
                'norm_weight' => 0.12,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 1,
                'noyuk_id' => 3,
                'norm_weight' => 0.004,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 1,
                'noyuk_id' => 4,
                'norm_weight' => 0.08,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 1,
                'noyuk_id' => 5,
                'norm_weight' => 0.84,
            ]);
            // 3-4 yosh
            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 2,
                'noyuk_id' => 1,
                'norm_weight' => 1,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 2,
                'noyuk_id' => 2,
                'norm_weight' => 0.12,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 2,
                'noyuk_id' => 3,
                'norm_weight' => 0.004,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 2,
                'noyuk_id' => 4,
                'norm_weight' => 0.08,
            ]);

            Norm::create([
                'norm_cat_id' => 24,
                'norm_age_id' => 2,
                'noyuk_id' => 5,
                'norm_weight' => 0.84,
            ]);
            // 24.cat end

        }
        
            
    }
}
