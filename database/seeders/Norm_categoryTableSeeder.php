<?php

namespace Database\Seeders;

use App\Models\Norm_category;
use Illuminate\Database\Seeder;

class Norm_categoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = Norm_category::all();
        if($rows->count() == 0){
            // 1-
            Norm_category::create([
                'norm_name' => "Витамин ва минераллар билан бойитилган олий ва 1-навли унидан нон маҳсулотлари",
            ]);
            // 2-
            Norm_category::create([
                'norm_name' => "Олий ва 1-навли ун",
            ]);
            // 3-
            Norm_category::create([
                'norm_name' => "Крахмал",
            ]);
            // 4-
            Norm_category::create([
                'norm_name' => "Ёрмалар, дуккакли дон маҳсулотлари, макарон маҳсулотлари, шу жумладан, мош-3 гр",
            ]);
            // 5-
            Norm_category::create([
                'norm_name' => "Шакар",
            ]);
            // 6-
            Norm_category::create([
                'norm_name' => "Қандолат маҳсулотлари (повидло, джем, мураббо)",
            ]);
            // 7-
            Norm_category::create([
                'norm_name' => "Сариёғ (табиий, сигир сутдан олинган )",
            ]);
            // 8-
            Norm_category::create([
                'norm_name' => "Ўсимлик мойи",
            ]);
            // 9-
            Norm_category::create([
                'norm_name' => "Табиий сигир сути (ёғлилик даражаси 2,5 — 3,2%)",
            ]);
            // 10-
            Norm_category::create([
                'norm_name' => "Қатиқ, кефир.",
            ]);
            // 11-
            Norm_category::create([
                'norm_name' => "Сметана (ёғлилик даражаси 15%)",
            ]);
            // 12-
            Norm_category::create([
                'norm_name' => "Творог (ёғлилик даражаси 2,5%, 5,0%)",
            ]);
            // 13-
            Norm_category::create([
                'norm_name' => "Пишлоқ (шу жумладан қаттиқ турдаги)",
            ]);
            // 14-
            Norm_category::create([
                'norm_name' => "I категорияли мол, қўй, парранда, қуён гўшти",
            ]);
            // 15-
            Norm_category::create([
                'norm_name' => "Балиқ (тозаланган, бошсиз,музлатилган)",
            ]);
            // 16-
            Norm_category::create([
                'norm_name' => "Тухум (дона)",
            ]);
            // 17-
            Norm_category::create([
                'norm_name' => "Картошка",
            ]);
            // 18-
            Norm_category::create([
                'norm_name' => "Сабзавотлар(шу жумладан помидор пастаси 3 — 5 гр)",
            ]);
            // 19-
            Norm_category::create([
                'norm_name' => "Мевалар, резаворлар, шарбатлар.",
            ]);
            // 20-
            Norm_category::create([
                'norm_name' => "Қуруқ мевалар (туршак, майиз, олхўри, олма, наматак, ёнғоқ)",
            ]);
            // 21-
            Norm_category::create([
                'norm_name' => "Чой",
            ]);
            // 22-
            Norm_category::create([
                'norm_name' => "Какао",
            ]);
            // 23-
            Norm_category::create([
                'norm_name' => "Йодланган ош тузи,табиий таъм ростловчилар (зира, кашнич дон)",
            ]);
            // 24-
            Norm_category::create([
                'norm_name' => "Хамиртуруш, разрыхлитель-(пишириқ кукуни), улучшитель-(хамирни яхшилайдиган восита), пишевая сода-(ош содаси)",
            ]);
        }
    }
}
