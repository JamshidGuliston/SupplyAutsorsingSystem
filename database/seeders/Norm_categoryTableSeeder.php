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
                'norm_name_short' => "1 сорт буғдой НОНи (600 гр дона)",
            ]);
            // 2-
            Norm_category::create([
                'norm_name' => "Олий ва 1-навли ун",
                'norm_name_short' => "1 сорт буғдой УНи",
            ]);
            // 3-
            Norm_category::create([
                'norm_name' => "Крахмал",
                'norm_name_short' => "Кисел",
            ]);
            // 4-
            Norm_category::create([
                'norm_name' => "Ёрмалар, дуккакли дон маҳсулотлари, макарон маҳсулотлари, шу жумладан, мош-3 гр",
                'norm_name_short' => "Ёрма, макарон маҳсулотлари",
            ]);
            // 5-
            Norm_category::create([
                'norm_name' => "Шакар",
                'norm_name_short' => "Шакар",
            ]);
            // 6-
            Norm_category::create([
                'norm_name' => "Қандолат маҳсулотлари (повидло, джем, мураббо)",
                'norm_name_short' => "Қандолат маҳсулотлари",
            ]);
            // 7-
            Norm_category::create([
                'norm_name' => "Сариёғ (табиий, сигир сутдан олинган )",
                'norm_name_short' => "Сариёғ",
            ]);
            // 8-
            Norm_category::create([
                'norm_name' => "Ўсимлик мойи",
                'norm_name_short' => "Ўсимлик ёғи",
            ]);
            // 9-
            Norm_category::create([
                'norm_name' => "Табиий сигир сути (ёғлилик даражаси 2,5 — 3,2%)",
                'norm_name_short' => "Сут",
            ]);
            // 10-
            Norm_category::create([
                'norm_name' => "Қатиқ, кефир.",
                'norm_name_short' => "Кефир",
            ]);
            // 11-
            Norm_category::create([
                'norm_name' => "Сметана (ёғлилик даражаси 15%)",
                'norm_name_short' => "Сметана",
            ]);
            // 12-
            Norm_category::create([
                'norm_name' => "Творог (ёғлилик даражаси 2,5%, 5,0%)",
                'norm_name_short' => "Творог",
            ]);
            // 13-
            Norm_category::create([
                'norm_name' => "Пишлоқ (шу жумладан қаттиқ турдаги)",
                'norm_name_short' => "Пишлоқ",
            ]);
            // 14-
            Norm_category::create([
                'norm_name' => "I категорияли мол, қўй, парранда, қуён гўшти",
                'norm_name_short' => "Гўшт, парранда гўшти",
            ]);
            // 15-
            Norm_category::create([
                'norm_name' => "Балиқ (тозаланган, бошсиз,музлатилган)",
                'norm_name_short' => "Музлатилган балиқ",
            ]);
            // 16-
            Norm_category::create([
                'norm_name' => "Тухум (дона)",
                'norm_name_short' => "Тухум (дона)",
            ]);
            // 17-
            Norm_category::create([
                'norm_name' => "Картошка",
                'norm_name_short' => "Картошка",
            ]);
            // 18-
            Norm_category::create([
                'norm_name' => "Сабзавотлар(шу жумладан помидор пастаси 3 — 5 гр)",
                'norm_name_short' => "Сабзавот шу жумладан томат",
            ]);
            // 19-
            Norm_category::create([
                'norm_name' => "Мевалар, резаворлар, шарбатлар.",
                'norm_name_short' => "Мевалар, резоворлар, шарбатлар",
            ]);
            // 20-
            Norm_category::create([
                'norm_name' => "Қуруқ мевалар (туршак, майиз, олхўри, олма, наматак, ёнғоқ)",
                'norm_name_short' => "Қуруқ мевалар",
            ]);
            // 21-
            Norm_category::create([
                'norm_name' => "Чой",
                'norm_name_short' => "Чой",
            ]);
            // 22-
            Norm_category::create([
                'norm_name' => "Какао",
                'norm_name_short' => "Какао",
            ]);
            // 23-
            Norm_category::create([
                'norm_name' => "Йодланган ош тузи,табиий таъм ростловчилар (зира, кашнич дон)",
                'norm_name_short' => "Йўдланган туз",
            ]);
            // 24-
            Norm_category::create([
                'norm_name' => "Хамиртуруш, разрыхлитель-(пишириқ кукуни), улучшитель-(хамирни яхшилайдиган восита), пишевая сода-(ош содаси)",
                'norm_name_short' => "Дрожа қуруқ",
            ]);
        }
    }
}
