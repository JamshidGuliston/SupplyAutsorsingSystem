<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;
use App\Models\Age_range;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use App\Models\Day;
use App\Models\Food;
use App\Models\Food_category;
use App\Models\Food_composition;
use App\Models\Month;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Kindgarden;
use App\Models\Year;
use App\Models\Temporary;
use App\Models\Groupweight;
use App\Models\Weightproduct;
use App\Models\Menu_composition;
use App\Models\bycosts;
use App\Models\Number_children;
use App\Models\Titlemenu;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\history_process;
use App\Models\Protsent;
use App\Models\Meal_time;
use App\Models\minus_multi_storage;
use App\Models\Nextday_namber;
use App\Models\Norm_category;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Product_category;
use App\Models\Outside_product;
use App\Models\Sale;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Take_group;
use App\Models\Size;
use App\Models\Take_small_base;
use App\Models\titlemenu_food;
use App\Models\typeofwork;
use App\Models\User;
use App\Models\ChildrenCountHistory;
use App\Models\Notification;
use App\Models\StorageChangeLog;
use Database\Seeders\TypeOfWorkSeeder;
use Telegram\Bot\Api;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use TCG\Voyager\Models\Category;

class TechnologController extends Controller
{
    public function days()
    {
        $days = Day::join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->orderby('days.id', 'DESC')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function rangeOfDays($start, $end)
    {
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function activmonth($month_id)
    {
        $month = Month::where('id', $month_id)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $month->yearid)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'days.month_id', 'years.year_name', 'days.year_id']);
        return $days;
    }

    public function index(Request $request)
    {
        $year = Year::where('year_active', 1)->first();
        $months = Month::where('yearid', Year::where('year_active', 1)->first()->id)->get();

        // faqat aktiv oy sanalarini oladi
        $days = Day::where('month_id', Month::where('month_active', 1)->first()->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->select('days.id', 'days.day_number', 'days.month_id', 'months.month_name', 'years.year_name', 'days.year_id')
            ->get();

        $monthsofyears = Month::where('months.id', '<=', $days->first()->month_id)
            ->join('years', 'years.id', '=', 'months.yearid')
            ->orderBy('months.id', 'DESC')
            ->get(['months.id', 'months.month_name', 'years.year_name']);

        $kingar = Kindgarden::all();
        $nextdaymenu = Nextday_namber::all();
        $season = Season::where('hide', 1)->first();
        $menus = Titlemenu::where('menu_season_id', $season->id)->get();

        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        return view('technolog.home', ['year' => $year, 'date' => $days, 'tomm' => $d, 'kingardens' => $kingar, 'menus' => $menus, 'next' => $nextdaymenu, 'months' => $months, 'monthsofyears' => $monthsofyears]);
    }

    // yangi kun ishlari
    public function newday(Request $request)
    {
        Temporary::truncate();
        $year = Year::where('year_name', $request->dayyear)->first();
        $acyear = Year::where('year_active', 1)->first();
        if ($year->id != $acyear->id) {
            Year::where('year_active', 1)->update(['year_active' => 0]);
            Month::where('yearid', $acyear->id)->where('month_active', 1)->update(['month_active' => 0]);
            Year::where('year_name', $request->dayyear)->update(['year_active' => 1]);
        }
        $months = Month::where('yearid', $year->id)->get();
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        foreach ($months as $month) {
            if ($month->month_en == date("F", $d)) {
                $month->update(['month_active' => 1]);
                $activeID = $month;
            }
            else {
                $month->update(['month_active' => 0]);
            }
        }
        // dd($activeID);
        $newday = Day::where('year_id', $year->id)
            ->where('month_id', $activeID->id)
            ->where('day_number', date("d", $d))->first();

        if (empty($newday->day_number)) {
            $newday = Day::create([
                'day_number' => date("d", $d),
                'month_id' => $activeID->id,
                'year_id' => $year->id
            ]);
        }

        // vaqtinchalik keyingi kun menyusini bugungi kungi menyu sifatida ishlatishni boshlaydi
        sleep(4);
        $nextdays = Nextday_namber::orderBy('kingar_name_id', 'ASC')->get();
        $endday = Day::orderBy('id', 'DESC')->first();
        foreach ($nextdays as $nextrow) {
            $king = Kindgarden::where('id', $nextrow->kingar_name_id)->where('hide', 1)->first();
            if (isset($king->id)) {
                Temporary::create([
                    'kingar_name_id' => $nextrow->kingar_name_id,
                    'workers' => $nextrow->workers_count,
                    'age_id' => $nextrow->king_age_name_id,
                    'age_number' => $nextrow->kingar_children_number
                ]);
            }
            Number_children::create([
                'kingar_name_id' => $nextrow->kingar_name_id,
                'day_id' => $endday->id,
                'king_age_name_id' => $nextrow->king_age_name_id,
                'kingar_children_number' => $nextrow->kingar_children_number,
                'workers_count' => $nextrow->workers_count,
                'kingar_menu_id' => $nextrow->kingar_menu_id,
            ]);
            $findmenu = Active_menu::where('day_id', $endday->id)->where('title_menu_id', $nextrow->kingar_menu_id)->get();
            if ($findmenu->count() == 0) {
                $menuitems = Menu_composition::where('title_menu_id', $nextrow->kingar_menu_id)
                    ->orderby('menu_meal_time_id', 'ASC')
                    ->orderby('id', 'ASC')
                    ->get();
                foreach ($menuitems as $row) {
                    Active_menu::create([
                        'day_id' => $endday->id,
                        'title_menu_id' => $row->title_menu_id,
                        'menu_meal_time_id' => $row->menu_meal_time_id,
                        'menu_food_id' => $row->menu_food_id,
                        'product_name_id' => $row->product_name_id,
                        'age_range_id' => $row->age_range_id,
                        'weight' => $row->weight
                    ]);
                }
            }
        }
        // yetkazib beruvchilarga zayavkalarni $endday->id kuniga saqlash
        $shops = Shop::where('hide', 1)->with('kindgarden.region')->with('product')->get();
        foreach ($shops as $shop) {
            $orderProduct = array();
            foreach ($shop->kindgarden as $row) {
                $orderCheck = order_product::where('kingar_name_id', $row->id)->where('day_id', $endday->id)->where('shop_id', $shop->id)->first();
                if (!$orderCheck) {
                    $orderProduct[$row->id] = order_product::create([
                        'kingar_name_id' => $row->id,
                        'day_id' => $endday->id,
                        'order_title' => date("d-m-Y H:i") . "Yetkazuvchi",
                        'document_processes_id' => 4,
                        'data_of_weight' => json_encode(now()),
                        'to_menus' => json_encode([]),
                        'shop_id' => $shop->id,
                    ]);
                }
            }
            foreach ($shop->kindgarden as $row) {
                foreach ($shop->product as $prod) {
                    $weight = 0;
                    foreach ($nextdays as $next) {
                        if ($row->id == $next->kingar_name_id) {
                            $workeat = titlemenu_food::where('day_id', $endday->id)->get();
                            $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                                ->where('age_range_id', $next->king_age_name_id)
                                ->where('product_name_id', $prod->id)
                                ->get();
                            foreach ($prlar as $prw) {
                                $weight += $prw->weight * $next->kingar_children_number;
                                if ($next->king_age_name_id == 4) {
                                    $workeat = titlemenu_food::where('day_id', $endday->id)
                                        ->where('food_id', $prw->menu_food_id)
                                        ->get();
                                    if ($workeat->count() > 0) {
                                        $weight += $prw->weight * $next->workers_count;
                                    }
                                }
                            }
                        }
                    }
                    $calculatedWeight = $weight / $prod->div;
                    $result = $calculatedWeight;
                    if ($prod->size_name_id == 3 or $prod->size_name_id == 2) {
                        $result = round($result);
                    }
                    else {
                        $result = round($result, 1);
                    }
                    if (isset($orderProduct[$row->id])) {
                        order_product_structure::create([
                            'order_product_name_id' => $orderProduct[$row->id]->id,
                            'product_name_id' => $prod->id,
                            'product_weight' => $result,
                            'actual_weight' => $calculatedWeight,
                        ]);
                    }
                }
            }
        }

        Nextday_namber::truncate();

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }


    public function sendmenu($day)
    {
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        $bool = array();
        $ages = Age_range::all();
        $sid = Season::where('hide', 1)->first();
        $menus = Titlemenu::leftjoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
            ->get(['titlemenus.id', 'titlemenus.menu_name', 'seasons.season_name']);
        if ($day == date("d-F-Y", $d)) {
            $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')
                ->orderby('kindgardens.id', 'ASC')->get();

            $gar = Kindgarden::where('hide', 1)->with('age_range')->get();
            // unset($gar[0]);
            // dd($gar);
            $mass = array();
            $loo = 0;
            for ($i = 0; $i < count($gr); $i++) {
                $mass[$loo]['id'] = $gr[$i]->id;
                $mass[$loo]['name'] = $gr[$i]->kingar_name;
                $mass[$loo]['workers'] = $gr[$i]->worker_count;
                // for($l=0; $l<count($age); $l++){
                $kages = Kindgarden::find($gr[$i]->id);
                // dd($kages->age_range);
                foreach ($kages->age_range as $age) {
                    if ($age->id == $gr[$i]->age_id) {
                        $mass[$loo][$age->id] = $gr[$i]->age_number;
                    }
                    if (empty($mass[$loo][$age->id]) and $age->id > 0 and $age->id != $gr[$i]->age_id) {
                        $mass[$loo][$age->id] = "-";
                    }
                }
                for ($j = 0; $j < count($gar); $j++) {
                    if ($gar[$j]['id'] == $gr[$i]['id']) {
                        $gar[$j]['ok'] = 1;
                    }
                }
                if ($i + 1 < count($gr) and $gr[$i + 1]->id != $mass[$loo]['id']) {
                    $loo++;
                }
            }
            $activ = Kindgarden::where('hide', 1)->get();
            $temp = Temporary::all();
            // yangi kun uchun bolalar soni o'zgartirish tarixini olish
            $childrenCountHistory = ChildrenCountHistory::where('created_at', '>=', date('Y-m-d 00:00:00'))->get();
            $nextday = Nextday_namber::join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                ->leftjoin('temporaries', function ($join) {
                $join->on('nextday_nambers.kingar_name_id', '=', 'temporaries.kingar_name_id');
                $join->on('nextday_nambers.king_age_name_id', '=', 'temporaries.age_id');
            })
                ->leftjoin('titlemenus', 'nextday_nambers.kingar_menu_id', '=', 'titlemenus.id')
                ->orderby('nextday_nambers.kingar_name_id', 'ASC')
                ->get([
                'nextday_nambers.id',
                'nextday_nambers.king_age_name_id',
                'nextday_nambers.kingar_children_number',
                'nextday_nambers.workers_count',
                'nextday_nambers.kingar_menu_id',
                'titlemenus.menu_name',
                'nextday_nambers.kingar_name_id',
                'kindgardens.id as kingarid',
                'kindgardens.kingar_name',
                'temporaries.id as tempid',
                'temporaries.age_number',
                'nextday_nambers.created_at',
                'nextday_nambers.updated_at'
            ]);
            $nextdayitem = array();
            $loo = 0;
            for ($i = 0; $i < count($nextday); $i++) {
                $ct = Nextday_namber::where('kingar_name_id', $nextday[$i]->kingar_name_id)->where('king_age_name_id', $nextday[$i]->king_age_name_id)->get();
                if ($ct->count() > 1) {
                    Nextday_namber::where('kingar_name_id', $nextday[$i]->kingar_name_id)->where('king_age_name_id', $nextday[$i]->king_age_name_id)->first()->delete();
                }
                $nextdayitem[$loo]['id'] = $nextday[$i]->id;
                $nextdayitem[$loo]['kingar_name_id'] = $nextday[$i]->kingar_name_id;
                $nextdayitem[$loo]['created_at'] = $nextday[$i]->created_at;
                $nextdayitem[$loo]['updated_at'] = $nextday[$i]->updated_at;
                $nextdayitem[$loo]['kingar_name'] = $nextday[$i]->kingar_name;
                $nextdayitem[$loo][$nextday[$i]->king_age_name_id] = array($nextday[$i]->id, $nextday[$i]->kingar_children_number, $nextday[$i]->tempid, $nextday[$i]->age_number, $nextday[$i]->kingar_menu_id, $nextday[$i]->menu_name);
                $nextdayitem[$loo]['workers_count'] = $nextday[$i]->workers_count;
                if ($i + 1 < count($nextday) and $nextday[$i + 1]->kingar_name_id != $nextdayitem[$loo]['kingar_name_id']) {
                    $loo++;
                }
            }

            $shops = Shop::where('hide', 1)->where('type_id', 1)->with('kindgarden')->with('product')->get();
            $endday = Day::orderBy('id', 'DESC')->first();
            // Har bir shop uchun zayavka saqlanganligini tekshirish
            $shopOrderStatus = [];

            foreach ($shops as $shop) {
                $hasOrder = order_product::where('shop_id', $shop->id)
                    ->where('day_id', $endday->id)
                    ->exists();

                $shopOrderStatus[$shop->id] = $hasOrder;
            }

            $mf = titlemenu_food::orderBy('day_id', 'DESC')->first();
            $sendmenu = 0;

            if (isset($mf->day_id) and $endday->id == $mf->day_id) {
                $sendmenu = 1;
            }
            $nextday = 1;
            $allmenus = Titlemenu::join('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
                ->get(['titlemenus.id', 'titlemenus.menu_name', 'seasons.season_name']);
            return view('technolog.newday', [
                'temp' => $temp,
                'sendmenu' => $sendmenu,
                'nextdayitem' => $nextdayitem,
                'shops' => $shops,
                'ages' => $ages,
                'menus' => $menus,
                'temps' => $mass,
                'gardens' => $gar,
                'activ' => $activ,
                'allmenus' => $allmenus,
                'shopOrderStatus' => $shopOrderStatus,
                'childrenCountHistory' => $childrenCountHistory
            ]);
        }
    }

    // public function showdate(Request $request, $year_id, $month_id, $day_id)
    // {
    //     $year = Year::where('id', $year_id)->first();
    //     $months = Month::where('yearid', $year->id)->get();
    //     $days = Day::where('month_id', $month_id)->where('year_id', $year->id)
    //             ->join('months', 'months.id', '=', 'days.month_id')
    //             ->join('years', 'years.id', '=', 'days.year_id')
    //             ->get(['days.id', 'days.day_number', 'months.month_name', 'days.month_id', 'years.year_name', 'days.year_id']);

    //     $ages = Age_range::all();
    //     $nextdayitem = [];
    //     $usage_status = [];

    //     $kingar = Kindgarden::where('hide', 1)->get();

    //     foreach($kingar as $row){
    //         $nextdayitem[] = [
    //             'kingar_name_id' => $row->id,
    //             'kingar_name' => $row->kingar_name,
    //             'workers_count' => $row->workers_count,
    //         ];
    //     }

    //     // Har bir bog'cha va yosh guruhi uchun ma'lumotlarni olish
    //     foreach($nextdayitem as $key => $item){
    //         foreach($ages as $age){
    //             $number_children = Number_children::where('day_id', $day_id)
    //                 ->where('kingar_name_id', $item['kingar_name_id'])
    //                 ->where('king_age_name_id', $age->id)
    //                 ->first();

    //             if($number_children){
    //                 $nextdayitem[$key][$age->id] = [
    //                     1 => $number_children->kingar_children_number,
    //                     2 => $number_children->workers_count
    //                 ];
    //             }
    //         }

    //         // Mahsulotlar ishlatilganligi statusini olish
    //         $usage_status[$item['kingar_name_id']] = 'Sarflanmagan'; // Boshlang'ich holat
    //     }

    //     return view('technolog.showdate', compact('year', 'months', 'days', 'ages', 'nextdayitem', 'usage_status', 'day_id', 'month_id', 'year_id'));
    // }

    public function showdate($y_id, $m_id, $day)
    {
        $year = Year::where('id', $y_id)->first();
        if ($m_id == 0) {
            $m_id = Month::where('yearid', $y_id)->first()->id;
        }
        if ($day == 0) {
            $day = Day::where('month_id', $m_id)->first()->id;
        }
        // dd($day);
        $months = Month::where('yearid', $y_id)->get();
        $ages = Age_range::all();
        $nextday = Number_children::where('day_id', $day)->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
            ->leftjoin('titlemenus', 'number_childrens.kingar_menu_id', '=', 'titlemenus.id')
            ->orderby('number_childrens.kingar_name_id', 'ASC')
            ->get();

        $nextdayitem = array();
        $loo = 0;
        $days = Day::where('month_id', $m_id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->select('days.id', 'days.day_number', 'days.month_id', 'months.month_name', 'years.year_name', 'days.year_id')
            ->orderBy('days.day_number', 'ASC')
            ->get();

        // Har bir kun uchun mahsulotlar ishlatilganligini tekshirish
        $usage_status = [];
        foreach ($nextday as $kindgarden) {
            $has_usage = minus_multi_storage::where('day_id', $day)
                ->where('kingarden_name_id', $kindgarden->kingar_name_id)
                ->exists();
            $usage_status[$kindgarden->kingar_name_id] = $has_usage ? 'Sarflangan' : 'Sarflanmagan';

        }

        for ($i = 0; $i < count($nextday); $i++) {
            $nextdayitem[$loo]['kingar_name_id'] = $nextday[$i]->kingar_name_id;
            $nextdayitem[$loo]['kingar_name'] = $nextday[$i]->kingar_name;
            $nextdayitem[$loo][$nextday[$i]->king_age_name_id] = array($nextday[$i]->id, $nextday[$i]->kingar_children_number, $nextday[$i]->tempid, $nextday[$i]->age_number, $nextday[$i]->kingar_menu_id, $nextday[$i]->menu_name);
            $nextdayitem[$loo]['workers_count'] = $nextday[$i]->workers_count;
            if ($i + 1 < count($nextday) and $nextday[$i + 1]->kingar_name_id != $nextdayitem[$loo]['kingar_name_id']) {
                $loo++;
            }

        }
        return view('technolog.showdate', ['year' => $year, 'y_id' => $y_id, 'm_id' => $m_id, 'aday' => $day, 'months' => $months, 'days' => $days, 'ages' => $ages, 'nextdayitem' => $nextdayitem, 'usage_status' => $usage_status]);
    }

    // Yangi bog'cha qo'shish (showdate sahifasi uchun)
    public function storeNumberChildren(Request $request)
    {
        try {
            $request->validate([
                'day_id' => 'required|integer|exists:days,id',
                'kingar_name_id' => 'required|integer|exists:kindgardens,id',
                'king_age_name_id' => 'required|integer|exists:age_ranges,id',
                'kingar_children_number' => 'required|integer|min:0',
                'workers_count' => 'required|integer|min:0',
                'kingar_menu_id' => 'nullable|integer|exists:titlemenus,id'
            ]);

            // Mavjud yozuvni tekshirish
            $existing = Number_children::where('day_id', $request->day_id)
                ->where('kingar_name_id', $request->kingar_name_id)
                ->where('king_age_name_id', $request->king_age_name_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu bog\'cha va yosh guruhi uchun ma\'lumot allaqachon mavjud!'
                ], 400);
            }

            Number_children::create([
                'day_id' => $request->day_id,
                'kingar_name_id' => $request->kingar_name_id,
                'king_age_name_id' => $request->king_age_name_id,
                'kingar_children_number' => $request->kingar_children_number,
                'workers_count' => $request->workers_count,
                'kingar_menu_id' => $request->kingar_menu_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bog\'cha muvaffaqiyatli qo\'shildi!'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Soft delete (showdate sahifasi uchun)
    public function softDeleteNumberChildren(Request $request)
    {
        try {
            $request->validate([
                'day_id' => 'required|integer',
                'kingar_name_id' => 'required|integer'
            ]);

            $deleted = Number_children::where('day_id', $request->day_id)
                ->where('kingar_name_id', $request->kingar_name_id)
                ->delete(); // SoftDeletes trait tufayli bu soft delete bo'ladi

            if ($deleted > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Muvaffaqiyatli o\'chirildi! (' . $deleted . ' ta yozuv)'
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'O\'chiriladigan ma\'lumot topilmadi!'
                ], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // O'chirilgan qatorlarni olish (showdate sahifasi uchun)
    public function getDeletedNumberChildren(Request $request)
    {
        try {
            $dayId = $request->input('day_id');

            $deletedRecords = Number_children::onlyTrashed()
                ->where('day_id', $dayId)
                ->with(['kindergarten', 'ageRange'])
                ->get()
                ->map(function ($record) {
                return [
                'id' => $record->id,
                'kingar_name_id' => $record->kingar_name_id,
                'kingar_name' => $record->kindergarten ? $record->kindergarten->kingar_name : 'Noma\'lum',
                'king_age_name_id' => $record->king_age_name_id,
                'age_name' => $record->ageRange ? $record->ageRange->age_name : 'Noma\'lum',
                'kingar_children_number' => $record->kingar_children_number,
                'workers_count' => $record->workers_count,
                'deleted_at' => $record->deleted_at->format('d.m.Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $deletedRecords
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // O'chirilgan qatorni tiklash (showdate sahifasi uchun)
    public function restoreNumberChildren(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer'
            ]);

            $record = Number_children::onlyTrashed()->find($request->id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiklanadigan yozuv topilmadi!'
                ], 404);
            }

            $record->restore();

            return response()->json([
                'success' => true,
                'message' => 'Muvaffaqiyatli tiklandi!'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Barcha o'chirilgan qatorlarni tiklash (showdate sahifasi uchun)
    public function restoreAllNumberChildren(Request $request)
    {
        try {
            $request->validate([
                'day_id' => 'required|integer'
            ]);

            $restored = Number_children::onlyTrashed()
                ->where('day_id', $request->day_id)
                ->restore();

            if ($restored > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Muvaffaqiyatli tiklandi! (' . $restored . ' ta yozuv)'
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiklanadigan ma\'lumot topilmadi!'
                ], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Yangi kun qo'shish (showdate sahifasi uchun)
    public function storeDay(Request $request)
    {
        try {
            $request->validate([
                'year_id' => 'required|integer',
                'month_id' => 'required|integer',
                'day_number' => 'required|integer|min:1|max:31'
            ]);

            // Bu kun allaqachon mavjudligini tekshirish
            $existing = Day::where('year_id', $request->year_id)
                ->where('month_id', $request->month_id)
                ->where('day_number', $request->day_number)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kun allaqachon mavjud! (' . $request->day_number . '-kun)'
                ], 400);
            }

            // Yil va oy ma'lumotlarini olish
            $year = Year::find($request->year_id);
            $month = Month::find($request->month_id);

            // Oy raqamini aniqlash (month_en dan)
            $monthNames = [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ];
            $monthNumber = $monthNames[strtolower($month->month_en)] ?? 1;

            // Sana yaratish
            $dateString = $year->year_name . '-' . str_pad($monthNumber, 2, '0', STR_PAD_LEFT) . '-' . str_pad($request->day_number, 2, '0', STR_PAD_LEFT) . ' 10:00:00';

            // Yangi kun yaratish
            $newDay = Day::create([
                'year_id' => $request->year_id,
                'month_id' => $request->month_id,
                'day_number' => $request->day_number,
            ]);

            // created_at va updated_at ni yangilash
            $newDay->created_at = $dateString;
            $newDay->updated_at = $dateString;
            $newDay->save();

            return response()->json([
                'success' => true,
                'message' => 'Yangi kun muvaffaqiyatli qo\'shildi! (' . $request->day_number . '-kun)',
                'day_id' => $newDay->id
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // O'tgan kunlar uchun ma'lumot qo'shish
    public function addPastDaysData(Request $request)
    {
        try {
            $daysBack = $request->input('days_back', 30); // Default 30 kun
            $addedCount = Number_children::addPastDaysData($daysBack);

            return response()->json([
                'success' => true,
                'message' => $addedCount . ' ta yangi ma\'lumot qo\'shildi',
                'added_count' => $addedCount
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Bog'chalar ro'yxatini olish
    public function getKindergartens()
    {
        $kindergartens = Kindgarden::where('hide', 1)->get(['id', 'kingar_name']);

        return response()->json([
            'kindergartens' => $kindergartens
        ]);
    }

    // Yosh guruhlari ro'yxatini olish
    public function getAgeRanges()
    {
        $ageRanges = Age_range::all(['id', 'age_name']);

        return response()->json([
            'age_ranges' => $ageRanges
        ]);
    }

    // O'tgan kunlarga bog'chalarni biriktirish
    public function assignPastDays(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $kindergartens = $request->input('kindergartens', []);
            $ageRanges = $request->input('age_ranges', []);

            if (empty($kindergartens) || empty($ageRanges)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bog\'cha va yosh guruhlari tanlanmagan'
                ]);
            }

            // Kunlarni olish
            $days = Day::whereBetween('created_at', [$startDate, $endDate])->get();

            $addedCount = 0;

            foreach ($days as $day) {
                foreach ($kindergartens as $kindergartenId) {
                    foreach ($ageRanges as $ageRangeId) {
                        // Mavjud ma'lumotni tekshirish
                        $existing = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindergartenId)
                            ->where('king_age_name_id', $ageRangeId)
                            ->first();

                        if (!$existing) {
                            // Yangi ma'lumot qo'shish
                            Number_children::create([
                                'kingar_name_id' => $kindergartenId,
                                'day_id' => $day->id,
                                'king_age_name_id' => $ageRangeId,
                                'kingar_children_number' => 0,
                                'workers_count' => 0,
                                'kingar_menu_id' => null,
                            ]);
                            $addedCount++;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => $addedCount . ' ta yangi ma\'lumot qo\'shildi',
                'added_count' => $addedCount
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Excel orqali ma'lumot yuklash sahifasi
    public function importExcelPage()
    {
        $kindergartens = Kindgarden::where('hide', 1)->orderBy('kingar_name')->get();
        return view('technolog.import_excel', compact('kindergartens'));
    }

    // Excel faylni import qilish
    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'kindergarten_id' => 'required|integer|exists:kindgardens,id',
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
            ]);

            $kindergartenId = $request->kindergarten_id;
            $file = $request->file('excel_file');

            $monthNames = [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ];

            // Excel faylni o'qish
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array) { return $array; }
            }, $file);

            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel fayl bo\'sh yoki noto\'g\'ri formatda!'
                ], 400);
            }

            $rows = $data[0];
            // Birinchi qatorni sarlavha deb o'tkazish
            $header = array_shift($rows);

            $details = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($rows as $index => $row) {
                // Bo'sh qatorni o'tkazish
                if (empty($row[0])) continue;

                $dateValue = $row[0]; // Sana
                $childrenCount = isset($row[2]) && $row[2] !== null && $row[2] !== '' ? (int)$row[2] : null; // Bolalar soni
                $ageRangeName = isset($row[3]) ? trim($row[3]) : ''; // Yosh toifasi
                $menuName = isset($row[4]) ? trim($row[4]) : ''; // Menu

                // Bolalar soni berilmagan bo'lsa - o'tkazib yuborish
                if ($childrenCount === null || $childrenCount <= 0) {
                    continue;
                }

                // Sanani parse qilish
                $dayNumber = null;
                $monthNumber = null;
                $yearNumber = null;

                if (is_numeric($dateValue)) {
                    // Excel serial date format
                    $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                    $dayNumber = (int)$dateObj->format('d');
                    $monthNumber = (int)$dateObj->format('m');
                    $yearNumber = (int)$dateObj->format('Y');
                } elseif (is_string($dateValue)) {
                    // String formatda sana (DD.MM.YYYY yoki DD/MM/YYYY yoki YYYY-MM-DD)
                    $dateValue = str_replace('/', '.', $dateValue);
                    if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $dateValue, $matches)) {
                        $dayNumber = (int)$matches[1];
                        $monthNumber = (int)$matches[2];
                        $yearNumber = (int)$matches[3];
                    } elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $dateValue, $matches)) {
                        $yearNumber = (int)$matches[1];
                        $monthNumber = (int)$matches[2];
                        $dayNumber = (int)$matches[3];
                    }
                }

                if (!$dayNumber || !$monthNumber || !$yearNumber) {
                    $errorCount++;
                    $details[] = [
                        'date' => $dateValue,
                        'children_count' => $childrenCount,
                        'age_range' => $ageRangeName,
                        'menu' => $menuName,
                        'status' => 'error',
                        'message' => 'Sana formatini aniqlab bo\'lmadi'
                    ];
                    continue;
                }

                $dateStr = str_pad($dayNumber, 2, '0', STR_PAD_LEFT) . '.' . str_pad($monthNumber, 2, '0', STR_PAD_LEFT) . '.' . $yearNumber;

                // Year topish yoki yaratish
                $year = Year::where('year_name', $yearNumber)->first();
                if (!$year) {
                    $year = Year::create([
                        'year_name' => $yearNumber,
                        'year_active' => 0
                    ]);
                }

                // Month topish yoki yaratish
                $monthEnNames = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                $monthUzNames = [
                    1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
                    5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
                    9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr'
                ];

                $month = Month::where('yearid', $year->id)
                    ->where('month_en', strtolower($monthEnNames[$monthNumber]))
                    ->first();

                if (!$month) {
                    $month = Month::create([
                        'month_name' => $monthUzNames[$monthNumber],
                        'month_en' => strtolower($monthEnNames[$monthNumber]),
                        'yearid' => $year->id,
                        'month_active' => 0
                    ]);
                }

                // Day topish yoki yaratish
                $day = Day::where('day_number', $dayNumber)
                    ->where('month_id', $month->id)
                    ->where('year_id', $year->id)
                    ->first();

                if (!$day) {
                    $day = Day::create([
                        'day_number' => $dayNumber,
                        'month_id' => $month->id,
                        'year_id' => $year->id
                    ]);
                    // created_at va updated_at ni to'g'ri sanaga o'rnatish
                    $dateString = $yearNumber . '-' . str_pad($monthNumber, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dayNumber, 2, '0', STR_PAD_LEFT) . ' 10:00:00';
                    $day->created_at = $dateString;
                    $day->updated_at = $dateString;
                    $day->save();
                }

                // Yosh toifasini topish (ID yoki nom bo'yicha)
                $ageRange = null;
                if (!empty($ageRangeName) || $ageRangeName === '0' || $ageRangeName === 0) {
                    if (is_numeric($ageRangeName)) {
                        // Raqam bo'lsa - ID sifatida qidirish
                        $ageRange = Age_range::find((int)$ageRangeName);
                    } else {
                        // Matn bo'lsa - nom bo'yicha qidirish
                        $ageRange = Age_range::where('age_name', $ageRangeName)->first();
                        if (!$ageRange) {
                            $ageRange = Age_range::where('age_name', 'LIKE', '%' . $ageRangeName . '%')->first();
                        }
                    }
                }

                if (!$ageRange) {
                    $errorCount++;
                    $details[] = [
                        'date' => $dateStr,
                        'children_count' => $childrenCount,
                        'age_range' => $ageRangeName,
                        'menu' => $menuName,
                        'status' => 'error',
                        'message' => 'Yosh toifasi topilmadi: ' . $ageRangeName
                    ];
                    continue;
                }

                // Menu topish (ID yoki nom bo'yicha)
                $menu = null;
                if (!empty($menuName) || $menuName === '0' || $menuName === 0) {
                    if (is_numeric($menuName)) {
                        // Raqam bo'lsa - ID sifatida qidirish
                        $menu = Titlemenu::find((int)$menuName);
                    } else {
                        // Matn bo'lsa - nom bo'yicha qidirish
                        $menu = Titlemenu::where('menu_name', $menuName)
                            ->orWhere('short_name', $menuName)
                            ->first();
                        if (!$menu) {
                            $menu = Titlemenu::where('menu_name', 'LIKE', '%' . $menuName . '%')
                                ->orWhere('short_name', 'LIKE', '%' . $menuName . '%')
                                ->first();
                        }
                    }
                }

                // Mavjud yozuvni tekshirish
                $existing = Number_children::where('day_id', $day->id)
                    ->where('kingar_name_id', $kindergartenId)
                    ->where('king_age_name_id', $ageRange->id)
                    ->first();

                if ($existing) {
                    // Mavjud bo'lsa yangilash
                    $existing->update([
                        'kingar_children_number' => $childrenCount,
                        'kingar_menu_id' => $menu ? $menu->id : $existing->kingar_menu_id,
                    ]);
                    $activeMenuId = $menu ? $menu->id : $existing->kingar_menu_id;
                    $successCount++;
                    $details[] = [
                        'date' => $dateStr,
                        'children_count' => $childrenCount,
                        'age_range' => $ageRangeName,
                        'menu' => $menuName,
                        'status' => 'success',
                        'message' => 'Yangilandi'
                    ];
                } else {
                    // Yangi yozuv yaratish
                    Number_children::create([
                        'day_id' => $day->id,
                        'kingar_name_id' => $kindergartenId,
                        'king_age_name_id' => $ageRange->id,
                        'kingar_children_number' => $childrenCount,
                        'workers_count' => 0,
                        'kingar_menu_id' => $menu ? $menu->id : 1,
                    ]);
                    $activeMenuId = $menu ? $menu->id : 1;
                    $successCount++;
                    $details[] = [
                        'date' => $dateStr,
                        'children_count' => $childrenCount,
                        'age_range' => $ageRangeName,
                        'menu' => $menuName,
                        'status' => 'success',
                        'message' => 'Qo\'shildi'
                    ];
                }

                // Active_menu ga menyu tarkibini qo'shish (agar mavjud bo'lmasa)
                $findmenu = Active_menu::where('day_id', $day->id)
                    ->where('title_menu_id', $activeMenuId)
                    ->exists();
                if (!$findmenu) {
                    $menuitems = Menu_composition::where('title_menu_id', $activeMenuId)
                        ->orderBy('menu_meal_time_id', 'ASC')
                        ->orderBy('id', 'ASC')
                        ->get();
                    foreach ($menuitems as $menurow) {
                        Active_menu::create([
                            'day_id' => $day->id,
                            'title_menu_id' => $menurow->title_menu_id,
                            'menu_meal_time_id' => $menurow->menu_meal_time_id,
                            'menu_food_id' => $menurow->menu_food_id,
                            'product_name_id' => $menurow->product_name_id,
                            'age_range_id' => $menurow->age_range_id,
                            'weight' => $menurow->weight,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => $successCount . ' ta qator muvaffaqiyatli import qilindi!',
                'summary' => [
                    'total' => $successCount + $errorCount,
                    'success' => $successCount,
                    'errors' => $errorCount
                ],
                'details' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteGarden(Request $request)
    {
        // dd($request->all());
        $garden = Nextday_namber::where('kingar_name_id', $request->garden_id)->first();
        $garden->delete();
        return redirect()->back();
    }

    // yetkazib beruvchilar

    public function nextdelivershop(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('kindgarden.region')->with('product')->first();

        $shopproducts = array();
        foreach ($shop->kindgarden as $row) {
            $shopproducts[$row->id]['name'] = $row->kingar_name;
            $shopproducts[$row->id]['region_id'] = $row->region_id;
            $day = Day::orderBy('id', 'DESC')->first();
            foreach ($shop->product as $prod) {
                // echo $prod->id;
                $shopproducts[$row->id][$prod->id] = "";
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $month = Month::where('month_active', 1)->first();
                $days = Day::where('month_id', $month->id)->where('year_id', Year::where('year_active', 1)->first()->id)->first();
                $minus = minus_multi_storage::where('day_id', '>=', $days->id)->where('kingarden_name_id', $row->id)->where('product_name_id', $prod->id)->sum('product_weight');
                $plus = plus_multi_storage::where('day_id', '>=', $days->id)->where('kingarden_name_d', $row->id)->where('product_name_id', $prod->id)->sum('product_weight');
                // echo $row->kingar_name.' '.$prod->product_name.' '.$plus.'-'.$minus.'='.$plus-$minus.'<br>';
                // dd($plus);
                $weight = 0;
                $itempr = "";
                $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();
                // dd($nextday);
                foreach ($nextday as $next) {
                    if ($row->id == $next->kingar_name_id) {
                        $workeat = titlemenu_food::where('day_id', $day->id)->get();
                        $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)->where('age_range_id', $next->king_age_name_id)->where('product_name_id', $prod->id)->get();
                        foreach ($prlar as $prw) {
                            $itempr = $itempr . "+" . $prw->weight . " * " . $next->kingar_children_number;
                            $weight += $prw->weight * $next->kingar_children_number;
                            if ($next->king_age_name_id == 4) {
                                $workeat = titlemenu_food::where('day_id', $day->id)->where('food_id', $prw->menu_food_id)->get();
                                if ($workeat->count() > 0) {
                                    $weight += $prw->weight * $next->workers_count;
                                }

                            }
                        }
                    // $allsum += $weight * $next->kingar_children_number;
                    // $onesum += $weight; 
                    // $workers = $next->workers_count;
                    }
                }

                $prdiv = Product::where('id', $prod->id)->first();
                // $itempr . "=" .
                if ($plus - $minus < 0) {
                    $modweight = 0;
                }
                else {
                    $modweight = $plus - $minus;
                }
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div;

            // taminotchilar 
            // $bool = plus_multi_storage::where('day_id', 82)->where('kingarden_name_d', $row->id)->where('product_name_id', $prod->id)->get();
            // if($bool->count() == 0 and $weight != 0){
            //     plus_multi_storage::create([
            //         'day_id' => 81,
            //         'shop_id' => $id,
            //         'kingarden_name_d' => $row->id,
            //         'order_product_id' => 0,
            //         'product_name_id' => $prod->id,
            //         'product_weight' => $weight / $prod->div,
            //     ]);
            // }
            }

        }

        // Muassasa nomlarini region nomi va raqamiga qarab saralash
        uasort($shopproducts, function ($a, $b) {
            // Avval region nomiga qarab saralash
            if ($a['region_id'] !== $b['region_id']) {
                return strcmp($a['region_id'], $b['region_id']);
            }

            // Region nomi bir xil bo'lsa, muassasa nomidagi raqamga qarab saralash
            $a_number = preg_replace('/[^0-9]/', '', $a['name']);
            $b_number = preg_replace('/[^0-9]/', '', $b['name']);

            if ($a_number && $b_number) {
                return intval($a_number) - intval($b_number);
            }

            // Raqam topilmasa, to'liq nomga qarab saralash
            return strcmp($a['name'], $b['name']);
        });

        // dd($shopproducts);

        // Order yaratish uchun ma'lumotlarni tayyorlash
        $day = Day::orderBy('id', 'DESC')->first();
        $orderData = [
            'shopproducts' => $shopproducts,
            'shop' => $shop,
            'day' => $day
        ];

        return view('technolog.nextdelivershop', compact('shopproducts', 'shop', 'orderData'));
    }

    public function createShopOrder(Request $request)
    {
        try {
            $shop = Shop::where('id', $request->shop_id)->with('kindgarden.region')->with('product')->first();

            if (!$shop) {
                return redirect()->back()->with('error', 'Shop topilmadi');
            }

            $day = Day::orderBy('id', 'DESC')->first();

            // order_product yaratish
            $orderProduct = array();
            foreach ($shop->kindgarden as $row) {
                // check if order_product already exists
                $orderCheck = order_product::where('kingar_name_id', $row->id)->where('day_id', $day->id)->where('shop_id', $shop->id)->first();
                if (!$orderCheck) {
                    $orderProduct[$row->id] = order_product::create([
                        'kingar_name_id' => $row->id, // Shop uchun 0
                        'day_id' => $day->id,
                        'order_title' => date("d-m-Y H:i") . "Yetkazuvchi",
                        'document_processes_id' => 4, // Default qiymat
                        'data_of_weight' => json_encode(now()),
                        'to_menus' => json_encode([]), // Shop uchun 0
                        'shop_id' => $shop->id,
                    ]);
                }
            }

            // order_product_structure ga maxsulotlarni qo'shish
            $shopproducts = array();
            foreach ($shop->kindgarden as $row) {
                $shopproducts[$row->id]['name'] = $row->kingar_name;
                $shopproducts[$row->id]['region_id'] = $row->region_id;

                foreach ($shop->product as $prod) {
                    $weight = 0;
                    $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();

                    foreach ($nextday as $next) {
                        if ($row->id == $next->kingar_name_id) {
                            $workeat = titlemenu_food::where('day_id', $day->id)->get();
                            $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                                ->where('age_range_id', $next->king_age_name_id)
                                ->where('product_name_id', $prod->id)
                                ->get();

                            foreach ($prlar as $prw) {
                                $weight += $prw->weight * $next->kingar_children_number;
                                if ($next->king_age_name_id == 4) {
                                    $workeat = titlemenu_food::where('day_id', $day->id)
                                        ->where('food_id', $prw->menu_food_id)
                                        ->get();
                                    if ($workeat->count() > 0) {
                                        $weight += $prw->weight * $next->workers_count;
                                    }
                                }
                            }
                        }
                    }

                    $calculatedWeight = $weight / $prod->div;
                    $result = $calculatedWeight;
                    if ($prod->size_name_id == 3 or $prod->size_name_id == 2) {
                        $result = round($result);
                    }
                    else {
                        $result = round($result, 1);
                    }
                    // order_product_structure ga qo'shish
                    if (isset($orderProduct[$row->id])) {
                        order_product_structure::create([
                            'order_product_name_id' => $orderProduct[$row->id]->id,
                            'product_name_id' => $prod->id,
                            'product_weight' => $result,
                            'actual_weight' => $calculatedWeight, // Boshlang'ich qiymat
                        ]);
                    }

                    $shopproducts[$row->id][$prod->id] = $calculatedWeight;
                }
            }

            return response()->json([
                'success' => true,
                'message' => $shop->shop_name . ' uchun zayavka saqlandi!'
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function nextdayshoppdf(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('kindgarden.region')->with('product')->first();

        $shopproducts = array();
        $regions = []; // Regionlar ro'yxati

        foreach ($shop->kindgarden as $row) {
            $shopproducts[$row->id]['name'] = $row->kingar_name;
            $shopproducts[$row->id]['region_name'] = $row->region ? $row->region->region_name : '';
            $shopproducts[$row->id]['region_id'] = $row->region_id;

            // Regionni ro'yxatga qo'shish
            if (!in_array($row->region_id, $regions)) {
                $regions[] = $row->region_id;
            }

            $day = Day::orderBy('id', 'DESC')->first();
            foreach ($shop->product as $prod) {
                $shopproducts[$row->id][$prod->id] = "";
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $weight = 0;
                $itempr = "";

                $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();

                foreach ($nextday as $next) {
                    if ($row->id == $next->kingar_name_id) {
                        $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                            ->where('age_range_id', $next->king_age_name_id)
                            ->where('product_name_id', $prod->id)->get();

                        foreach ($prlar as $prw) {
                            $itempr = $itempr . "+" . $prw->weight . " * " . $next->kingar_children_number;
                            $weight += $prw->weight * $next->kingar_children_number;
                        }

                        // Xodimlar uchun ovqat gramajlarini hisoblash
                        $workerfood = titlemenu_food::where('day_id', $day->id)
                            ->where('worker_age_id', $next->king_age_name_id)
                            ->where('titlemenu_id', $next->kingar_menu_id)
                            ->get();

                        foreach ($workerfood as $tr) {
                            $workerprlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                                ->where('age_range_id', $next->king_age_name_id)
                                ->where('menu_food_id', $tr->food_id)
                                ->where('product_name_id', $prod->id)
                                ->get();

                            foreach ($workerprlar as $wpr) {
                                $weight += $wpr->weight * $next->workers_count;
                            }
                        }
                    }
                }

                $prdiv = Product::where('id', $prod->id)->first();
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div;
            }
        }

        // Muassasa nomlarini region nomi va raqamiga qarab saralash
        uasort($shopproducts, function ($a, $b) {
            if ($a['region_name'] !== $b['region_name']) {
                return strcmp($a['region_name'], $b['region_name']);
            }

            $a_number = preg_replace('/[^0-9]/', '', $a['name']);
            $b_number = preg_replace('/[^0-9]/', '', $b['name']);

            if ($a_number && $b_number) {
                return intval($a_number) - intval($b_number);
            }

            return strcmp($a['name'], $b['name']);
        });

        $day = Day::join('months', 'days.month_id', '=', 'months.id')->orderBy('days.id', 'DESC')->first();

        // Regionlar bo'yicha guruhlash
        $groupedByRegions = [];
        foreach ($shopproducts as $kindergartenId => $kindergartenData) {
            $regionId = $kindergartenData['region_id'];
            if (!isset($groupedByRegions[$regionId])) {
                $groupedByRegions[$regionId] = [
                    'region_name' => $kindergartenData['region_name'],
                    'kindergartens' => []
                ];
            }
            $groupedByRegions[$regionId]['kindergartens'][$kindergartenId] = $kindergartenData;
        }

        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('technolog.nextdayshoppdf', compact('groupedByRegions', 'shop', 'day')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    // ... existing code ...

    public function nextdayshopexcel(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('kindgarden.region')->with('product')->first();

        $shopproducts = array();
        $regions = []; // Regionlar ro'yxati

        foreach ($shop->kindgarden as $row) {
            $shopproducts[$row->id]['name'] = $row->kingar_name;
            $shopproducts[$row->id]['region_name'] = $row->region ? $row->region->region_name : '';
            $shopproducts[$row->id]['region_id'] = $row->region_id;

            // Regionni ro'yxatga qo'shish
            if (!in_array($row->region_id, $regions)) {
                $regions[] = $row->region_id;
            }

            $day = Day::orderBy('id', 'DESC')->first();
            foreach ($shop->product as $prod) {
                $shopproducts[$row->id][$prod->id] = "";
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $weight = 0;
                $itempr = "";

                $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();

                foreach ($nextday as $next) {
                    if ($row->id == $next->kingar_name_id) {
                        $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                            ->where('age_range_id', $next->king_age_name_id)
                            ->where('product_name_id', $prod->id)->get();

                        foreach ($prlar as $prw) {
                            $itempr = $itempr . "+" . $prw->weight . " * " . $next->kingar_children_number;
                            $weight += $prw->weight * $next->kingar_children_number;
                        }

                        // Xodimlar uchun ovqat gramajlarini hisoblash
                        $workerfood = titlemenu_food::where('day_id', $day->id)
                            ->where('worker_age_id', $next->king_age_name_id)
                            ->where('titlemenu_id', $next->kingar_menu_id)
                            ->get();

                        foreach ($workerfood as $tr) {
                            $workerprlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                                ->where('age_range_id', $next->king_age_name_id)
                                ->where('menu_food_id', $tr->food_id)
                                ->where('product_name_id', $prod->id)
                                ->get();

                            foreach ($workerprlar as $wpr) {
                                $weight += $wpr->weight * $next->workers_count;
                            }
                        }
                    }
                }

                $prdiv = Product::where('id', $prod->id)->first();
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div;
            }
        }

        // Muassasa nomlarini region nomi va raqamiga qarab saralash
        uasort($shopproducts, function ($a, $b) {
            if ($a['region_name'] !== $b['region_name']) {
                return strcmp($a['region_name'], $b['region_name']);
            }

            $a_number = preg_replace('/[^0-9]/', '', $a['name']);
            $b_number = preg_replace('/[^0-9]/', '', $b['name']);

            if ($a_number && $b_number) {
                return intval($a_number) - intval($b_number);
            }

            return strcmp($a['name'], $b['name']);
        });

        $day = Day::join('months', 'days.month_id', '=', 'months.id')->orderBy('days.id', 'DESC')->first();

        // Regionlar bo'yicha guruhlash
        $groupedByRegions = [];
        foreach ($shopproducts as $kindergartenId => $kindergartenData) {
            $regionId = $kindergartenData['region_id'];
            if (!isset($groupedByRegions[$regionId])) {
                $groupedByRegions[$regionId] = [
                    'region_name' => $kindergartenData['region_name'],
                    'kindergartens' => []
                ];
            }
            $groupedByRegions[$regionId]['kindergartens'][$kindergartenId] = $kindergartenData;
        }

        // Excel fayl yaratish (PhpSpreadsheet orqali)
        return \Excel::download(new class($groupedByRegions, $shop, $day) implements \Maatwebsite\Excel\Concerns\FromCollection,
        \Maatwebsite\Excel\Concerns\WithEvents,
        \Maatwebsite\Excel\Concerns\WithTitle {

            private $groupedByRegions;
            private $shop;
            private $day;

            public function __construct($groupedByRegions, $shop, $day)
            {
                $this->groupedByRegions = $groupedByRegions;
                $this->shop = $shop;
                $this->day = $day;
            }

            public function collection()
            {
                return collect([]);
            }

            public function title(): string
            {
                return 'Hisobot';
            }

            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                    $sheet = $event->sheet->getDelegate();
                    $currentRow = 1;

                    foreach ($this->groupedByRegions as $regionId => $regionData) {
                        // Region sarlavhasi
                        $sheet->setCellValue('A' . $currentRow, $regionData['region_name'] . ' ХУДУДИ');
                        $sheet->mergeCells('A' . $currentRow . ':' . $this->getColumnLetter(2 + count($this->shop->product)) . $currentRow);
                        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(14);
                        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $currentRow++;

                        // Shop nomi va sana
                        $sheet->setCellValue('A' . $currentRow, $this->shop->shop_name . '     sana: ' . $this->day->day_number . '-' . $this->day->month_name);
                        $sheet->mergeCells('A' . $currentRow . ':' . $this->getColumnLetter(2 + count($this->shop->product)) . $currentRow);
                        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);
                        $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $currentRow++;

                        // Bo'sh qator
                        $currentRow++;

                        // Jadval sarlavhasi
                        $headerRow = $currentRow;
                        $sheet->setCellValue('A' . $currentRow, 'ID');
                        $sheet->setCellValue('B' . $currentRow, 'MTT-номи');
                        $col = 'C';
                        foreach ($this->shop->product as $product) {
                            $sheet->setCellValue($col . $currentRow, $product->product_name);
                            $col++;
                        }

                        // Sarlavha stilini o'rnatish
                        $lastCol = $this->getColumnLetter(2 + count($this->shop->product));
                        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray([
                                'font' => ['bold' => true, 'size' => 11],
                                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['rgb' => '000000']
                                    ]
                                ],
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'E0E0E0']
                                ]
                            ]);
                        $currentRow++;

                        // Ma'lumotlar
                        $tr = 1;
                        $counts = [];
                        $dataStartRow = $currentRow;

                        foreach ($regionData['kindergartens'] as $kindergartenId => $kindergartenData) {
                            $sheet->setCellValue('A' . $currentRow, $tr++);
                            $sheet->setCellValue('B' . $currentRow, $kindergartenData['name']);

                            $col = 'C';
                            foreach ($this->shop->product as $product) {
                                if (!isset($counts[$product->id])) {
                                    $counts[$product->id] = 0;
                                }

                                $result = 0;
                                if (isset($kindergartenData[$product->id]) && $kindergartenData[$product->id] > 0) {
                                    $result = $kindergartenData[$product->id];
                                    if ($product->size_name_id == 3 || $product->size_name_id == 2) {
                                        $result = round($result);
                                    }
                                    else {
                                        $result = round($result, 1);
                                    }
                                }

                                $sheet->setCellValue($col . $currentRow, $result);
                                $counts[$product->id] += $result;
                                $col++;
                            }

                            // Qator stilini o'rnatish
                            $sheet->getStyle('A' . $currentRow . ':' . $lastCol . $currentRow)->applyFromArray([
                                    'borders' => [
                                        'allBorders' => [
                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                            'color' => ['rgb' => '000000']
                                        ]
                                    ],
                                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
                                ]);

                            // MTT nomini chapga tekislash
                            $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                            $currentRow++;
                        }

                        // Jami qator
                        $totalRow = $currentRow;
                        $sheet->setCellValue('A' . $currentRow, '');
                        $sheet->setCellValue('B' . $currentRow, $regionData['region_name'] . ' ХУДУДИ ЖАМИ:');

                        $col = 'C';
                        foreach ($this->shop->product as $product) {
                            $sheet->setCellValue($col . $currentRow, $counts[$product->id]);
                            $col++;
                        }

                        // Jami qator stilini o'rnatish
                        $sheet->getStyle('A' . $totalRow . ':' . $lastCol . $totalRow)->applyFromArray([
                                'font' => ['bold' => true, 'size' => 11],
                                'borders' => [
                                    'allBorders' => [
                                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                        'color' => ['rgb' => '000000']
                                    ]
                                ],
                                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F0F0F0']
                                ]
                            ]);

                        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                        $currentRow++; // Jami qatoridan keyingi qator

                        // Har bir region uchun sahifa uzilishi (oxirgi region emas bo'lsa)
                        $regionKeys = array_keys($this->groupedByRegions);
                        $lastRegionId = end($regionKeys);
                        if ($regionId !== $lastRegionId) {
                            $sheet->setBreak('A' . $currentRow, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                        }

                        $currentRow += 2; // Bo'sh qatorlar
                    }

                    // Ustunlar kengligini o'rnatish
                    $sheet->getColumnDimension('A')->setWidth(6);
                    $sheet->getColumnDimension('B')->setWidth(40); // MTT nomi uchun keng ustun

                    $col = 'C';
                    foreach ($this->shop->product as $product) {
                        $sheet->getColumnDimension($col)->setWidth(12);
                        $col++;
                    }

                    // Sahifa sozlamalari (A4 portrait - tikka)
                    $sheet->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                        ->setFitToWidth(1)
                        ->setFitToHeight(0);

                    // Chop etish uchun sozlamalar
                    $sheet->getPageMargins()
                        ->setTop(0.5)
                        ->setRight(0.5)
                        ->setLeft(0.5)
                        ->setBottom(0.5);
                }
                ];
            }

            private function getColumnLetter($columnNumber)
            {
                $letter = '';
                while ($columnNumber > 0) {
                    $temp = ($columnNumber - 1) % 26;
                    $letter = chr($temp + 65) . $letter;
                    $columnNumber = ($columnNumber - $temp - 1) / 26;
                }
                return $letter;
            }

        }, $shop->shop_name . '_' . $day->day_number . '-' . $day->month_name . '.xlsx');
    }

    public function updateBulkAgeMenu(Request $request)
    {
        $ageId = $request->age_id;
        $menuId = $request->menu_id;
        $nextday = Nextday_namber::where('king_age_name_id', $ageId)->get();
        foreach ($nextday as $row) {
            $row->kingar_menu_id = $menuId;
            $row->save();
        }
        return redirect()->back()->with('success', 'Barcha menyularni o\'zgartirildi!');
    }

    public function editnextallworkers(Request $request)
    {
        $day = Day::orderBy('id', 'DESC')->first();
        $lastday = Number_children::where('day_id', $day->id)->get();

        $nextday = Nextday_namber::all();
        foreach ($nextday as $row) {
            if ($lastday->where('kingar_name_id', $row->kingar_name_id)->where('king_age_name_id', $row->king_age_name_id)->first() != null) {
                $row->workers_count = $lastday->where('kingar_name_id', $row->kingar_name_id)->where('king_age_name_id', $row->king_age_name_id)->first()->workers_count;
                $row->save();
            }
        }
        return redirect()->back()->with('success', 'Xodimlar soni o\'zgartirildi!');
    }

    // PDF next day //////////////////////////////////////////////////////////
    public function nextdaymenuPDF(Request $request, $gid, $ageid)
    {
        $menu = Nextday_namber::where([
            ['kingar_name_id', '=', $gid],
            ['king_age_name_id', '=', $ageid]
        ])->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
            ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
        // dd($menu);  
        $products = Product::where('hide', 1)
            ->orderBy('sort', 'ASC')->get();

        $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
            ->where('age_range_id', $ageid)
            ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
            ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
            ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
            ->orderBy('menu_meal_time_id')
            ->get();

        // dd($menuitem);
        // xodimlar ovqati uchun
        $day = Day::join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number', 'days.id as id', 'months.month_name']);
        // dd($day);
        $workerfood = titlemenu_food::where('day_id', $day->id)
            ->where('worker_age_id', $ageid)
            ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
            ->get();
        // dd($workerfood);
        $nextdaymenuitem = [];
        $workerproducts = [];
        // kamchilik bor boshlangich qiymat berishda
        $productallcount = array_fill(1, 500, 0);
        foreach ($menuitem as $item) {
            $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
            $productallcount[$item->product_name_id] += $item->weight;
            for ($i = 0; $i < count($products); $i++) {
                if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                    $products[$i]['yes'] = 1;
                // array_push($yesproduct, $products[$i]);
                }
            }
        }
        // dd($productallcount);
        // Xodimlar uchun ovqat gramajlarini hisoblash
        $workerproducts = array_fill(1, 500, 0);
        foreach ($workerfood as $tr) {
            // Tushlikdagi birinchi ovqat va nondan yeyishadi
            if (isset($nextdaymenuitem[3][$tr->food_id])) {
                foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                    if ($key != 'foodname') {
                        $workerproducts[$key] += $value;
                    // Xodimlar gramajini ham productallcount ga qo'shish
                    // $productallcount[$key] += $value;
                    }
                }
            }
        }
        // dd($workerproducts);    

        // dd($workerfood);
        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('alltable', ['day' => $day, 'productallcount' => $productallcount, 'workerproducts' => $workerproducts, 'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // $customPaper = array(0,0,360,360);
        // $dompdf->setPaper($customPaper);

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
    // bog'chalar sozlanmalari

    public function settings(Request $request, $id)
    {
        $kgarden = Kindgarden::find($id);
        $age = Age_range::all();
        $region = Region::all();
        // dd($kgarden->age_range);
        return view('technolog.settings', ['garden' => $kgarden, 'ages' => $age, 'regions' => $region]);
    }

    public function ageranges(Request $request, $id)
    {
        $results = Kindgarden::where('id', $id)->with('age_range')->get();
        // dd($results[0]->age_range);
        $menus = Titlemenu::leftjoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
            ->get(['titlemenus.id', 'titlemenus.menu_name', 'seasons.season_name']);
        $html = [];
        foreach ($results[0]->age_range as $rows) {
            $option = "<select id='tommenu' class='form-control' name='menuids[" . $rows['id'] . "]' required>
            <option value=''>-----</option>";
            foreach ($menus as $menu) {
                $option = $option . "<option value=" . $menu->id . ">" . $menu->menu_name . " - " . $menu->season_name . "</option>";
            }
            $option = $option . "</select>";
            // $html = $html + "<input type='text' value='salom'>";
            array_push($html, "<div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' name='ages[" . $rows['id'] . "]' class='form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default' required>
            </div><span>Menusi</span>" . $option);
        }
        return $html;
    }

    public function gageranges(Request $request, $id)
    {
        $results = Kindgarden::where('id', $id)->with('age_range')->get();
        $html = [];
        foreach ($results[0]->age_range as $rows) {
            array_push($html, "<div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' data-id = '" . $rows['id'] . "' gar-id = '" . $results[0]->id . "' class='form-control ageranges' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default' required>
            </div>");
        }
        return $html;
    }


    public function addage(Request $request, $bogid, $ageid, $qiymati)
    {
        // $find = Temporary::where('kingar_name_id', $bogid)->first();
        // if ($find->age_id == $ageid) {
        //     $find->delete();
        //     return 0;
        // }

        Temporary::create([
            'kingar_name_id' => $bogid,
            'age_id' => $ageid,
            'age_number' => $qiymati
        ]);
    }

    public function nextdayaddgarden(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        $find = Nextday_namber::where('kingar_name_id', $request->kgarden)->get();
        if ($find->count() == 0) {
            foreach ($request->ages as $key => $value) {
                Nextday_namber::create([
                    'kingar_name_id' => $request->kgarden,
                    'king_age_name_id' => $key,
                    'kingar_children_number' => $value,
                    'workers_count' => $request->workers,
                    'kingar_menu_id' => $request->menuids[$key]
                ]);
            }
        }

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    public function updategarden(Request $request)
    {
        $kind = Kindgarden::find($request->kinname_id);
        $tags = $request->yongchek;
        $kind->age_range()->sync($tags);
        // dd($request->all());
        Kindgarden::where('id', $request->kinname_id)
            ->update([
            'kingar_name' => $request->kinname,
            'region_id' => $request->region,
            'kingar_password' => $request->kinparol,
            'worker_count' => $request->worker,
            'worker_age_id' => $request->worker_age_id,
            'hide' => $request->hide,
        ]);
        return redirect()->route('technolog.home');
    }

    public function getage(Request $request, $bogid)
    {
        $results = Kindgarden::where('id', $bogid)->with('age_range')->get();
        // dd($results[0]->age_range);
        $htmls = [];
        array_push($htmls, "<h3>" . $results[0]['kingar_name'] . "</h3> <input type='hidden' name='kingarediteid' value=" . $results[0]['id'] . " >");
        foreach ($results[0]->age_range as $rows) {
            $edite = Temporary::where('kingar_name_id', $bogid)->where('age_id', $rows['id'])->first();
            if (empty($edite['age_number'])) {

                $edite['age_number'] = 0;
            }
            // $html = $html + "<input type='text' value='salom'>";
            array_push($htmls, "  <div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' required name='ages[]' value=" . $edite['age_number'] . " data-id=" . $rows['id'] . "  class='age_ranges form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default'>
            <input type='hidden' required name='agesid[]' value=" . $rows['id'] . ">
            </div>");
        }
        return $htmls;
    }

    public function editage(Request $request)
    {
        // dd($request->all());
        $ages = $request->ages;
        $agesid = $request->agesid;
        for ($i = 0; $i < count($ages); $i++) {
            $find = Temporary::where('kingar_name_id', $request->kingarediteid)->where('age_id', $agesid[$i])->get();

            if (empty($find[0])) {
                Temporary::create([
                    'kingar_name_id' => $request->kingarediteid,
                    'age_id' => $agesid[$i],
                    'age_number' => $ages[$i]
                ]);
            }
            else {
                Temporary::where('kingar_name_id', $request->kingarediteid)->where('age_id', $agesid[$i])->update([
                    'age_id' => $agesid[$i],
                    'age_number' => $ages[$i]
                ]);
            }
        }

        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    public function activagecountedit(Request $request)
    {
        Number_children::where('day_id', $request->dayid)->where('kingar_name_id', $request->kinid)->where('king_age_name_id', $request->ageid)
            ->update(['kingar_children_number' => $request->agecount]);

        return redirect()->route('technolog.showdate', ["year" => $request->yearid, "month" => $request->monthid, "day" => $request->dayid]);
    }

    // mayda skladlarga product buyurtma berish

    public function addproduct()
    {
        $months = Month::all();
        $days = Day::orderby('id', 'DESC')->get();
        $orederproduct = order_product::join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            // ->where('day_id', $days[1]->id)
            ->select('order_products.id', 'days.day_number', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name')
            ->orderby('order_products.id', 'DESC')
            ->get();
        $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->get();
        // dd($orederproduct);
        $kingar = Kindgarden::all();

        foreach ($orederproduct as $item) {
            $t = 0;
            foreach ($kingar as $ki) {
                if ($item->kingar_name == $ki->kingar_name) {
                // $kingar[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.addproduct', ['gardens' => $kingar, 'orders' => $orederproduct, 'products' => $orederitems, 'months' => $months]);
    }

    public function ordername(Request $request)
    {
        $days = Day::orderby('id', 'DESC')->get();
        $orderproduct = order_product::create([
            'kingar_name_id' => $request->mtmname,
            'day_id' => $days[1]->id,
            'order_title' => $request->title,
            'document_processes_id' => 1,
        ]);

        history_process::create([
            'order_product_id' => $orderproduct->id,
            'user_name_id' => Auth::user()->id,
            'order_title' => $request->title,
            'document_process_id' => 1,
            'action' => 1
        ]);

        return redirect()->route('technolog.addproduct');
    }

    public function orderitem(Request $request, $id)
    {
        $orederproduct = order_product::where('order_products.id', $id)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            ->first();
        $days = Day::orderby('id', 'DESC')->get();
        // agar yangi kun ochilsa hujjat oxiriga yetmagan hisoblanadi
        // if (empty($orederproduct->day_number) or $days[1]->day_number != $orederproduct->day_number or $days[1]->month_id != $orederproduct->month_id) {
        //     return redirect()->route('technolog.addproduct');
        // }
        // shu joyida hide ishlatishimiz kerak majbur
        $newsproduct = Product::orderby('sort', 'ASC')->get();
        $items = order_product_structure::where('order_product_name_id', $id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'order_product_structures.data_of_weight', 'products.product_name')
            ->get();
        foreach ($items as $item) {
            $t = 0;
            foreach ($newsproduct as $pro) {
                if ($item->product_name == $pro->product_name) {
                    $newsproduct[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        // dd($items);
        return view('technolog.orderitem', ['orderid' => $id, 'productall' => $newsproduct, 'items' => $items, 'ordername' => $orederproduct]);
    }
    public function plusproduct(Request $request)
    {
        // dd($request->all());
        foreach ($request->orders as $key => $value) {
            if ($value != null) {
                // Maxsulot ma'lumotlarini olish
                $product = Product::find($key);
                $order = order_product::find($request->titleid);

                // data_of_weight uchun asosiy ma'lumotlarni to'plash
                $dataOfWeight = [
                    'product_id' => $key,
                    'product_name' => $product ? $product->product_name : 'Noma\'lum maxsulot',
                    'total_weight' => $value,
                    'order_id' => $request->titleid,
                    'order_title' => $order ? $order->order_title : '',
                    'added_manually' => true,
                    'added_at' => now()->toISOString(),
                    'summary' => [
                        'manual_addition' => true,
                        'weight' => $value
                    ]
                ];

                order_product_structure::create([
                    'order_product_name_id' => $request->titleid,
                    'product_name_id' => $key,
                    'product_weight' => $value,
                    'data_of_weight' => json_encode($dataOfWeight, JSON_UNESCAPED_UNICODE)
                ]);
            }
        }
        return redirect()->route('technolog.orderitem', $request->titleid);
    }
    // parolni tasdiqlash
    public function controlpassword(Request $request)
    {
        $password = Auth::user()->password;
        if (Hash::check($request->password, $password)) {
            $result = 1;
            order_product::where('id', $request->orderid)->update([
                'document_processes_id' => 2
            ]);
        }
        else {
            $result = 0;
        }
        return $result;
    }
    // botga start bosganlarni tashkilotiga bog'lash
    public function getbotusers(Request $request)
    {
        $users = Person::with('shop')->with('garden')->orderby('id', 'DESC')->get();
        $gardens = Kindgarden::all();
        $shops = Shop::all();
        // dd($users);
        return view('technolog.botusers', compact('users', 'gardens', 'shops'));
    }
    // keraksiz foydalanuvchini o'chirish
    public function deletepeople(Request $request)
    {
        Person::where('id', $request->personid)->delete();
        return redirect()->route('technolog.getbotusers');
    }

    public function deletetitlemenuid(Request $request)
    {
        // dd($request->all());
        Titlemenu::where('id', $request->menuid)->delete();
        return redirect()->route('technolog.seasons');
    }
    // orderproduct malulotlarini olish 
    public function getproduct(Request $request)
    {

        $number = order_product_structure::where('order_product_structures.id', $request->id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name')
            ->first();

        $htmlproduct = "<div class='input-group mb-3'>
            <span class='input-group-text' id='basic-addon2'>" . $number['product_name'] . " </span>
            <input  type='text' data-producy=" . $number['id'] . " value=" . $number['product_weight'] . " required class='form-control  product_order'  placeholder='raqam kiriting'></div>";

        return $htmlproduct;
    }

    // orderproduct malumotlarni tahrirlash

    public function editproduct(Request $request)
    {
        order_product_structure::where('id', $request->producid)->update(
        ['product_weight' => $request->orderinpval]
        );
    }

    //  orderproduct malumotlarini o'chirish 

    public function deleteid(Request $request)
    {
        order_product_structure::where('id', $request->id)->delete();
    }

    public function bindgarden(Request $request)
    {
        $per = Person::where('id', $request['personid'])->first();
        $rr = Kindgarden::where('id', $request['mname'])
            ->update([
            'telegram_user_id' => $per->telegram_id
        ]);
        Person::where('id', $request['personid'])
            ->update([
            'kingar_id' => $request['mname'],
            'shop_id' => -1
        ]);
        return redirect()->route('technolog.getbotusers');
    }

    public function bindshop(Request $request)
    {
        $per = Person::where('id', $request['personid'])->first();
        Shop::where('id', $request['shname'])
            ->update([
            'telegram_id' => $per->telegram_id
        ]);
        Person::where('id', $request['personid'])
            ->update([
            'kingar_id' => 0,
            'shop_id' => $request['shname']
        ]);
        return redirect()->route('technolog.getbotusers');
    }

    // Menu saqlash

    public function todaynextdaymenu(Request $request)
    {
        // dd($request->all());
        $mid = $request->mid;
        $dmf = $request->dmf;
        $menuages = [];
        foreach ($mid as $mi) {
            $param = explode("_", $mi);
            $menuages[$param[0]] = $param[1];
        }
        // dd($menuages);
        $days = Day::orderBy('id', 'DESC')->first();
        $chil_number = Temporary::all();
        // dd($chil_number);
        foreach ($chil_number as $child) {
            $workers = Kindgarden::where('id', $child->kingar_name_id)->first();
            // dd($workers['worker_count']);
            $menusi = $request['manuone'];
            // dd($request->all());
            if ($child->age_id == 3) {
                $menusi = $request['two'];
            }
            Nextday_namber::create([
                'kingar_name_id' => $child->kingar_name_id,
                'king_age_name_id' => $child->age_id,
                'kingar_children_number' => $child->age_number,
                'workers_count' => $child->workers ?? 0,
                'kingar_menu_id' => $menuages[$child->age_id],
            ]);

        }

        foreach ($dmf as $dm) {
            $param = explode("_", $dm);
            titlemenu_food::create([
                'day_id' => $days->id,
                'worker_age_id' => $param[0],
                'titlemenu_id' => $param[1],
                'food_id' => $param[2]
            ]);
        }

        $temp = Temporary::truncate();
        $gr = Kindgarden::all();

        return redirect()->route('technolog.home');
    }

    public function allproducts(Request $request)
    {
        $products = Product::leftjoin('product_categories', 'product_categories.id', '=', 'products.category_name_id')
            ->leftjoin('norm_categories', 'norm_categories.id', '=', 'products.norm_cat_id')
            ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->with('shop')
            ->get([
            'products.*',
            'product_categories.pro_cat_name',
            'product_categories.id as pro_cat_id',
            'norm_categories.norm_name',
            'sizes.size_name'
        ]);
        // dd($products);
        return view('technolog.allproducts', compact('products'));
    }

    public function settingsproduct(Request $request, $id)
    {
        $product = Product::where('id', $id)->first();
        $categories = Product_category::all();
        $norms = Norm_category::all();
        $sizes = Size::all();
        // dd($product);
        return view('technolog.settingsproduct', compact('norms', 'product', 'categories', 'sizes'));
    }

    public function updateproduct(Request $request)
    {
        // dd($request->all());
        Product::where('id', $request['productid'])
            ->update([
            'product_name' => $request['product_name'],
            'size_name_id' => $request['sizeid'],
            'category_name_id' => $request['catid'],
            'norm_cat_id' => $request['normid'],
            'div' => $request['div'],
            'package_size' => $request['package_size'],
            'sort' => $request['sort'],
            'hide' => $request['hide'],
            'proteins' => $request['proteins'] ?? 0,
            'fats' => $request['fats'] ?? 0,
            'carbohydrates' => $request['carbohydrates'] ?? 0,
            'kcal' => $request['kcal'] ?? 0
        ]);
        return redirect()->route('technolog.settingsproduct', $request['productid'])->with('status', "Malumotlar saqlandi!");
    }

    public function shops(Request $request)
    {
        // Faol do'konlar (hide = 1) - oxirgi kiritilganlar birinchi
        $activeShops = Shop::where('hide', 1)->orderBy('id', 'desc')->get();

        // Nofaol do'konlar (hide = 0) - ID bo'yicha DESC tartibda
        $inactiveShops = Shop::where('hide', 0)->orderBy('id', 'desc')->get();

        // Faol do'konlarni birinchi, nofaollarni oxiriga qo'yamiz
        $shops = $activeShops->merge($inactiveShops);

        return view('technolog.shops', compact('shops'));
    }

    public function shopsettings(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('product')->with('kindgarden')->first();
        $products = Product::all();
        $gardens = Kindgarden::all();
        $types = typeofwork::all();
        return view('technolog.shopsettings', compact('types', 'shop', 'products', 'gardens'));
    }

    public function updateshop(Request $request)
    {
        $shop = Shop::find($request->shopid);
        $prd = $request->products;
        $grd = $request->gardens;
        if ($request->type == 2) {
            $prd = [];
            $grd = [];
        }
        $shop->product()->sync($prd);
        $shop->kindgarden()->sync($grd);

        $shop->update([
            'shop_name' => $request->shopname,
            'bossname' => $request->bossname,
            'phone' => $request->phone,
            'type_id' => $request->type,
            'hide' => $request->hide
        ]);
        return redirect()->route('technolog.shops');
    }

    public function addshop()
    {
        $products = Product::all();
        $gardens = Kindgarden::all();
        $types = typeofwork::all();

        return view('technolog.addshop', compact('products', 'gardens', 'types'));
    }

    public function createshop(Request $request)
    {
        $shop = Shop::create([
            'shop_name' => $request->name,
            'bossname' => $request->bossname,
            'phone' => $request->phone,
            'type_id' => $request->type,
            'telegram_id' => 0,
            'hide' => 1
        ]);

        if ($request->type == 1) {
            $prd = $request->products;
            $shop->product()->sync($prd);
            $grd = $request->gardens;
            $shop->kindgarden()->sync($grd);
        }

        return redirect()->route('technolog.shops');
    }

    public function food(Request $request)
    {
        $food = Food::all();
        return view('technolog.food', compact('food'));
    }

    public function foodsettings(Request $request, $id)
    {
        $food = Food::where('id', $id)->first();
        $categories = Food_category::all();
        $times = Meal_time::all();
        return view('technolog.foodsettings', compact('food', 'categories', 'times'));
    }

    public function updatefood(Request $request)
    {
        Food::where('id', $request->foodid)
            ->update([
            'food_cat_id' => $request->catid,
            'meal_time_id' => $request->timeid,
            'food_weight' => $request->weight
        ]);

        return redirect()->route('food');
    }

    public function fooditem(Request $request, $id)
    {
        $productall = Product::all();
        $food = Food_composition::where('food_name_id', $id)->join('food', 'food.id', '=', 'food_compositions.food_name_id')
            ->join('products', 'products.id', '=', 'food_compositions.product_name_id')
            ->get(['food_compositions.id', 'products.id as productid', 'food.food_name', 'products.product_name', 'food_compositions.gram', 'food_compositions.weight_without_waste', 'food_compositions.proteins', 'food_compositions.fats', 'food_compositions.carbohydrates', 'food_compositions.kcal']);
        // dd($food);
        foreach ($food as $item) {
            $t = 0;
            foreach ($productall as $pro) {
                if ($item->product_name == $pro->product_name) {
                    $productall[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.fooditem', compact('food', 'productall', 'id'));
    }

    public function addproductfood(Request $request)
    {
        $createData = [
            'food_name_id' => $request->titleid,
            'product_name_id' => $request->productid,
            'gram' => $request->gram ?? 0
        ];

        // Yangi ustunlarni qo'shamiz (agar mavjud bo'lsa)
        if (!empty($request->weight_without_waste)) {
            $createData['weight_without_waste'] = $request->weight_without_waste;
        }
        if (!empty($request->proteins)) {
            $createData['proteins'] = $request->proteins;
        }
        if (!empty($request->fats)) {
            $createData['fats'] = $request->fats;
        }
        if (!empty($request->carbohydrates)) {
            $createData['carbohydrates'] = $request->carbohydrates;
        }
        if (!empty($request->kcal)) {
            $createData['kcal'] = $request->kcal;
        }

        Food_composition::create($createData);
        return redirect()->route('fooditem', $request->titleid);
    }

    public function deleteproductfood(Request $request)
    {
        Food_composition::where('id', $request->id)->delete();
    }

    public function editproductfood(Request $request)
    {
        $updateData = [
            'product_name_id' => $request->productid,
            'gram' => $request->gram ?? 0
        ];

        // Yangi ustunlarni qo'shamiz (agar mavjud bo'lsa)
        if (isset($request->weight_without_waste)) {
            $updateData['weight_without_waste'] = $request->weight_without_waste;
        }
        if (isset($request->proteins)) {
            $updateData['proteins'] = $request->proteins;
        }
        if (isset($request->fats)) {
            $updateData['fats'] = $request->fats;
        }
        if (isset($request->carbohydrates)) {
            $updateData['carbohydrates'] = $request->carbohydrates;
        }
        if (isset($request->kcal)) {
            $updateData['kcal'] = $request->kcal;
        }

        Food_composition::where('id', $request->id)->update($updateData);

        return redirect()->route('fooditem', $request->titleid);
    }

    public function addfood(Request $request)
    {
        $categories = Food_category::all();
        $times = Meal_time::all();
        return view('technolog.addfood', compact('categories', 'times'));
    }

    public function createfood(Request $request)
    {
        Food::create([
            'food_name' => $request->name,
            'food_cat_id' => $request->catid,
            'meal_time_id' => $request->timeid,
            'food_prepar_tech' => '...',
            'food_image' => 'png.png',
            'food_weight' => $request->weight
        ]);

        return redirect()->route('food');
    }

    public function seasons(Request $request)
    {
        $seasons = Season::all();
        return view('technolog.seasons', compact('seasons'));
    }

    public function menus(Request $request, $id)
    {
        // Faqat parent menyularni olish (parent_id = null)
        $menus = Titlemenu::with('children')
            ->leftjoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
            ->where('titlemenus.menu_season_id', $id)
            ->whereNull('titlemenus.parent_id')
            ->orderBy('titlemenus.order_number', 'ASC')
            ->orderBy('titlemenus.id', 'DESC')
            ->get(['titlemenus.id', 'titlemenus.menu_name', 'titlemenus.parent_id', 'seasons.season_name']);

        $works = Nextday_namber::all();
        for ($i = 0; $i < count($menus); $i++) {
            $menus[$i]['us'] = 0;
            foreach ($works as $row) {
                if ($row->kingar_menu_id == $menus[$i]['id']) {
                    $menus[$i]['us'] = 1;
                }
            }

            // Child menyular uchun ham 'us' flagni qo'shish
            foreach ($menus[$i]->children as $child) {
                $child['us'] = 0;
                foreach ($works as $row) {
                    if ($row->kingar_menu_id == $child->id) {
                        $child['us'] = 1;
                    }
                }
            }
        }
        return view('technolog.menus', compact('menus', 'id', 'works'));
    }

    public function addtitlemenu(Request $request, $id)
    {
        $ages = Age_range::all();

        // Barcha seasonlar va ularning ierarxik menyularini olish
        $seasons = Season::with(['titlemenus' => function ($query) {
            $query->whereNull('parent_id')
                ->with('children')
                ->orderBy('menu_name', 'ASC');
        }])->orderBy('season_name', 'ASC')->get();

        return view('technolog.addtitlemenu', compact('id', 'ages', 'seasons'));
    }

    public function createmenu(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::create([
            'menu_name' => $request->name,
            'menu_season_id' => $request->seasonid,
            'parent_id' => $request->parent_id ?? null
        ]);

        $age = $request->yongchek;
        $menu->age_range()->sync($age);

        return redirect()->route('technolog.menus', $request->seasonid);
    }

    public function menuitem(Request $request, $id)
    {
        $times = Meal_time::all();
        $titlemenu = Titlemenu::where('titlemenus.id', $id)->with('age_range')
            ->leftJoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
            ->first(['titlemenus.id', 'titlemenus.menu_name', 'titlemenus.menu_season_id', 'titlemenus.short_name', 'titlemenus.order_number', 'seasons.season_name']);

        $menuitem = Menu_composition::where('title_menu_id', $id)
            ->leftJoin('titlemenus', 'titlemenus.id', '=', 'menu_compositions.title_menu_id')
            ->leftJoin('meal_times', 'meal_times.id', '=', 'menu_compositions.menu_meal_time_id')
            ->leftJoin('food', 'food.id', '=', 'menu_compositions.menu_food_id')
            ->leftJoin('products', 'products.id', '=', 'menu_compositions.product_name_id')
            ->leftJoin('age_ranges', 'age_ranges.id', '=', 'menu_compositions.age_range_id')
            ->orderby('menu_compositions.menu_meal_time_id', 'ASC')
            ->orderby('menu_compositions.id', 'ASC')
            ->get([
            'titlemenus.menu_name',
            'titlemenus.menu_season_id',
            'titlemenus.id as menuid',
            'meal_times.meal_time_name',
            'meal_times.id as meal_timeid',
            'food.food_name',
            'food.id as foodid',
            'products.product_name',
            'products.id as productid',
            'age_ranges.id as ageid',
            'menu_compositions.weight',
            'menu_compositions.id',
            'menu_compositions.waste_free',
            'menu_compositions.proteins',
            'menu_compositions.fats',
            'menu_compositions.carbohydrates',
            'menu_compositions.kcal'
        ]);

        // Maxsulotlar bo'yicha jami gramlarni hisoblash
        $productTotals = [];
        foreach ($menuitem as $item) {
            $productId = $item->productid;
            $weight = $item->weight;

            if (isset($productTotals[$productId])) {
                $productTotals[$productId]['total_weight'] += $weight;
            }
            else {
                $productTotals[$productId] = [
                    'product_name' => $item->product_name,
                    'total_weight' => $weight
                ];
            }
        }

        // dd($productTotals);
        return view('technolog.menuitem', compact('id', 'times', 'titlemenu', 'menuitem', 'productTotals'));
    }

    public function updateTitlemenu(Request $request)
    {
        $request->validate([
            'menu_name' => 'required|string|max:255',
            'menu_season_id' => 'required|integer|exists:seasons,id',
            'description' => 'nullable|string',
            'short_name' => 'nullable|string|max:255',
            'order_number' => 'nullable|integer',
            'parent_id' => 'nullable|integer|exists:titlemenus,id'
        ]);

        $titlemenu = Titlemenu::findOrFail($request->id);
        $titlemenu->update([
            'menu_name' => $request->menu_name,
            'short_name' => $request->short_name,
            'order_number' => $request->order_number,
            'menu_season_id' => $request->menu_season_id,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->route('technolog.menuitem', $titlemenu->id)
            ->with('success', 'Menyu muvaffaqiyatli yangilandi!');
    }

    public function updateMenuAgeRange(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer|exists:titlemenus,id',
            'age_range_id' => 'required|integer|exists:age_ranges,id'
        ]);

        $menuId = $request->menu_id;
        $newAgeRangeId = $request->age_range_id;

        try {
            DB::beginTransaction();

            // 1. Titlemenu_age_range jadvalini yangilash
            $titlemenu = Titlemenu::findOrFail($menuId);
            $titlemenu->age_range()->sync([$newAgeRangeId]);

            // 2. Menu_compositions jadvalidagi barcha yozuvlarning age_range_id ni yangilash
            Menu_composition::where('title_menu_id', $menuId)
                ->update(['age_range_id' => $newAgeRangeId]);

            DB::commit();

            return redirect()->route('technolog.menuitem', $menuId)
                ->with('success', 'Yosh toifasi muvaffaqiyatli yangilandi!');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('technolog.menuitem', $menuId)
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    //  copy
    public function menuitemshow(Request $request, $id)
    {
        $times = Meal_time::all();
        $titlemenu = Titlemenu::where('titlemenus.id', $id)->with('age_range')
            ->leftJoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
            ->first(['titlemenus.id', 'titlemenus.menu_name', 'titlemenus.menu_season_id', 'titlemenus.short_name', 'titlemenus.order_number', 'seasons.season_name']);

        $menuitem = Menu_composition::where('title_menu_id', $id)
            ->leftJoin('titlemenus', 'titlemenus.id', '=', 'menu_compositions.title_menu_id')
            ->leftJoin('meal_times', 'meal_times.id', '=', 'menu_compositions.menu_meal_time_id')
            ->leftJoin('food', 'food.id', '=', 'menu_compositions.menu_food_id')
            ->leftJoin('products', 'products.id', '=', 'menu_compositions.product_name_id')
            ->leftJoin('age_ranges', 'age_ranges.id', '=', 'menu_compositions.age_range_id')
            ->orderby('menu_compositions.menu_meal_time_id', 'ASC')
            ->orderby('menu_compositions.id', 'ASC')
            ->get([
            'titlemenus.menu_name',
            'titlemenus.menu_season_id',
            'titlemenus.id as menuid',
            'meal_times.meal_time_name',
            'meal_times.id as meal_timeid',
            'food.food_name',
            'food.id as foodid',
            'products.product_name',
            'products.id as productid',
            'age_ranges.id as ageid',
            'menu_compositions.weight',
            'menu_compositions.id',
            'menu_compositions.waste_free',
            'menu_compositions.proteins',
            'menu_compositions.fats',
            'menu_compositions.carbohydrates',
            'menu_compositions.kcal'
        ]);

        // Maxsulotlar bo'yicha jami gramlarni hisoblash
        $productTotals = [];
        foreach ($menuitem as $item) {
            $productId = $item->productid;
            $weight = $item->weight;

            if (isset($productTotals[$productId])) {
                $productTotals[$productId]['total_weight'] += $weight;
            }
            else {
                $productTotals[$productId] = [
                    'product_name' => $item->product_name,
                    'total_weight' => $weight
                ];
            }
        }

        // dd($productTotals);
        return view('technolog.menuitemshow', compact('id', 'times', 'titlemenu', 'menuitem', 'productTotals'));
    }
    public function copymenuitem(Request $request)
    {
        $titlemenu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $ages = array();
        $loop = 0;
        foreach ($titlemenu->age_range as $age) {
            $ages[$loop++] = $age->id;
        }

        // Nusxa child sifatida yaratiladi
        $newtitlemenu = Titlemenu::create([
            'menu_name' => $request->newmenuname,
            'menu_season_id' => $request->seasonid,
            'parent_id' => $request->menuid // Original menyu parent bo'ladi
        ]);

        $newtitlemenu->age_range()->sync($ages);

        $menu = Menu_composition::where('title_menu_id', $request->menuid)->get();

        foreach ($menu as $row) {
            Menu_composition::create([
                "title_menu_id" => $newtitlemenu->id,
                "menu_meal_time_id" => $row->menu_meal_time_id,
                "menu_food_id" => $row->menu_food_id,
                "product_name_id" => $row->product_name_id,
                "age_range_id" => $row->age_range_id,
                "weight" => $row->weight
            ]);
        }

        return redirect()->route('technolog.menuitem', $newtitlemenu->id);
    }


    public function getfood(Request $request)
    {
        $food = Food::where('meal_time_id', $request->id)
            ->orwhere('meal_time_id', 0)
            ->get();

        $html = "<select id='foodid' name='foodid' onchange='change()' class='form-select' required aria-label='Default select example'>
                        <option value=''>--Taomni tanlang--</option>";
        foreach ($food as $row) {
            $html = $html . "<option value=" . $row->id . ">" . $row->food_name . "</option>";
        }
        $html = $html . "</select>";
        return $html;
    }

    public function getfoodcomposition(Request $request)
    {
        $menu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $foodcom = Food_composition::where('food_name_id', $request->id)
            ->join('products', 'products.id', '=', 'food_compositions.product_name_id')
            ->get(['food_compositions.id', 'products.id as productid', 'products.product_name', 'food_compositions.gram', 'food_compositions.weight_without_waste', 'food_compositions.proteins', 'food_compositions.fats', 'food_compositions.carbohydrates', 'food_compositions.kcal']);
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach ($menu->age_range as $row) {
            $html = $html . "<th scope='col'>" . $row['age_name'] . "</th>";
        }
        $html = $html . "
                        <th scope='col'>Chiqindisiz (gr)</th>
                        <th scope='col'>Oqsillar (gr)</th>
                        <th scope='col'>Yog'lar (gr)</th>
                        <th scope='col'>Uglevodlar (gr)</th>
                        <th scope='col'>Kaloriya</th></tr>
                </thead>
                <tbody>";

        foreach ($foodcom as $product) {
            $html = $html . "<tr>
                <td><input type='hidden' name='products[]' value='" . $product->productid . "'></td>
                <td>" . $product->product_name . "</td>";
            foreach ($menu->age_range as $row) {
                $html = $html . "<td><input type='text' name='ages[]' value='" . $product->gram . "' required style='width: 100%;'></td>";
            }
            $html = $html . "
                <td><input type='text' name='waste_free" . $product->productid . "' value='" . $product->weight_without_waste . "' placeholder='0' style='width: 100%;'></td>
                <td><input type='text' name='proteins" . $product->productid . "' value='" . $product->proteins . "' placeholder='0' style='width: 100%;'></td>
                <td><input type='text' name='fats" . $product->productid . "' value='" . $product->fats . "' placeholder='0' style='width: 100%;'></td>
                <td><input type='text' name='carbohydrates" . $product->productid . "' value='" . $product->carbohydrates . "' placeholder='0' style='width: 100%;'></td>
                <td><input type='text' name='kcal" . $product->productid . "' value='" . $product->kcal . "' placeholder='0' style='width: 100%;'></td>";

            $html = $html . "</tr>";
        }
        $html = $html . "</tbody>
            </table>";

        return $html;
    }

    public function createmenucomposition(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::where('id', $request->titleid)->with('age_range')->first();
        $t = 0;
        foreach ($request->products as $product) {
            $waste_free_field = "waste_free" . $product;
            $proteins_field = "proteins" . $product;
            $fats_field = "fats" . $product;
            $carbohydrates_field = "carbohydrates" . $product;
            $kcal_field = "kcal" . $product;

            foreach ($menu->age_range as $age) {
                // echo "menu: ".$request->titleid." mealtime: ".$request->timeid." food: ".$request->foodid." product: ".$product." age: ".$age->id." weight: ".$request[$ages][$t++]." <br/>";
                $createData = [
                    'title_menu_id' => $request->titleid,
                    'menu_meal_time_id' => $request->timeid,
                    'menu_food_id' => $request->foodid,
                    'product_name_id' => $product,
                    'age_range_id' => $age->id,
                    'weight' => $request["ages"][$t++]
                ];

                // Yangi ustunlarni qo'shamiz (agar mavjud bo'lsa)
                if (isset($request->$waste_free_field) && !empty($request->$waste_free_field)) {
                    $createData['waste_free'] = $request->$waste_free_field;
                }
                if (isset($request->$proteins_field) && !empty($request->$proteins_field)) {
                    $createData['proteins'] = $request->$proteins_field;
                }
                if (isset($request->$fats_field) && !empty($request->$fats_field)) {
                    $createData['fats'] = $request->$fats_field;
                }
                if (isset($request->$carbohydrates_field) && !empty($request->$carbohydrates_field)) {
                    $createData['carbohydrates'] = $request->$carbohydrates_field;
                }
                if (isset($request->$kcal_field) && !empty($request->$kcal_field)) {
                    $createData['kcal'] = $request->$kcal_field;
                }

                Menu_composition::create($createData);
            }

        }

        return redirect()->route('technolog.menuitem', $request->titleid);
    }

    public function getmenuproduct(Request $request)
    {
        $menu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $foodcom = Menu_composition::where('title_menu_id', $request->menuid)
            ->where('menu_meal_time_id', $request->timeid)
            ->where('menu_food_id', $request->foodid)
            ->where('product_name_id', $request->prodid)
            ->join('products', 'products.id', '=', 'menu_compositions.product_name_id')
            ->get(['menu_compositions.id', 'products.product_name', 'age_range_id', 'menu_compositions.weight', 'menu_compositions.waste_free', 'menu_compositions.proteins', 'menu_compositions.fats', 'menu_compositions.carbohydrates', 'menu_compositions.kcal']);
        // dd($foodcom);
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach ($menu->age_range as $row) {
            $html = $html . "<th scope='col'>" . $row['age_name'] . "</th>";
        }
        $html = $html . "<th scope='col'>Chiqindisiz (gr)</th>
                        <th scope='col'>Oqsillar (gr)</th>
                        <th scope='col'>Yog'lar (gr)</th>
                        <th scope='col'>Uglevodlar (gr)</th>
                        <th scope='col'>Kaloriya</th>";

        $html = $html . "</tr>
                </thead>
                <tbody>";
        for ($it = 0; $it < count($foodcom); $it++) {
            $html = $html . "<tr>
                <td></td>
                <td>" . $foodcom[$it]['product_name'] . "</td>";
            foreach ($menu->age_range as $row) {
                $html = $html . "<td><input type='text' name='ages[]' value='" . $foodcom[$it]['weight'] . "' required style='width: 100%;'></td>";
                $html = $html . "<input type='hidden' name='rows[]' value='" . $foodcom[$it]['id'] . "'>";
                $html = $html . "<td><input type='text' name='waste_free[]' value='" . ($foodcom[$it]['waste_free'] ?? '') . "' style='width: 100%;'></td>
                    <td><input type='text' name='proteins[]' value='" . ($foodcom[$it]['proteins'] ?? '') . "' style='width: 100%;'></td>
                    <td><input type='text' name='fats[]' value='" . ($foodcom[$it]['fats'] ?? '') . "' style='width: 100%;'></td>
                    <td><input type='text' name='carbohydrates[]' value='" . ($foodcom[$it]['carbohydrates'] ?? '') . "' style='width: 100%;'></td>
                    <td><input type='text' name='kcal[]' value='" . ($foodcom[$it]['kcal'] ?? '') . "' style='width: 100%;'></td>";
                $it++;
            }

            $html = $html . "</tr>";
        }
        $html = $html . "</tbody>
            </table>";

        return $html;

    }

    public function editemenuproduct(Request $request)
    {
        // dd($request->all());
        $it = 0;
        $nutrition_it = 0; // Yangi ustunlar uchun alohida iterator

        foreach ($request->rows as $row) {
            $updateData = [
                'weight' => $request->ages[$it]
            ];

            // Yangi ustunlarni qo'shamiz (agar mavjud bo'lsa)
            if (isset($request->waste_free) && isset($request->waste_free[$nutrition_it])) {
                $updateData['waste_free'] = $request->waste_free[$nutrition_it];
            }
            if (isset($request->proteins) && isset($request->proteins[$nutrition_it])) {
                $updateData['proteins'] = $request->proteins[$nutrition_it];
            }
            if (isset($request->fats) && isset($request->fats[$nutrition_it])) {
                $updateData['fats'] = $request->fats[$nutrition_it];
            }
            if (isset($request->carbohydrates) && isset($request->carbohydrates[$nutrition_it])) {
                $updateData['carbohydrates'] = $request->carbohydrates[$nutrition_it];
            }
            if (isset($request->kcal) && isset($request->kcal[$nutrition_it])) {
                $updateData['kcal'] = $request->kcal[$nutrition_it];
            }

            Menu_composition::where('id', $row)->update($updateData);

            $it++;

            // Nutrition ustunlari faqat birinchi iteratsiyada increment bo'ladi
            // chunki har bir yosh guruhi uchun bir xil nutrition ma'lumotlari ishlatiladi
            if ($it % count($request->rows) == 1) {
                $nutrition_it++;
            }
        }
        return redirect()->route('technolog.menuitem', $request->menuid);
    }

    public function deletemenufood(Request $request)
    {
        Menu_composition::where('title_menu_id', $request->menuid)
            ->where('menu_meal_time_id', $request->timeid)
            ->where('menu_food_id', $request->foodid)
            ->delete();
        return redirect()->route('technolog.menuitem', $request->menuid);
    }

    public function getfoodnametoday(Request $request)
    {
        $food = Menu_composition::where('title_menu_id', $request->menuid)
            ->where('menu_meal_time_id', 3)
            ->join('food', 'food.id', '=', 'menu_compositions.menu_food_id')
            ->get(['food.food_name', 'food.id as foodid']);

        $html = "<p>Xodimlar ovqatini tanlang.</p>";
        $bool = [];
        foreach ($food as $row) {
            if (empty($bool[$row->foodid])) {
                $bool[$row->foodid] = 1;
                $html = $html . "<input type='checkbox' class='checkfood' value=" . $row->foodid . "> <span id=" . 'worfood' . $row->foodid . ">" . $row->food_name . "</span> <br>";
            }
        }
        $html = $html . "</select>";
        echo $html;
    }

    public function sendtoallgarden(Request $request)
    {
        dd('OK');
    }

    public function editnextworkers(Request $request)
    {
        // soat
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");

        Nextday_namber::where('kingar_name_id', $request->kingid)
            ->update(['workers_count' => $request->workers]);

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    public function editnextcheldren(Request $request)
    {

        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        $currentRecord = Nextday_namber::where('id', $request->nextrow)->first();

        Nextday_namber::where('id', $request->nextrow)
            ->update(['kingar_children_number' => $request->agecount]);
        ChildrenCountHistory::create([
            'kingar_name_id' => $currentRecord->kingar_name_id,
            'king_age_name_id' => $currentRecord->king_age_name_id,
            'old_children_count' => $currentRecord->kingar_children_number,
            'new_children_count' => $request->agecount,
            'changed_by' => auth()->user()->id,
            'changed_at' => now(),
            'change_reason' => 'Admin tomonidan qo\'lda o\'zgartirildi'
        ]);

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }
    public function fornextmenuselect(Request $request)
    {
        $s = Season::where('hide', 1)->first();
        $titles = Titlemenu::all();
        $html = "<select name='menuid' class='form-select' required aria-label='Default select example'>";
        foreach ($titles as $row) {
            if ($row->id == $request->menuid)
                $html = $html . "<option value=" . $row->id . " selected>" . $row->menu_name . "</option>";
            else
                $html = $html . "<option value=" . $row->id . ">" . $row->menu_name . "</option>";
        }

        $html = $html . "</select>";

        return $html;
    }

    public function editnextmenu(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-8 hours 30 minutes");
        Nextday_namber::where('id', $request->nextrow)->update(['kingar_menu_id' => $request->menuid]);
        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    // sklad

    // skladga buyurtma pdf
    public function orderskladpdf(Request $request, $id)
    {
        $document = order_product::where('order_products.id', $id)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->first(['kindgardens.kingar_name', 'order_products.id as docid', 'order_products.order_title']);
        // dd($document);
        $items = order_product_structure::where('order_product_name_id', $id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
            // ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name')
            ->get();
        // dd($items);
        $dompdf = new Dompdf('UTF-8');
        if (env('ISECONOMY')) {
            $html = mb_convert_encoding(view('pdffile.technolog.orderskladpdf', compact('items', 'document')), 'HTML-ENTITIES', 'UTF-8');
        }
        else {
            $html = mb_convert_encoding(view('pdffile.storage.orderTitleKid', compact('items', 'document')), 'HTML-ENTITIES', 'UTF-8');
        }
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
    // chef 
    public function allchefs()
    {
        $users = User::where('role_id', 6)->get();
        return view('technolog.allchefs', compact('users'));
    }

    public function addchef()
    {
        $kindgardens = Kindgarden::with('user')->get();

        return view('technolog.addchef', compact('kindgardens'));
    }

    public function createchef(Request $request)
    {
        // dd($request->all());
        $user = User::create([
            'role_id' => 6,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'avatar' => "users/default.png",
            'email_verified_at' => NULL,
            'password' => bcrypt($request->password),
            'remember_token' => Str::random(60),
            'settings' => NULL,
        ]);

        $tags = $request->kinid;
        $user->kindgarden()->sync($tags);

        return redirect()->route('technolog.allchefs');
    }

    public function chefsettings(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $kindgardens = Kindgarden::where('hide', 1)->get();
        return view('technolog.chefsettings', compact('user', 'kindgardens'));
    }

    public function updatechef(Request $request)
    {
        $user = User::find($request->userid);

        // Asosiy ma'lumotlarni yangilash
        $updateData = [
            'name' => $request->chefname,
            'email' => $request->chefemail,
            'phone' => $request->phone,
        ];

        // Parol validatsiyasi va yangilash
        if (!empty($request->newpassword)) {
            if ($request->newpassword !== $request->confirmpassword) {
                return redirect()->back()->withErrors(['password' => 'Parollar mos kelmaydi!'])->withInput();
            }

            if (strlen($request->newpassword) < 6) {
                return redirect()->back()->withErrors(['password' => 'Parol kamida 6 ta belgi bo\'lishi kerak!'])->withInput();
            }

            $updateData['password'] = bcrypt($request->newpassword);
        }

        // User ma'lumotlarini yangilash
        $user->update($updateData);

        // Bog'chalar bilan bog'lanishni yangilash
        $gardens = $request->gardens ?? [];
        $user->kindgarden()->sync($gardens);

        return redirect()->route('technolog.allchefs')->with('status', 'Chef ma\'lumotlari muvaffaqiyatli yangilandi!');
    }

    public function chefgetproducts(Request $request)
    {
        $day = Day::join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->orderBy('id', 'DESC')->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);

        $all = minus_multi_storage::where('day_id', $day->id + 1)
            ->join('kindgardens', 'kindgardens.id', '=', 'minus_multi_storages.kingarden_name_id')
            ->orderBy('kingarden_name_id', 'DESC')
            ->get(['minus_multi_storages.id', 'kindgardens.kingar_name', 'minus_multi_storages.product_name_id', 'minus_multi_storages.product_weight', 'minus_multi_storages.kingarden_name_id']);
        $arr = [];
        // dd($arr);
        $products = [];
        $kindgardens = Kindgarden::where('hide', 1)->get();
        foreach ($all as $row) {
            $arr[$row->kingarden_name_id]['name'] = $row->kingar_name;
            $arr[$row->kingarden_name_id][$row->product_name_id] = $row->product_weight;
            // $arr[$row->kingarden_name_id]['row'] = $row->id;
            $r = Product::where('id', $row->product_name_id)->first();
            $r['yes'] = 'ok';
            if (!isset($pbool[$row->product_name_id]))
                $products[] = $r;
            $pbool[$row->product_name_id] = 1;
        }
        // dd($products);
        return view('technolog.chefgetproducts', ['kindgardens' => $kindgardens, 'day' => $day, 'all' => $arr, 'products' => $products]);
    }

    public function chefeditproductw(Request $request)
    {
        // dd($request->all());
        minus_multi_storage::where('day_id', $request->dayid)->where('kingarden_name_id', $request->kingid)->where('product_name_id', $request->prodid)
            ->update(['product_weight' => $request->kg]);

        return redirect()->route('technolog.chefgetproducts');
    }
    // end chif

    // kichkina skladlar /////////////////////////////////////////
    public function minusmultistorage(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $ill = $monthid;
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();
        // Faqat joriy yilga tegishli oylarni olish
        $months = Month::where('yearid', $year->id)->get();
        $days = Day::where('year_id', $year->id)->where('month_id', $month->id)->get();
        $minusproducts = [];

        foreach ($days as $day) {
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.kingar_menu_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id . "+"] = 0;
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                    $minusproducts[$row->product_name_id][$day->id . '-'] = 0;
                }
                if ($row->kingar_menu_id == -1) {
                    $minusproducts[$row->product_name_id][$day->id . "-"] += $row->product_weight;
                }
                else {
                    $minusproducts[$row->product_name_id][$day->id . "+"] += $row->product_weight;
                }
                $minusproducts[$row->product_name_id][$day->id] = $minusproducts[$row->product_name_id][$day->id . "-"] + $minusproducts[$row->product_name_id][$day->id . "+"];
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        return view('technolog.minusmultistorage', [
            'minusproducts' => $minusproducts,
            'kingar' => $king,
            'days' => $days,
            'months' => $months,
            'month' => $month,
            'monthid' => $ill,
            'year' => $year
        ]);
    }

    public function editminusproduct(Request $request)
    {
        $productId = $request->prodid;
        $dayId = $request->dayid;
        $kingardenId = $request->kinid;
        $weight = $request->kg;

        // Mavjud yozuvni topish
        $minusStorage = minus_multi_storage::where('day_id', $dayId)
            ->where('kingarden_name_id', $kingardenId)
            ->where('product_name_id', $productId)
            ->first();

        // Eski qiymatni saqlash (log uchun)
        $oldValue = $minusStorage ? round($minusStorage->product_weight, 2) : 0;

        if ($minusStorage) {
            // Yangilash
            if ($weight > 0) {
                $minusStorage->update([
                    'product_weight' => $weight
                ]);
            }
            else {
                // Agar 0 bo'lsa, o'chirish
                $minusStorage->delete();
            }

            // Log yozish
            StorageChangeLog::create([
                'kingarden_id' => $kingardenId,
                'product_id' => $productId,
                'day_id' => $dayId,
                'type' => 'minus',
                'old_value' => $oldValue,
                'new_value' => $weight,
                'difference' => $weight - $oldValue,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Unknown',
            ]);
        }

        return redirect()->route('technolog.minusmultistorage', ['id' => $kingardenId, 'monthid' => $request->monthid]);
    }

    public function minusmultistoragePDF(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();
        $days = Day::where('year_id', $year->id)->where('month_id', $month->id)->get();
        $minusproducts = [];

        foreach ($days as $day) {
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.kingar_menu_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
            ]);
            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id . "+"] = 0;
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                    $minusproducts[$row->product_name_id][$day->id . '-'] = 0;
                }
                if ($row->kingar_menu_id == -1) {
                    $minusproducts[$row->product_name_id][$day->id . "-"] += $row->product_weight;
                }
                else {
                    $minusproducts[$row->product_name_id][$day->id . "+"] += $row->product_weight;
                }
                $minusproducts[$row->product_name_id][$day->id] = $minusproducts[$row->product_name_id][$day->id . "-"] + $minusproducts[$row->product_name_id][$day->id . "+"];
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }

        $pdf = \PDF::loadView('pdffile.technolog.minusmultistorage', [
            'minusproducts' => $minusproducts,
            'kingar' => $king,
            'days' => $days,
            'month' => $month,
            'year' => $year
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('minusmultistorage_' . $king->kingar_name . '_' . $month->month_name . '.pdf');
    }

    public function minusmultistorageExcel(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();

        return \Excel::download(new \App\Exports\MinusmultistorageExport($kid, $monthid), 'minusmultistorage_' . $king->kingar_name . '_' . $month->month_name . '.xlsx');
    }

    public function addResidualStorage(Request $request)
    {
        // dd($request->all());
        foreach ($request->weights as $key => $value) {
            $check = plus_multi_storage::where('day_id', $request->day_id)
                ->where('kingarden_name_d', $request->kingarden_id)
                ->where('product_name_id', $key)
                ->where('residual', 1)
                ->first();
            if ($check) {
                $check->update([
                    'product_weight' => $value,
                ]);
            }
            else {
                plus_multi_storage::create([
                    'day_id' => $request->day_id,
                    'kingarden_name_d' => $request->kingarden_id,
                    'product_name_id' => $key,
                    'shop_id' => -1,
                    'order_product_id' => 0,
                    'product_weight' => $value ?? 0,
                    'residual' => 1,
                ]);
            }
        }
        return redirect()->route('technolog.plusmultistorage', ['id' => $request->kingarden_id, 'monthid' => 0]);
    }

    public function plusmultistorage(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $ill = $monthid;
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $products = Product::where('hide', 1)->join('sizes', 'products.size_name_id', '=', 'sizes.id')->get(['products.id', 'products.product_name', 'sizes.size_name']);
        // Faqat joriy yilga tegishli oylarni olish
        $months = Month::where('yearid', $year->id)->get();
        $days = Day::where('year_id', $year->id)->where('month_id', Month::where('id', $monthid)->first()->id)->get();

        // Sarflangan mahsulotlar (minus) - har bir kun uchun
        $minusproducts = [];
        foreach ($days as $day) {
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }

        // O'tgan oydan qoldiqni olish (residual = 1)
        $residualProducts = [];
        $residualData = plus_multi_storage::where('kingarden_name_d', $kid)
            ->where('residual', 1)
            ->where('day_id', '>=', $days->first()->id)
            ->where('day_id', '<=', $days->last()->id)
            ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
            ->get([
            'plus_multi_storages.product_name_id',
            'plus_multi_storages.product_weight',
            'products.product_name',
        ]);

        foreach ($residualData as $row) {
            if (!isset($residualProducts[$row->product_name_id])) {
                $residualProducts[$row->product_name_id] = [
                    'weight' => 0,
                    'productname' => $row->product_name
                ];
            }
            $residualProducts[$row->product_name_id]['weight'] += $row->product_weight;
        }

        $plusproducts = [];
        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kid)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.shop_id',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.product_weight',
                'plus_multi_storages.residual',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            foreach ($plus as $row) {
                // residual = 1 bo'lganlarni o'tkazib yuborish (ular qoldiqda ko'rsatiladi)
                if ($row->residual == 1) {
                    continue;
                }

                if (!isset($plusproducts[$row->product_name_id][$day->id])) {
                    $plusproducts[$row->product_name_id][$day->id . "+"] = 0;
                    $plusproducts[$row->product_name_id][$day->id] = 0;
                    $plusproducts[$row->product_name_id][$day->id . '-'] = 0;
                }
                if ($row->shop_id == -1) {
                    $plusproducts[$row->product_name_id][$day->id . "-"] += $row->product_weight;
                }
                else {
                    $plusproducts[$row->product_name_id][$day->id . "+"] += $row->product_weight;
                }
                $plusproducts[$row->product_name_id][$day->id] = $plusproducts[$row->product_name_id][$day->id . "-"] + $plusproducts[$row->product_name_id][$day->id . "+"];
                $plusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }

        // Faqat qoldiq bo'lib, kunlik ma'lumoti yo'q mahsulotlarni qo'shish
        foreach ($residualProducts as $productId => $residualData) {
            if (!isset($plusproducts[$productId])) {
                $plusproducts[$productId] = ['productname' => $residualData['productname']];
            }
        }

        // Faqat minus bo'lib, plus bo'lmagan mahsulotlarni qo'shish
        foreach ($minusproducts as $productId => $minusData) {
            if (is_numeric($productId) && !isset($plusproducts[$productId])) {
                $plusproducts[$productId] = ['productname' => $minusData['productname']];
            }
        }

        return view('technolog.plusmultistorage', ['plusproducts' => $plusproducts, 'minusproducts' => $minusproducts, 'residualProducts' => $residualProducts, 'products' => $products, 'kingar' => $king, 'days' => $days, 'months' => $months, 'monthid' => $ill, 'year' => $year]);
    }

    public function plusmultistoragePDF(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();
        $days = Day::where('year_id', $year->id)->where('month_id', $month->id)->get();

        // Sarflangan mahsulotlar (minus) - har bir kun uchun
        $minusproducts = [];
        foreach ($days as $day) {
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.div',
            ]);
            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }

        // O'tgan oydan qoldiqni olish (residual = 1)
        $residualProducts = [];
        $residualData = plus_multi_storage::where('kingarden_name_d', $kid)
            ->where('residual', 1)
            ->where('day_id', '>=', $days->first()->id)
            ->where('day_id', '<=', $days->last()->id)
            ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
            ->get([
            'plus_multi_storages.product_name_id',
            'plus_multi_storages.product_weight',
            'products.product_name',
        ]);

        foreach ($residualData as $row) {
            if (!isset($residualProducts[$row->product_name_id])) {
                $residualProducts[$row->product_name_id] = [
                    'weight' => 0,
                    'productname' => $row->product_name
                ];
            }
            $residualProducts[$row->product_name_id]['weight'] += $row->product_weight;
        }

        $plusproducts = [];
        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kid)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.shop_id',
                'plus_multi_storages.product_weight',
                'plus_multi_storages.residual',
                'products.product_name',
            ]);
            foreach ($plus as $row) {
                // residual = 1 bo'lganlarni o'tkazib yuborish (ular qoldiqda ko'rsatiladi)
                if ($row->residual == 1) {
                    continue;
                }

                if (!isset($plusproducts[$row->product_name_id][$day->id])) {
                    $plusproducts[$row->product_name_id][$day->id . "+"] = 0;
                }
                if ($row->shop_id != -1) {
                    $plusproducts[$row->product_name_id][$day->id . "+"] += $row->product_weight;
                }
                $plusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }

        // Faqat qoldiq bo'lib, kunlik ma'lumoti yo'q mahsulotlarni qo'shish
        foreach ($residualProducts as $productId => $residualData) {
            if (!isset($plusproducts[$productId])) {
                $plusproducts[$productId] = ['productname' => $residualData['productname']];
            }
        }

        // Faqat minus bo'lib, plus bo'lmagan mahsulotlarni qo'shish
        foreach ($minusproducts as $productId => $minusData) {
            if (is_numeric($productId) && !isset($plusproducts[$productId])) {
                $plusproducts[$productId] = ['productname' => $minusData['productname']];
            }
        }

        $pdf = \PDF::loadView('pdffile.technolog.plusmultistorage', [
            'plusproducts' => $plusproducts,
            'minusproducts' => $minusproducts,
            'residualProducts' => $residualProducts,
            'kingar' => $king,
            'days' => $days,
            'month' => $month,
            'year' => $year
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('plusmultistorage_' . $king->kingar_name . '_' . $month->month_name . '.pdf');
    }

    public function plusmultistorageExcel(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();

        return \Excel::download(new \App\Exports\PlusmultistorageExport($kid, $monthid), 'plusmultistorage_' . $king->kingar_name . '_' . $month->month_name . '.xlsx');
    }

    public function editMinusStorage(Request $request)
    {
        $productId = $request->product_id;
        $dayId = $request->day_id;
        $kingardenId = $request->kingarden_id;
        $weight = $request->weight;

        // Mavjud yozuvni qidirish
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Mahsulot topilmadi'], 404);
        }

        $minusStorage = minus_multi_storage::where('day_id', $dayId)
            ->where('kingarden_name_id', $kingardenId)
            ->where('product_name_id', $productId)
            ->first();

        // div bilan ko'paytirmaslik kerak, chunki view da ham div dan foydalanmayapmiz
        $weightWithDiv = $weight;

        // Eski qiymatni saqlash (log uchun)
        $oldValue = $minusStorage ? round($minusStorage->product_weight, 2) : 0;

        if ($minusStorage) {
            // Yangilash
            if ($weight > 0) {
                $minusStorage->update([
                    'product_weight' => $weightWithDiv
                ]);
            }
            else {
                // Agar 0 bo'lsa, o'chirish
                $minusStorage->delete();
            }
        }
        else {
            // Yangi yozuv yaratish (faqat 0 dan katta bo'lsa)
            if ($weight > 0) {
                minus_multi_storage::create([
                    'day_id' => $dayId,
                    'kingarden_name_id' => $kingardenId,
                    'product_name_id' => $productId,
                    'product_weight' => $weightWithDiv,
                ]);
            }
        }

        // Log yozish
        StorageChangeLog::create([
            'kingarden_id' => $kingardenId,
            'product_id' => $productId,
            'day_id' => $dayId,
            'type' => 'minus',
            'old_value' => $oldValue,
            'new_value' => $weight,
            'difference' => $weight - $oldValue,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
        ]);

        return response()->json(['success' => true]);
    }

    public function editPlusStorage(Request $request)
    {
        $productId = $request->product_id;
        $dayId = $request->day_id;
        $kingardenId = $request->kingarden_id;
        $weight = $request->weight;

        // Mavjud yozuvni qidirish (shop_id != -1 bo'lgan kirimlar)
        $plusStorage = plus_multi_storage::where('day_id', $dayId)
            ->where('kingarden_name_d', $kingardenId)
            ->where('product_name_id', $productId)
            ->where('shop_id', '!=', -1)
            ->where('residual', 0)
            ->first();

        // Eski qiymatni saqlash (log uchun)
        $oldValue = $plusStorage ? round($plusStorage->product_weight, 2) : 0;

        if ($plusStorage) {
            // Yangilash
            if ($weight > 0) {
                $plusStorage->update([
                    'product_weight' => $weight
                ]);
            }
            else {
                // Agar 0 bo'lsa, o'chirish
                $plusStorage->delete();
            }
        }
        else {
            // Yangi yozuv yaratish (faqat 0 dan katta bo'lsa)
            if ($weight > 0) {
                plus_multi_storage::create([
                    'day_id' => $dayId,
                    'shop_id' => 0, // Qo'lda kiritilgan
                    'kingarden_name_d' => $kingardenId,
                    'product_name_id' => $productId,
                    'product_weight' => $weight,
                    'residual' => 0,
                ]);
            }
        }

        // Log yozish
        StorageChangeLog::create([
            'kingarden_id' => $kingardenId,
            'product_id' => $productId,
            'day_id' => $dayId,
            'type' => 'plus',
            'old_value' => $oldValue,
            'new_value' => $weight,
            'difference' => $weight - $oldValue,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
        ]);

        return response()->json(['success' => true]);
    }

    public function storageChangeLogs(Request $request, $kid, $monthid)
    {
        $king = Kindgarden::where('id', $kid)->first();
        $year = Year::where('year_active', 1)->first();
        if ($monthid == 0) {
            $monthid = Month::where('month_active', 1)->first()->id;
        }
        $month = Month::where('id', $monthid)->first();
        $months = Month::where('yearid', $year->id)->get();

        // O'zgarishlar tarixini olish
        $logs = StorageChangeLog::where('kingarden_id', $kid)
            ->with(['product', 'day', 'user'])
            ->whereHas('day', function ($query) use ($monthid, $year) {
            $query->where('month_id', $monthid)
                ->where('year_id', $year->id);
        })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('technolog.storageChangeLogs', [
            'logs' => $logs,
            'kingar' => $king,
            'month' => $month,
            'months' => $months,
            'monthid' => $monthid,
            'year' => $year
        ]);
    }

    public function editResidualStorage(Request $request)
    {
        $productId = $request->product_id;
        $kingardenId = $request->kingarden_id;
        $weight = $request->weight;

        // Mavjud residual yozuvni qidirish
        $residualStorage = plus_multi_storage::where('kingarden_name_d', $kingardenId)
            ->where('product_name_id', $productId)
            ->where('residual', 1)
            ->first();

        // Agar yangi yozuv yaratish kerak bo'lsa, hozirgi oyning birinchi kunini olish
        $year = Year::where('year_active', 1)->first();
        $month = Month::where('month_active', 1)->first();
        $firstDay = Day::where('year_id', $year->id)
            ->where('month_id', $month->id)
            ->orderBy('day_number', 'asc')
            ->first();

        if (!$firstDay) {
            return response()->json(['success' => false, 'message' => 'Kun topilmadi'], 404);
        }

        if ($residualStorage) {
            // Yangilash
            if ($weight > 0) {
                $residualStorage->update([
                    'product_weight' => $weight
                ]);
            }
            else {
                // Agar 0 bo'lsa, o'chirish
                $residualStorage->delete();
            }
        }
        else {
            // Yangi yozuv yaratish (faqat 0 dan katta bo'lsa)
            if ($weight > 0) {
                plus_multi_storage::create([
                    'day_id' => $firstDay->id, // Birinchi kun
                    'shop_id' => 0,
                    'kingarden_name_d' => $kingardenId,
                    'product_name_id' => $productId,
                    'product_weight' => $weight,
                    'residual' => 1,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function moveremainder(Request $request)
    {
        $thismonth = Month::where('month_active', 1)->first();
        $prevmonth = Day::where('month_id', $thismonth->id - 1)->get();
        $kinds = Kindgarden::where('id', $request->kind)->get();
        $products = Product::all();
        $modproduct = [];

        $allminusproducts = [];
        $allplusproducts = [];
        foreach ($kinds as $kid) {
            $prevmods = [];
            $minusproducts = [];
            $plusproducts = [];
            $takedproducts = [];
            $actualweights = [];
            $addeds = [];
            $plus = plus_multi_storage::where('day_id', '>=', $prevmonth->first()->id)->where('day_id', '<=', $prevmonth->last()->id)
                ->where('kingarden_name_d', $kid->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->orderby('plus_multi_storages.day_id', 'DESC')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.residual',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $minus = minus_multi_storage::where('day_id', '>=', $prevmonth->first()->id)->where('day_id', '<=', $prevmonth->last()->id)
                ->where('kingarden_name_id', $kid->id)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kid->id)
                ->where('take_groups.day_id', '>=', $prevmonth->first()->id)->where('take_groups.day_id', '<=', $prevmonth->last()->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                'take_small_bases.id',
                'take_small_bases.product_id',
                'take_groups.day_id',
                'take_small_bases.kindgarden_id',
                'take_small_bases.weight',
            ]);
            foreach ($prevmonth as $day) {
                foreach ($minus->where('day_id', $day->id) as $row) {
                    if (!isset($minusproducts[$row->product_name_id])) {
                        $minusproducts[$row->product_name_id] = 0;
                    }
                    $minusproducts[$row->product_name_id] += $row->product_weight;
                }
                foreach ($trashes->where('day_id', $day->id) as $row) {
                    if (!isset($takedproducts[$row->product_id])) {
                        $takedproducts[$row->product_id] = 0;
                    }
                    if (!isset($minusproducts[$row->product_id])) {
                        $minusproducts[$row->product_id] = 0;
                    }
                    $takedproducts[$row->product_id] += $row->weight;
                    $minusproducts[$row->product_id] += $row->weight;
                }
                foreach ($plus->where('day_id', $day->id) as $row) {
                    if (!isset($prevmods[$row->product_name_id])) {
                        $prevmods[$row->product_name_id] = 0;
                    }
                    if (!isset($plusproducts[$row->product_name_id])) {
                        $plusproducts[$row->product_name_id] = 0;
                        $addeds[$row->product_name_id] = 0;
                    }
                    if ($row->residual == 0) {
                        $plusproducts[$row->product_name_id] += $row->product_weight;
                        $takedproducts[$row->product_name_id] = 0;
                    }
                    else {
                        $prevmods[$row->product_name_id] += $row->product_weight;
                        $plusproducts[$row->product_name_id] += $row->product_weight;
                    }

                }

                foreach ($products as $row) {
                    if (!isset($prevmods[$row->id])) {
                        $prevmods[$row->id] = 0;
                    }
                    if (!isset($plusproducts[$row->id])) {
                        $plusproducts[$row->id] = 0;
                    }
                    if (!isset($minusproducts[$row->id])) {
                        $minusproducts[$row->id] = 0;
                    }
                    $minusproducts[$row->id] = ($plusproducts[$row->id] - $minusproducts[$row->id] < 0) ? ($plusproducts[$row->id] - $minusproducts[$row->id]) + $minusproducts[$row->id] : $minusproducts[$row->id];
                }

                $groups = Groupweight::where('kindergarden_id', $kid->id)
                    ->where('day_id', $day->id)
                    ->get();
                foreach ($groups as $group) {
                    $actuals = Weightproduct::where('groupweight_id', $group->id)->get();
                    foreach ($products as $row) {
                        if (!isset($prevmods[$row->id])) {
                            $prevmods[$row->id] = 0;
                        }
                        if (!isset($plusproducts[$row->id])) {
                            $plusproducts[$row->id] = 0;
                        }
                        if (!isset($added[$row->id])) {
                            $added[$row->id] = 0;
                        }
                        if (!isset($minusproducts[$row->id])) {
                            $minusproducts[$row->id] = 0;
                        }
                        if (!isset($takedproducts[$row->id])) {
                            $takedproducts[$row->id] = 0;
                        }
                        if (!isset($lost[$row->id])) {
                            $lost[$row->id] = 0;
                        }
                        if ($actuals->where('product_id', $row->id)->count() > 0) {
                            $weight = $actuals->where('product_id', $row->id)->first()->weight;
                        }
                        else {
                            $weight = 0;
                        }
                        if ($weight - ($plusproducts[$row->id] - $minusproducts[$row->id]) < 0) {
                            $lost[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
                        }
                        else {
                            $added[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
                            $plusproducts[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
                        }
                    }
                }

            }

            foreach ($products as $row) {
                if (!isset($allminusproducts[$kid->id][$row->id])) {
                    $allminusproducts[$kid->id][$row->id] = 0;
                }
                if (!isset($plusproducts[$row->id])) {
                    $plusproducts[$row->id] = 0;
                }
                if (!isset($minusproducts[$row->id])) {
                    $minusproducts[$row->id] = 0;
                }
                if (!isset($allplusproducts[$kid->id][$row->id])) {
                    $allplusproducts[$kid->id][$row->id] = 0;
                }
                $allplusproducts[$kid->id][$row->id] += $plusproducts[$row->id];
                $allminusproducts[$kid->id][$row->id] += $minusproducts[$row->id];
            }
        // dd($allminusproducts, $allplusproducts, $plusproducts, $added);
        }

        foreach ($kinds as $kid) {
            foreach ($products as $row) {
                if (!isset($modproduct[$kid->id][$row->id])) {
                    $modproduct[$kid->id][$row->id] = 0;
                }
                $modproduct[$kid->id][$row->id] = $allplusproducts[$kid->id][$row->id] - $allminusproducts[$kid->id][$row->id];
            }
        }

        $firstday = Day::where('month_id', $thismonth->id)->first();

        foreach ($modproduct as $kid => $row) {
            foreach ($row as $pid => $value) {
                $mod = plus_multi_storage::where('day_id', $firstday->id)
                    ->where('kingarden_name_d', $kid)
                    ->where('residual', 1)
                    ->where('product_name_id', $pid)
                    ->get();

                if ($mod->count() == 0 and $value >= 0) {
                    plus_multi_storage::create([
                        'day_id' => $firstday->id,
                        'shop_id' => -1,
                        'kingarden_name_d' => $kid,
                        'order_product_id' => time(),
                        'residual' => 1,
                        'product_name_id' => $pid,
                        'product_weight' => $value,
                    ]);
                }
            }
        }

        return redirect()->route('technolog.plusmultistorage', ['id' => $request->kind, 'monthid' => 0]);
    }

    public function deleteweights(Request $request)
    {
        // dd($request->all());
        Weightproduct::where('groupweight_id', $request->group_id)->delete();
        Groupweight::where('id', $request->group_id)->delete();

        return redirect()->route('technolog.weightcurrent', ['kind' => $request->kindergardenId, 'yearid' => 0, 'monthid' => 0]);
    }

    public function weightsdocument(Request $request, $group_id)
    {
        $group = Groupweight::where('id', $group_id)->first();
        $kind = Kindgarden::where('id', $group->kindergarden_id)->first();
        $day = Day::where('days.id', $group->day_id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($day);
        $products = Weightproduct::where('groupweight_id', $group_id)
            ->join('products', 'products.id', '=', 'weightproducts.product_id')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')->get();
        // dd($products);
        $document = [];
        foreach ($products as $row) {
            if ($row->weight > 0) {
                $document[$row->product_id]['group_id'] = $row->groupweight_id;
                $document[$row->product_id]['product_name'] = $row->product_name;
                $document[$row->product_id]['size_name'] = $row->size_name;
                $document[$row->product_id]['sort'] = $row->sort;
                $document[$row->product_id]['weight'] = $row->weight;
                $document[$row->product_id]['cost'] = 0;
            }
        }
        usort($document, function ($a, $b) {
            if (isset($a["sort"]) and isset($b["sort"])) {
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($document);
        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('pdffile.technolog.weightsdocument', compact('document', 'kind', 'day')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);

    }

    public function monthlyweights(Request $request, $kid, $monthid)
    {
        $days = $this->activmonth($monthid);
        $products = Product::orderBy('sort', 'ASC')->get();
        $kind = Kindgarden::where('id', $kid)->first();
        $groups = Groupweight::where('groupweights.kindergarden_id', $kid)
            ->where('days.month_id', $monthid)
            ->join('days', 'days.id', '=', 'groupweights.day_id')
            ->get([
            'groupweights.id',
            'groupweights.kindergarden_id',
            'groupweights.day_id',
            'days.day_number'
        ]);

        $document = [];
        foreach ($groups as $row) {
            $prods = Weightproduct::where('weightproducts.groupweight_id', $row->id)
                ->join('products', 'products.id', '=', 'weightproducts.product_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')->get();
            foreach ($prods as $product) {
                if ($product->weight > 0) {
                    $document[$product->product_id][$row->day_id]['group_id'] = $product->groupweight_id;
                    $document[$product->product_id][$row->day_id]['product_id'] = $product->product_id;
                    $document[$product->product_id][$row->day_id]['size_name'] = $product->size_name;
                    $document[$product->product_id][$row->day_id]['sort'] = $product->sort;
                    $document[$product->product_id][$row->day_id]['weight'] = $product->weight;
                    $document[$product->product_id][$row->day_id]['cost'] = 0;
                }
            }
        }

        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('pdffile.technolog.monthlyreport', compact('document', 'kind', 'days', 'products')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    public function reportinout(Request $request)
    {
        $days = $this->activmonth($request->month_id);
        $products = Product::orderBy('sort', 'ASC')->get();
        $kind = Kindgarden::where('id', $request->kindergarden_id)->first();

        $prevmods = [];
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $isThisMeasureDay = [];

        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kind->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.residual',
                'plus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kind->id)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kind->id)
                ->where('take_groups.day_id', $day->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                'take_small_bases.id',
                'take_small_bases.product_id',
                'take_groups.day_id',
                'take_small_bases.kindgarden_id',
                'take_small_bases.weight',
            ]);
            $groups = Groupweight::where('kindergarden_id', $kind->id)
                ->where('day_id', $day->id)
                ->first();
            if (isset($groups)) {
                $actuals = Weightproduct::where('groupweight_id', $groups->id)->get();
            }
            else {
                $actuals = [];
            }

            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
            }
            foreach ($plus as $row) {
                if (!isset($prevmods[$row->product_name_id])) {
                    $prevmods[$row->product_name_id] = 0;
                }
                if (!isset($plusproducts[$row->product_name_id][$day->id])) {
                    $plusproducts[$row->product_name_id][$day->id] = 0;
                    $addeds[$row->product_name_id][$day->id] = 0;
                }
                if ($row->residual == 0) {
                    $plusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                    $takedproducts[$row->product_name_id][$day->id] = 0;
                }
                else {
                    $prevmods[$row->product_name_id] += $row->product_weight;
                }
            }
            foreach ($trashes as $row) {
                if (!isset($takedproducts[$row->product_id][$day->id])) {
                    $takedproducts[$row->product_id][$day->id] = 0;
                }
                $takedproducts[$row->product_id][$day->id] += $row->weight;
            }
            foreach ($actuals as $row) {
                if (!isset($actualweights[$row->product_id][$day->id])) {
                    $actualweights[$row->product_id][$day->id] = 0;
                    $isThisMeasureDay[$day->id] = 1;
                }
                if (!isset($plusproducts[$row->product_id][$day->id])) {
                    $plusproducts[$row->product_id][$day->id] = 0;
                    $addeds[$row->product_id][$day->id] = 0;
                }
                if (!isset($minusproducts[$row->product_id][$day->id])) {
                    $minusproducts[$row->product_id][$day->id] = 0;
                }
                $actualweights[$row->product_id][$day->id] += $row->weight;
            }
        }

        return view('technolog.reportinout', compact('prevmods', 'kind', 'days', 'products', 'minusproducts', 'plusproducts', 'takedproducts', 'actualweights', 'isThisMeasureDay'));
    }

    public function reportinoutpdf(Request $request)
    {
        $days = $this->activmonth($request->month_id);
        $products = Product::orderBy('sort', 'ASC')->get();
        $kind = Kindgarden::where('id', $request->kindergarden_id)->first();

        $prevmods = [];
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $isThisMeasureDay = [];

        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kind->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.residual',
                'plus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kind->id)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kind->id)
                ->where('take_groups.day_id', $day->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                'take_small_bases.id',
                'take_small_bases.product_id',
                'take_groups.day_id',
                'take_small_bases.kindgarden_id',
                'take_small_bases.weight',
            ]);
            $groups = Groupweight::where('kindergarden_id', $kind->id)
                ->where('day_id', $day->id)
                ->first();
            if (isset($groups)) {
                $actuals = Weightproduct::where('groupweight_id', $groups->id)->get();
            }
            else {
                $actuals = [];
            }

            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
            }
            foreach ($plus as $row) {
                if (!isset($prevmods[$row->product_name_id])) {
                    $prevmods[$row->product_name_id] = 0;
                }
                if (!isset($plusproducts[$row->product_name_id][$day->id])) {
                    $plusproducts[$row->product_name_id][$day->id] = 0;
                    $addeds[$row->product_name_id][$day->id] = 0;
                }
                if ($row->residual == 0) {
                    $plusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                    $takedproducts[$row->product_name_id][$day->id] = 0;
                }
                else {
                    $prevmods[$row->product_name_id] += $row->product_weight;
                }
            }
            foreach ($trashes as $row) {
                if (!isset($takedproducts[$row->product_id][$day->id])) {
                    $takedproducts[$row->product_id][$day->id] = 0;
                }
                $takedproducts[$row->product_id][$day->id] += $row->weight;
            }
            foreach ($actuals as $row) {
                if (!isset($actualweights[$row->product_id][$day->id])) {
                    $actualweights[$row->product_id][$day->id] = 0;
                    $isThisMeasureDay[$day->id] = 1;
                }
                if (!isset($plusproducts[$row->product_id][$day->id])) {
                    $plusproducts[$row->product_id][$day->id] = 0;
                    $addeds[$row->product_id][$day->id] = 0;
                }
                if (!isset($minusproducts[$row->product_id][$day->id])) {
                    $minusproducts[$row->product_id][$day->id] = 0;
                }
                $actualweights[$row->product_id][$day->id] += $row->weight;
            }
        }
        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('pdffile.technolog.reportinout', compact('prevmods', 'kind', 'days', 'products', 'minusproducts', 'plusproducts', 'takedproducts', 'actualweights', 'isThisMeasureDay')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A0', 'landscape');
        $dompdf->render();
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    public function reportinoutexcel(Request $request)
    {
        $days = $this->activmonth($request->month_id);
        $products = Product::orderBy('sort', 'ASC')->get();
        $kind = Kindgarden::where('id', $request->kindergarden_id)->first();

        $prevmods = [];
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $isThisMeasureDay = [];

        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kind->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.residual',
                'plus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kind->id)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kind->id)
                ->where('take_groups.day_id', $day->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                'take_small_bases.id',
                'take_small_bases.product_id',
                'take_groups.day_id',
                'take_small_bases.kindgarden_id',
                'take_small_bases.weight',
            ]);
            $groups = Groupweight::where('kindergarden_id', $kind->id)
                ->where('day_id', $day->id)
                ->first();
            if (isset($groups)) {
                $actuals = Weightproduct::where('groupweight_id', $groups->id)->get();
            }
            else {
                $actuals = [];
            }

            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id][$day->id])) {
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
            }
            foreach ($plus as $row) {
                if (!isset($prevmods[$row->product_name_id])) {
                    $prevmods[$row->product_name_id] = 0;
                }
                if (!isset($plusproducts[$row->product_name_id][$day->id])) {
                    $plusproducts[$row->product_name_id][$day->id] = 0;
                    $addeds[$row->product_name_id][$day->id] = 0;
                }
                if ($row->residual == 0) {
                    $plusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                    $takedproducts[$row->product_name_id][$day->id] = 0;
                }
                else {
                    $prevmods[$row->product_name_id] += $row->product_weight;
                }
            }
            foreach ($trashes as $row) {
                if (!isset($takedproducts[$row->product_id][$day->id])) {
                    $takedproducts[$row->product_id][$day->id] = 0;
                }
                $takedproducts[$row->product_id][$day->id] += $row->weight;
            }
            foreach ($actuals as $row) {
                if (!isset($actualweights[$row->product_id][$day->id])) {
                    $actualweights[$row->product_id][$day->id] = 0;
                    $isThisMeasureDay[$day->id] = 1;
                }
                if (!isset($plusproducts[$row->product_id][$day->id])) {
                    $plusproducts[$row->product_id][$day->id] = 0;
                    $addeds[$row->product_id][$day->id] = 0;
                }
                if (!isset($minusproducts[$row->product_id][$day->id])) {
                    $minusproducts[$row->product_id][$day->id] = 0;
                }
                $actualweights[$row->product_id][$day->id] += $row->weight;
            }
        }

        $filename = 'reportinout_' . $kind->kingar_name . '_' . $days[0]['month_name'] . '.xlsx';
        return \Excel::download(new \App\Exports\ReportInOutExport($prevmods, $kind, $days, $products, $minusproducts, $plusproducts, $takedproducts, $actualweights, $isThisMeasureDay), $filename);
    }

    public function getmodproduct(Request $request, $kid)
    {
        $king = Kindgarden::where('id', $kid)->with('user')->first();
        $days = Day::where('year_id', Year::where('year_active', 1)->first()->id)->where('month_id', Month::where('month_active', 1)->first()->id)->get();
        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        $prevmods = [];
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $isThisMeasureDay = [];

        foreach ($days as $day) {
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $king->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'plus_multi_storages.id',
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.day_id',
                'plus_multi_storages.residual',
                'plus_multi_storages.kingarden_name_d',
                'plus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $king->id)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                'minus_multi_storages.id',
                'minus_multi_storages.product_name_id',
                'minus_multi_storages.day_id',
                'minus_multi_storages.kingarden_name_id',
                'minus_multi_storages.product_weight',
                'products.product_name',
                'products.size_name_id',
                'products.div',
                'products.sort'
            ]);
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $king->id)
                ->where('take_groups.day_id', $day->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                'take_small_bases.id',
                'take_small_bases.product_id',
                'take_groups.day_id',
                'take_small_bases.kindgarden_id',
                'take_small_bases.weight',
            ]);

            foreach ($minus as $row) {
                if (!isset($minusproducts[$row->product_name_id])) {
                    $minusproducts[$row->product_name_id] = 0;
                }
                $minusproducts[$row->product_name_id] += $row->product_weight;
            }
            foreach ($plus as $row) {
                if (!isset($prevmods[$row->product_name_id])) {
                    $prevmods[$row->product_name_id] = 0;
                }
                if (!isset($plusproducts[$row->product_name_id])) {
                    $plusproducts[$row->product_name_id] = 0;
                    $addeds[$row->product_name_id] = 0;
                }
                if ($row->residual == 0) {
                    $plusproducts[$row->product_name_id] += $row->product_weight;
                    $takedproducts[$row->product_name_id] = 0;
                }
                else {
                    $prevmods[$row->product_name_id] += $row->product_weight;
                }
            }
            foreach ($trashes as $row) {
                if (!isset($takedproducts[$row->product_id])) {
                    $takedproducts[$row->product_id] = 0;
                }
                if (!isset($minusproducts[$row->product_name_id])) {
                    $minusproducts[$row->product_name_id] = 0;
                }
                $takedproducts[$row->product_id] += $row->weight;
            }

            foreach ($products as $row) {
                if (!isset($plusproducts[$row->id])) {
                    $plusproducts[$row->id] = 0;
                }
                if (!isset($minusproducts[$row->id])) {
                    $minusproducts[$row->id] = 0;
                }
                $minusproducts[$row->id] = ($plusproducts[$row->id] - $minusproducts[$row->id] < 0) ? ($plusproducts[$row->id] - $minusproducts[$row->id]) + $minusproducts[$row->id] : $minusproducts[$row->id];
            }

            $groups = Groupweight::where('kindergarden_id', $king->id)
                ->where('day_id', $day->id)
                ->first();
            if (isset($groups)) {
                $actuals = Weightproduct::where('groupweight_id', $groups->id)->get();
                foreach ($products as $row) {
                    if (!isset($prevmods[$row->id])) {
                        $prevmods[$row->id] = 0;
                    }
                    if (!isset($plusproducts[$row->id])) {
                        $plusproducts[$row->id] = 0;
                    }
                    if (!isset($added[$row->id])) {
                        $added[$row->id] = 0;
                    }
                    if (!isset($minusproducts[$row->id])) {
                        $minusproducts[$row->id] = 0;
                    }
                    if (!isset($takedproducts[$row->id])) {
                        $takedproducts[$row->id] = 0;
                    }
                    if (!isset($lost[$row->id])) {
                        $lost[$row->id] = 0;
                    }
                    if ($actuals->where('product_id', $row->id)->count() > 0) {
                        $weight = $actuals->where('product_id', $row->id)->first()->weight;
                    }
                    else {
                        $weight = 0;
                    }
                    if ($weight - (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id])) < 0) {
                        $lost[$row->id] += (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id])) - $weight;
                    }
                    else {
                        $added[$row->id] += $weight - (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id]));
                        $plusproducts[$row->id] += $weight - (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id]));
                    }


                }
            }

        // if($day->id == 686){
        //     dd($weight, $prevmods, $plusproducts, $minusproducts, $takedproducts, $lost, $added);
        // }
        }

        $html = "<table class='table table-light table-striped table-hover'>
                <input type='hidden' name='kingarid' value='" . $kid . "'>
                <input type='hidden' name='chefid' value='" . $king['user'][0]['id'] . "'>
                <input type='hidden' name='dayid' value='" . $days[count($days) - 1]->id . "'>
                <thead>
                    <tr>
                        <th scope='col'>Maxsulot</th>
                        <th scope='col'>O'tgan oydan</th>
                        <th scope='col'>Кирим</th>
                        <th scope='col'>Ортирма</th>
                        <th scope='col'>Чиқим</th>
                        <th scope='col'>Чиқити</th>
                        <th scope='col'>Yo'qolgan</th>
                        <th scope='col'>Қолдиқ</th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($products as $product) {
            if (!isset($prevmods[$product->id])) {
                $prevmods[$product->id] = 0;
            }
            if (!isset($plusproducts[$product->id])) {
                $plusproducts[$product->id] = 0;
            }
            if (!isset($added[$product->id])) {
                $added[$product->id] = 0;
            }
            if (!isset($minusproducts[$product->id])) {
                $minusproducts[$product->id] = 0;
            }
            if (!isset($takedproducts[$product->id])) {
                $takedproducts[$product->id] = 0;
            }
            if (!isset($lost[$product->id])) {
                $lost[$product->id] = 0;
            }
            if (isset($minusproducts[$product->id]) or isset($plusproducts[$product->id])) {
                $html = $html . "<tr>
                            <td>" . $product->product_name . "</td>
                            <td>";
                $totalin = $plusproducts[$product->id] + $prevmods[$product->id];
                $html = $html . sprintf('%0.3f', $prevmods[$product->id]) . "</td>
                            <td>";

                $html = $html . sprintf('%0.3f', $plusproducts[$product->id]) . "</td>
                            <td>";

                $html = $html . sprintf('%0.3f', $added[$product->id]) . "</td>
                            <td>";

                $html = $html . sprintf('%0.3f', $minusproducts[$product->id]) . "</td>
                            <td>";
                $totalout = $minusproducts[$product->id] + $takedproducts[$product->id];
                $html = $html . sprintf('%0.3f', $takedproducts[$product->id]) . "</td><td>";

                $html = $html . sprintf('%0.3f', $lost[$product->id]) . "</td>
                            <td>" . sprintf('%0.3f', $totalin - $totalout) . ' ' . $product->size_name . "</td>
                        </tr>";
            }
        }
        $html = $html . "</tbody>
            </table>
            ";

        return $html;
    }

    public function plusmultimodadd(Request $request)
    {
        // dd($request->all());
        foreach ($request->prodadd as $key => $value) {
            if ($value != null) {
                plus_multi_storage::create([
                    'day_id' => $request->dayid,
                    'shop_id' => -1,
                    'kingarden_name_d' => $request->kingarid,
                    'order_product_id' => 0,
                    'residual' => 0,
                    'product_name_id' => $key,
                    'product_weight' => $value,
                ]);
            }
        }

        $take = Take_group::create([
            'contur_id' => 1,
            'day_id' => $request->dayid,
            'taker_id' => $request->chefid,
            'outside_id' => 1,
            'title' => "Yo'qolgan maxulotlar",
            'description' => "",
        ]);

        foreach ($request->prodminus as $key => $value) {
            if ($value != null) {
                Take_small_base::create([
                    'kindgarden_id' => $request->kingarid,
                    'takegroup_id' => $take->id,
                    'product_id' => $key,
                    'weight' => $value,
                    'cost' => 0,
                ]);
            }
        }

        return redirect()->route('technolog.home');
    }

    public function asdf()
    {

        echo "ok";
    }

    public function finding($day)
    {
        // $days = Day::where('id', '>=', 122)->orderBy('id', 'DESC')->get();
        $kinds = Kindgarden::all();
        $products = Product::all();
        $errors = [];
        foreach ($kinds as $kind) {
            foreach ($products as $product) {
                $find = plus_multi_storage::where('kingarden_name_d', $kind->id)
                    ->where('day_id', $day)
                    ->where('shop_id', 0)
                    ->where('product_name_id', $product->id)
                    ->get();
                $t = 0;
                foreach ($find as $ff) {
                    if ($t != 0) {
                        $ff->delete();
                    }
                    $t++;
                }

                if ($find->count() > 1) {
                    array_push($errors, $find);
                }
            }
        }

        dd($errors);
        dd("OK");
    }

    public function updatemanu()
    {
        $days = $this->days();
        return view('technolog.updatemanu', ['days' => $days]);

    }

    public function getactivemenuproducts(Request $request)
    {
        $days = Day::where('days.id', '>=', $request->bid)
            ->where('days.id', '<=', $request->eid)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $titlemenu = Titlemenu::all();
        $foods = Food::all();
        $ages = Age_range::all();
        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->get(['products.id', 'products.product_name', 'sizes.size_name']);

        $menus = Active_menu::where('day_id', ">=", $request->bid)->where('day_id', "<=", $request->eid)->get();

        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>Yosh</th>
                        <th scope='col'>Menu</th>
                        <th scope='col'>Taom nomi</th>
                        <th scope='col'>Maxsulot</th>
                        <th scope='col'>Kg</th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($menus as $row) {
            $html = $html . "<tr>
                        <td>" . $ages->find($row->age_range_id)->age_name . "</td>
                        <td>" . $titlemenu->find($row->title_menu_id)->menu_name . "</td>
                        <td>" . $foods->find($row->menu_food_id)->food_name . "</td>
                        <td>" . $products->find($row->product_name_id)->product_name . "</td>
                        <td> <input type='text' name='weight[" . $row->id . "]' value='" . $row->weight . "'/></td>
                    </tr>";

        }
        $html = $html . "</tbody>
            </table>
            ";

        return $html;
    }

    public function weightcurrent(Request $request, $kind, $yearid = 0, $monthid)
    {
        // dd($request->all(), $kind, $yearid, $monthid);
        if ($yearid == 0) {
            $yearid = Year::where('year_active', 1)->first()->id;
        }
        $year = Year::where('id', $yearid)->first();
        $months = Month::where('yearid', $yearid)->get();
        $il = $monthid;
        if ($monthid == 0) {
            $il = Month::where('month_active', 1)->where('yearid', $yearid)->first()->id;
            if ($il == null) {
                $il = Month::where('yearid', $yearid)->first()->id;
            }
        }
        $monthdays = $this->activmonth($il);
        $days = $this->days();
        $products = Product::all();
        $kindergarden = Kindgarden::where('id', $kind)->first();
        $id = $il;
        $groups = Groupweight::where('kindergarden_id', $kind)
            ->where('day_id', '>=', $monthdays[0]->id)->where('day_id', '<=', $monthdays[count($monthdays) - 1]->id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('technolog.weightcurrent', compact('groups', 'months', 'id', 'days', 'products', 'year', 'monthdays', 'il', 'kindergarden'));
    }

    public function editegroup(Request $request)
    {

        Groupweight::where('id', $request->group_id)->update([
            'name' => $request->nametitle,
            'day_id' => $request->editedayid
        ]);

        foreach ($request->weights as $key => $value) {
            $isThere = Weightproduct::where('groupweight_id', $request->group_id)->where('product_id', $key)->first();
            if (isset($isThere)) {
                Weightproduct::where('groupweight_id', $request->group_id)->where('product_id', $key)->update([
                    'weight' => $value
                ]);
            }
            else {
                Weightproduct::create([
                    'groupweight_id' => $request->group_id,
                    'product_id' => $key,
                    'weight' => $value
                ]);
            }
        }

        return redirect()->route('technolog.weightcurrent', ['kind' => $request->kind_id, 'yearid' => $request->yearid, 'monthid' => $request->monthid]);
    }

    public function addingweights(Request $request)
    {
        // dd($request->all());
        $group = Groupweight::create([
            'name' => $request->title,
            'kindergarden_id' => $request->kindergarden_id,
            'day_id' => $request->day_id
        ]);

        foreach ($request->weights as $key => $value) {
            if ($value > 0) {
                Weightproduct::create([
                    'groupweight_id' => $group->id,
                    'product_id' => $key,
                    'weight' => $value,
                ]);
            }
        }

        return redirect()->route('technolog.weightcurrent', ['kind' => $request->kindergarden_id, 'yearid' => 0, 'monthid' => 0]);
    }

    public function getweightproducts(Request $request)
    {
        $products = Product::all();
        $prgroup = Weightproduct::where('groupweight_id', $request->group_id)->get();
        // dd($request->group_id);
        $html = "<table class='table table-light table-striped table-hover' style='width: calc(100% - 2rem)!important;'>
                <thead>
                    <tr>
                        <th scope='col'>ID</th>
                        <th scope='col'>Maxsulot</th>
                        <th scope='col'>Miqdori</th>
                    </tr>
                </thead>
                <tbody>";

        $i = 0;
        foreach ($products as $all) {
            $weightproduct = $prgroup->where('product_id', $all->id)->first();
            $weight = $weightproduct ? $weightproduct->weight : 0;

            $html = $html . "<tr>
                <td scope='row'>" . ++$i . "</td>
                <td>" . $all->product_name . "</td>";
            $html = $html . "<td style='width: 50px;'><input type='text' name='weights[" . $all->id . "]' value='" . $weight . "'></td>";
            $html = $html . "</tr>";
        }
        $html = $html . "</tbody>
                </table>";

        return $html;
    }

    public function editactivemanu(Request $request)
    {
        foreach ($request->weight as $key => $value) {
            Active_menu::where('id', $key)->update(['weight' => $value]);
        }

        return redirect()->route('technolog.seasons');
    }

    public function pageCreateProduct()
    {
        $categories = Product_category::all();
        $norms = Norm_category::all();
        $sizes = Size::all();
        return view('technolog.createproduct', compact('categories', 'norms', 'sizes'));
    }

    public function createproduct(Request $request)
    {
        Product::create([
            'product_name' => $request->product_name,
            'size_name_id' => $request->sizeid,
            'category_name_id' => $request->catid,
            'product_image' => "...",
            'norm_cat_id' => $request->normid,
            'div' => $request->div,
            'sort' => $request->sort,
            'hide' => $request->hide,
            'proteins' => $request->proteins ?? 0,
            'fats' => $request->fats ?? 0,
            'carbohydrates' => $request->carbohydrates ?? 0,
            'kcal' => $request->kcal ?? 0
        ]);

        return redirect()->route('technolog.allproducts');
    }

    function funtest()
    {
        return Kindgarden::all();
    }

    public function tabassum(Request $request, $start, $end)
    {
        $days = $this->rangeOfDays($start, $end);
        foreach ($days as $day) {
            if (Number_children::where('day_id', $day->id)->where('kingar_name_id', '=', 35)->count() == 0) {
                $v = Number_children::where('day_id', $day->id)->where('king_age_name_id', '=', 4)->first();

                Number_children::create([
                    'kingar_name_id' => 35,
                    'day_id' => $day->id,
                    'king_age_name_id' => 4,
                    'kingar_children_number' => 100,
                    'workers_count' => 0,
                    'kingar_menu_id' => $v->kingar_menu_id,
                ]);
            }
        }

        dd("ok");
    }

    //  /////////////////////////////////////////

    function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    // Bolalar qatnovi
    public function bolalarQatnovi(Request $request)
    {
        $year = Year::where('year_active', 1)->first();
        $months = Month::where('yearid', $year->id)->get();
        $days = $this->days();

        return view('technolog.bolalar_qatnovi', compact('year', 'months', 'days'));
    }

    public function getBolalarQatnoviData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $regionId = $request->input('region_id');

        // Kunlarni olish
        $selectedDays = Day::where('days.id', '>=', $startDate)
            ->where('days.id', '<=', $endDate)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->orderBy('days.id', 'ASC')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);

        // Bog'chalarni tuman bo'yicha guruhlash
        $kindgardens = Kindgarden::with('region')
            ->when($regionId, function ($query) use ($regionId) {
            return $query->where('region_id', $regionId);
        })
            ->orderBy('region_id')
            ->orderBy('number_of_org')
            ->get();

        // Yosh guruhlarini olish
        $ageRanges = Age_range::all();

        // Har bir tuman uchun ma'lumotlarni tayyorlash
        $regions = Region::all();
        $attendanceData = [];

        foreach ($regions as $region) {
            $regionKindgardens = $kindgardens->where('region_id', $region->id);

            if ($regionKindgardens->count() > 0) {
                $attendanceData[$region->id] = [
                    'region_name' => $region->region_name,
                    'kindgardens' => []
                ];

                $regionTotalChildren = []; // Har bir kun uchun tuman bo'yicha jami bolalar

                foreach ($regionKindgardens as $kindgarden) {
                    $kindgardenData = [
                        'id' => $kindgarden->id,
                        'name' => $kindgarden->kingar_name,
                        'number_of_org' => $kindgarden->number_of_org,
                        'days' => [], // Har bir kun uchun ma'lumotlar
                        'total' => 0,
                        'short_total' => 0,
                        'workers_total' => 0
                    ];

                    $kindgardenTotal = 0;
                    $kindgardenShortTotal = 0;
                    $kindgardenWorkersTotal = 0;

                    // Har bir kun uchun ma'lumotlarni olish
                    foreach ($selectedDays as $day) {
                        // 3-7 yosh bolalar soni (age_id = 4)
                        $childrenCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->whereIn('king_age_name_id', [4, 5]) // 3-7 yosh va boshqa mos yosh guruhlari
                            ->sum('kingar_children_number');

                        // Qisqa guruh bolalar soni (age_id = 5)
                        $shortGroupCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 3) // Qisqa guruh
                            ->sum('kingar_children_number');

                        // Xodimlar soni
                        $workersCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->whereIn('king_age_name_id', [4, 5]) // 3-7 yosh uchun xodimlar
                            ->sum('workers_count');

                        $kindgardenData['days'][$day->id] = [
                            'children_count' => $childrenCount,
                            'short_group_count' => $shortGroupCount,
                            'workers_count' => $workersCount
                        ];

                        // Jami hisoblash
                        $kindgardenTotal += $childrenCount;
                        $kindgardenShortTotal += $shortGroupCount;
                        $kindgardenWorkersTotal += $workersCount;
                    }

                    // Bog'cha bo'yicha jami qo'shish
                    $kindgardenData['total'] = $kindgardenTotal;
                    $kindgardenData['short_total'] = $kindgardenShortTotal;
                    $kindgardenData['workers_total'] = $kindgardenWorkersTotal;

                    $attendanceData[$region->id]['kindgardens'][] = $kindgardenData;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $attendanceData,
            'days' => $selectedDays
        ]);
    }

    // PDF ga yuklab olish
    public function downloadBolalarQatnoviPDF(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $regionId = $request->input('region_id');

        // Kunlarni olish
        $selectedDays = Day::where('days.id', '>=', $startDate)
            ->where('days.id', '<=', $endDate)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->orderBy('days.id', 'ASC')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);

        // Bog'chalarni tuman bo'yicha guruhlash
        $kindgardens = Kindgarden::with('region')
            ->when($regionId, function ($query) use ($regionId) {
            return $query->where('region_id', $regionId);
        })
            ->orderBy('region_id')
            ->orderBy('number_of_org')
            ->get();

        // Har bir tuman uchun ma'lumotlarni tayyorlash
        $regions = Region::all();
        $attendanceData = [];

        foreach ($regions as $region) {
            $regionKindgardens = $kindgardens->where('region_id', $region->id);

            if ($regionKindgardens->count() > 0) {
                $attendanceData[$region->id] = [
                    'region_name' => $region->region_name,
                    'kindgardens' => []
                ];

                foreach ($regionKindgardens as $kindgarden) {
                    $kindgardenData = [
                        'id' => $kindgarden->id,
                        'kingar_name' => $kindgarden->kingar_name,
                        'number_of_org' => $kindgarden->number_of_org,
                        'days' => [], // Har bir kun uchun ma'lumotlar
                        'total' => 0,
                        'short_total' => 0,
                        'workers_total' => 0
                    ];

                    $kindgardenTotal = 0;
                    $kindgardenShortTotal = 0;
                    $kindgardenWorkersTotal = 0;

                    // Har bir kun uchun ma'lumotlarni olish
                    foreach ($selectedDays as $day) {
                        // 3-7 yosh bolalar soni (age_id = 4)
                        $childrenCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 4) // 3-7 yosh
                            ->sum('kingar_children_number');

                        // Qisqa guruh bolalar soni (age_id = 5)
                        $shortGroupCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 3) // Qisqa guruh
                            ->sum('kingar_children_number');

                        // Xodimlar soni
                        $workersCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 4) // 3-7 yosh uchun xodimlar
                            ->sum('workers_count');

                        $kindgardenData['days'][$day->id] = [
                            'children_count' => $childrenCount,
                            'short_group_count' => $shortGroupCount,
                            'workers_count' => $workersCount
                        ];

                        // Jami hisoblash
                        $kindgardenTotal += $childrenCount;
                        $kindgardenShortTotal += $shortGroupCount;
                        $kindgardenWorkersTotal += $workersCount;
                    }

                    // Bog'cha bo'yicha jami qo'shish
                    $kindgardenData['total'] = $kindgardenTotal;
                    $kindgardenData['short_total'] = $kindgardenShortTotal;
                    $kindgardenData['workers_total'] = $kindgardenWorkersTotal;

                    $attendanceData[$region->id]['kindgardens'][] = $kindgardenData;
                }
            }
        }

        // PDF yaratish
        $pdf = \PDF::loadView('technolog.bolalar_qatnovi_pdf', [
            'attendanceData' => $attendanceData,
            'selectedDays' => $selectedDays
        ]);

        $fileName = 'bolalar_qatnovi_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($fileName);
    }


    // Excel ga yuklab olish
    public function downloadBolalarQatnoviExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $regionId = $request->input('region_id');

        // Kunlarni olish
        $selectedDays = Day::where('days.id', '>=', $startDate)
            ->where('days.id', '<=', $endDate)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->orderBy('days.id', 'ASC')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);

        // Bog'chalarni tuman bo'yicha guruhlash
        $kindgardens = Kindgarden::with('region')
            ->when($regionId, function ($query) use ($regionId) {
            return $query->where('region_id', $regionId);
        })
            ->orderBy('region_id')
            ->orderBy('number_of_org')
            ->get();

        // Har bir tuman uchun ma'lumotlarni tayyorlash
        $regions = Region::all();
        $attendanceData = [];

        foreach ($regions as $region) {
            $regionKindgardens = $kindgardens->where('region_id', $region->id)
                ->sortBy('number_of_org');

            if ($regionKindgardens->count() > 0) {
                $attendanceData[$region->id] = [
                    'region_name' => $region->region_name,
                    'kindgardens' => []
                ];

                foreach ($regionKindgardens as $kindgarden) {
                    $kindgardenData = [
                        'id' => $kindgarden->id,
                        'kingar_name' => $kindgarden->kingar_name,
                        'number_of_org' => $kindgarden->number_of_org,
                        'days' => [], // Har bir kun uchun ma'lumotlar
                        'total' => 0,
                        'short_total' => 0,
                        'workers_total' => 0
                    ];

                    $kindgardenTotal = 0;
                    $kindgardenShortTotal = 0;
                    $kindgardenWorkersTotal = 0;

                    // Har bir kun uchun ma'lumotlarni olish
                    foreach ($selectedDays as $day) {
                        // 3-7 yosh bolalar soni (age_id = 4)
                        $childrenCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 4) // 3-7 yosh
                            ->sum('kingar_children_number');

                        // Qisqa guruh bolalar soni (age_id = 5)
                        $shortGroupCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 3) // Qisqa guruh
                            ->sum('kingar_children_number');

                        // Xodimlar soni
                        $workersCount = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $kindgarden->id)
                            ->where('king_age_name_id', 4) // 3-7 yosh uchun xodimlar
                            ->sum('workers_count');

                        $kindgardenData['days'][$day->id] = [
                            'children_count' => $childrenCount,
                            'short_group_count' => $shortGroupCount,
                            'workers_count' => $workersCount
                        ];

                        // Jami hisoblash
                        $kindgardenTotal += $childrenCount;
                        $kindgardenShortTotal += $shortGroupCount;
                        $kindgardenWorkersTotal += $workersCount;
                    }

                    // Bog'cha bo'yicha jami qo'shish
                    $kindgardenData['total'] = $kindgardenTotal;
                    $kindgardenData['short_total'] = $kindgardenShortTotal;
                    $kindgardenData['workers_total'] = $kindgardenWorkersTotal;

                    $attendanceData[$region->id]['kindgardens'][] = $kindgardenData;
                }
            }
        }

        // Excel fayl yaratish
        $fileName = 'bolalar_qatnovi_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new class($attendanceData, $selectedDays) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithEvents {

            protected $data;
            protected $days;

            public function __construct($data, $days)
            {
                $this->data = $data;
                $this->days = $days;
            }

            public function array(): array
            {
                $result = [];

                // Sarlavha qatori
                $result[] = ['BOLALAR QATNOVI HISOBOTI'];
                $result[] = ['Vaqt oralig\'i: ' . $this->days->first()->day_number . '.' . $this->days->first()->month_name . '.' . $this->days->first()->year_name . ' - ' . $this->days->last()->day_number . '.' . $this->days->last()->month_name . '.' . $this->days->last()->year_name];
                $result[] = ['Hisobot sanasi: ' . date('d.m.Y H:i')];
                $result[] = []; // Bo'sh qator

                $firstRegion = true;

                foreach ($this->data as $regionId => $region) {
                    // Har bir tumanga o'tishdan oldin bo'sh qator (birinchi tuman bundan mustasno)
                    if (!$firstRegion) {
                        $result[] = []; // Bo'sh qator
                    }
                    $firstRegion = false;

                    // Tuman sarlavhasi
                    $result[] = [$region['region_name']];
                    $result[] = []; // Bo'sh qator

                    // Jadval sarlavhasi
                    $headerRow = ['TR', 'DMTT'];

                    // Har bir bog'cha uchun ustunlar
                    foreach ($region['kindgardens'] as $kindgarden) {
                        $orgNumber = $kindgarden['number_of_org'] ?: $kindgarden['kingar_name'];
                        $headerRow[] = $orgNumber;
                        $headerRow[] = '';
                        $headerRow[] = '';
                    }

                    // Jami ustuni
                    $headerRow[] = 'Jami';
                    $headerRow[] = '';
                    $headerRow[] = '';

                    $result[] = $headerRow;

                    // Ikkinchi sarlavha qatori
                    $subHeaderRow = ['', ''];
                    foreach ($region['kindgardens'] as $kindgarden) {
                        $subHeaderRow[] = '3-7 yosh';
                        $subHeaderRow[] = 'Qisqa guruh';
                        $subHeaderRow[] = 'Xodim';
                    }
                    $subHeaderRow[] = '3-7 yosh';
                    $subHeaderRow[] = 'Qisqa guruh';
                    $subHeaderRow[] = 'Xodim';

                    $result[] = $subHeaderRow;

                    // Har bir kun uchun ma'lumotlar
                    foreach ($this->days as $dayIndex => $day) {
                        $dataRow = [$dayIndex + 1, $day->day_number . '.' . $day->month_name . '.' . $day->year_name];

                        // Har bir bog'cha uchun ma'lumotlar
                        foreach ($region['kindgardens'] as $kindgarden) {
                            $dayData = $kindgarden['days'][$day->id] ?? null;

                            // Mavjud bo'lmagan guruhlarga 0 qiymat berish
                            $childrenCount = $dayData ? ($dayData['children_count'] ?? 0) : 0;
                            $shortGroupCount = $dayData ? ($dayData['short_group_count'] ?? 0) : 0;
                            $workersCount = $dayData ? ($dayData['workers_count'] ?? 0) : 0;

                            $dataRow[] = $childrenCount;
                            $dataRow[] = $shortGroupCount;
                            $dataRow[] = $workersCount;
                        }

                        // Kun bo'yicha jami
                        $dayTotal = 0;
                        $dayShortTotal = 0;
                        $dayWorkersTotal = 0;

                        foreach ($region['kindgardens'] as $kindgarden) {
                            $dayData = $kindgarden['days'][$day->id] ?? null;
                            if ($dayData) {
                                $dayTotal += $dayData['children_count'] ?? 0;
                                $dayShortTotal += $dayData['short_group_count'] ?? 0;
                                $dayWorkersTotal += $dayData['workers_count'] ?? 0;
                            }
                        }

                        $dataRow[] = $dayTotal;
                        $dataRow[] = $dayShortTotal;
                        $dataRow[] = $dayWorkersTotal;

                        $result[] = $dataRow;
                    }

                    // Jami qatori
                    $totalRow = ['', 'Jami'];

                    // Har bir bog'cha bo'yicha jami
                    foreach ($region['kindgardens'] as $kindgarden) {
                        $totalRow[] = $kindgarden['total'] ?? 0;
                        $totalRow[] = $kindgarden['short_total'] ?? 0;
                        $totalRow[] = $kindgarden['workers_total'] ?? 0;
                    }

                    // Tuman bo'yicha jami
                    $regionTotal = 0;
                    $regionShortTotal = 0;
                    $regionWorkersTotal = 0;

                    foreach ($region['kindgardens'] as $kindgarden) {
                        $regionTotal += $kindgarden['total'] ?? 0;
                        $regionShortTotal += $kindgarden['short_total'] ?? 0;
                        $regionWorkersTotal += $kindgarden['workers_total'] ?? 0;
                    }

                    $totalRow[] = $regionTotal;
                    $totalRow[] = $regionShortTotal;
                    $totalRow[] = $regionWorkersTotal;

                    $result[] = $totalRow;
                }

                return $result;
            }

            public function columnWidths(): array
            {
                $widths = [
                    'A' => 3, // TR
                    'B' => 10, // DMTT
                ];

                // Bog'cha va jami ustunlarini dinamik aniqlash
                $totalCols = 2 + (count($this->data[array_key_first($this->data)]['kindgardens']) * 3) + 3;

                for ($i = 3; $i <= $totalCols; $i++) {
                    $widths[$this->getColumnLetter($i)] = 5; // 3 xonali raqam sig'adi
                }

                return $widths;
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                $row = 1;

                // Sarlavha stillari
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2')->getFont()->setSize(12);
                $sheet->getStyle('A3')->getFont()->setSize(12);

                $row = 5; // Jadval boshlanishi

                foreach ($this->data as $regionId => $region) {
                    // Tuman sarlavhasi
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle("A{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
                    $row += 2;

                    // Jadval sarlavhasi - bog'cha nomlari qatori (5-qator)
                    $headerStartRow = 5; // Bog'cha nomlari qatori
                    $headerEndRow = 6; // Yosh guruhlari qatori
                    $colCount = 2 + (count($region['kindgardens']) * 3) + 3; // TR + DMTT + bog'chalar + jami

                    // Asosiy sarlavha - bog'cha nomlari uchun qora rang (5-qator)
                    $sheet->getStyle("A{$headerStartRow}:B{$headerStartRow}")
                        ->getFont()->setBold(true);
                    $sheet->getStyle("A{$headerStartRow}:B{$headerStartRow}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFFFF');
                    $sheet->getStyle("A{$headerStartRow}:B{$headerStartRow}")
                        ->getFont()->getColor()->setRGB('000000');

                    // Har bir ustun uchun
                    for ($col = 3; $col <= $colCount; $col++) {
                        $sheet->getStyle($this->getColumnLetter($col) . $headerEndRow)
                            ->getAlignment()->setTextRotation(90) // 90 gradus burish
                            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }

                    // Bog'cha nomlari uchun qora rang (colspan=3)
                    $startCol = 3; // C ustuni
                    foreach ($region['kindgardens'] as $index => $kindgarden) {
                        $endCol = $startCol + 2; // 3 ustun uchun
                        $sheet->getStyle("{$this->getColumnLetter($startCol)}{$headerStartRow}:{$this->getColumnLetter($endCol)}{$headerStartRow}")
                            ->getFont()->setBold(true);
                        $sheet->getStyle("{$this->getColumnLetter($startCol)}{$headerStartRow}:{$this->getColumnLetter($endCol)}{$headerStartRow}")
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('196189151');
                        $sheet->getStyle("{$this->getColumnLetter($startCol)}{$headerStartRow}:{$this->getColumnLetter($endCol)}{$headerStartRow}")
                            ->getFont()->getColor()->setRGB('000000');
                        $startCol += 3;
                    }

                    // Jami ustuni uchun qora rang
                    $jamiStartCol = $colCount - 2;
                    $jamiEndCol = $colCount;
                    $sheet->getStyle("{$this->getColumnLetter($jamiStartCol)}{$headerStartRow}:{$this->getColumnLetter($jamiEndCol)}{$headerStartRow}")
                        ->getFont()->setBold(true);
                    $sheet->getStyle("{$this->getColumnLetter($jamiStartCol)}{$headerStartRow}:{$this->getColumnLetter($jamiEndCol)}{$headerStartRow}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('333333');
                    $sheet->getStyle("{$this->getColumnLetter($jamiStartCol)}{$headerStartRow}:{$this->getColumnLetter($jamiEndCol)}{$headerStartRow}")
                        ->getFont()->getColor()->setRGB('FFFFFF');

                    // Ikkinchi sarlavha - oq rang (yosh guruhlari uchun) - 6-qator
                    $sheet->getStyle("A{$headerEndRow}:{$this->getColumnLetter($colCount)}{$headerEndRow}")
                        ->getFont()->setBold(true);
                    $sheet->getStyle("A{$headerEndRow}:{$this->getColumnLetter($colCount)}{$headerEndRow}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFFFF');
                    $sheet->getStyle("A{$headerEndRow}:{$this->getColumnLetter($colCount)}{$headerEndRow}")
                        ->getFont()->getColor()->setRGB('000000');

                    $row += 2;

                    // Ma'lumotlar qatorlari
                    $dataStartRow = $row;
                    $dataEndRow = $row + count($this->days) - 1;

                    // Ma'lumotlar qatorlari stillari
                    for ($i = $dataStartRow; $i <= $dataEndRow; $i++) {
                        if ($i % 2 == 0) {
                            $sheet->getStyle("A{$i}:{$this->getColumnLetter($colCount)}{$i}")
                                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F8F9FA');
                        }
                    }

                    $row = $dataEndRow + 1;

                    // Jami qatori
                    $sheet->getStyle("A{$row}:{$this->getColumnLetter($colCount)}{$row}")
                        ->getFont()->setBold(true);
                    $sheet->getStyle("A{$row}:{$this->getColumnLetter($colCount)}{$row}")
                        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('E3F2FD');

                    $row += 3; // Bo'sh qatorlar uchun
                }

                // Barcha kataklar uchun chegaralar
                $sheet->getStyle('A1:' . $this->getColumnLetter($colCount) . $row)
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }

            public function registerEvents(): array
            {
                return [
                    \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                    $sheet = $event->sheet->getDelegate();
                    $row = 5;

                    foreach ($this->data as $regionId => $region) {
                        // $row += 2; // Tuman sarlavhasi va bo'sh qator

                        // Merge cells for main headers
                        $colCount = 2 + (count($region['kindgardens']) * 3) + 3;
                        $headerStartCol = 3; // C ustuni

                        // Har bir bog'cha uchun merge qilish
                        foreach ($region['kindgardens'] as $index => $kindgarden) {
                            $startCol = $headerStartCol + ($index * 3);
                            $endCol = $startCol + 2;

                            $sheet->mergeCells($this->getColumnLetter($startCol) . $row . ':' . $this->getColumnLetter($endCol) . $row);
                        }

                        // Jami ustuni uchun merge
                        $jamiStartCol = $headerStartCol + (count($region['kindgardens']) * 3);
                        $jamiEndCol = $jamiStartCol + 2;
                        $sheet->mergeCells($this->getColumnLetter($jamiStartCol) . $row . ':' . $this->getColumnLetter($jamiEndCol) . $row);

                        $row += 2 + count($this->days) + 1 + 2; // Sarlavhalar + ma'lumotlar + jami + bo'sh qatorlar
                    }
                }
                ];
            }

            private function getColumnLetter($columnNumber)
            {
                $columnLetter = '';
                while ($columnNumber > 0) {
                    $columnNumber--;
                    $columnLetter = chr(65 + ($columnNumber % 26)) . $columnLetter;
                    $columnNumber = intval($columnNumber / 26);
                }
                return $columnLetter;
            }
        }, $fileName);
    }

    // Muassasalar (Bog'chalar) boshqaruvi
    public function muassasalar(Request $request)
    {
        $kindgardens = Kindgarden::with('age_range')->orderBy('id', 'DESC')->get();
        $regions = Region::all();
        return view('technolog.muassasalar', compact('kindgardens', 'regions'));
    }

    public function addmuassasa(Request $request)
    {
        $regions = Region::all();
        $ages = Age_range::all();
        return view('technolog.addmuassasa', compact('regions', 'ages'));
    }

    public function createmuassasa(Request $request)
    {
        // Validation
        $request->validate(Kindgarden::rules());

        $kindgarden = Kindgarden::create([
            'region_id' => $request->region_id,
            'kingar_name' => $request->kingar_name,
            'short_name' => $request->short_name,
            'number_of_org' => $request->number_of_org,
            'kingar_password' => $request->kingar_password ?? "123456",
            'telegram_user_id' => 0,
            'worker_count' => $request->worker_count,
            'worker_age_id' => $request->worker_age_id ?? 1,
            'hide' => $request->hide ?? 1
        ]);

        // Yosh guruhlari bog'lash
        if ($request->yongchek) {
            $kindgarden->age_range()->sync($request->yongchek);
        }

        return redirect()->route('technolog.muassasalar')->with('status', 'Yangi muassasa muvaffaqiyatli qo\'shildi!');
    }

    public function editmuassasa(Request $request, $id)
    {
        $kindgarden = Kindgarden::with('age_range')->where('id', $id)->first();
        $regions = Region::all();
        $ages = Age_range::all();
        return view('technolog.editmuassasa', compact('kindgarden', 'regions', 'ages'));
    }

    public function updatemuassasa(Request $request)
    {
        // Validation (edit holatida ID ni o'tkazamiz)
        $request->validate(Kindgarden::rules($request->kindgarden_id));

        $kindgarden = Kindgarden::find($request->kindgarden_id);

        $kindgarden->update([
            'region_id' => $request->region_id,
            'kingar_name' => $request->kingar_name,
            'short_name' => $request->short_name,
            'number_of_org' => $request->number_of_org,
            'kingar_password' => $request->kingar_password ?? "123456",
            'worker_count' => $request->worker_count,
            'worker_age_id' => $request->worker_age_id ?? 1,
            'hide' => $request->hide ?? 1
        ]);

        // Yosh guruhlari yangilash
        if ($request->yongchek) {
            $kindgarden->age_range()->sync($request->yongchek);
        }

        return redirect()->route('technolog.muassasalar')->with('status', 'Muassasa ma\'lumotlari muvaffaqiyatli yangilandi!');
    }

    public function deletemuassasa(Request $request)
    {
        $kindgarden = Kindgarden::find($request->id);
        if ($kindgarden) {
            // Bog'langan ma'lumotlarni o'chirish
            $kindgarden->age_range()->detach();
            $kindgarden->delete();
        }

        return response()->json(['success' => true]);
    }

    // Mahsulotlarni sarflash uchun modal ma'lumotlarini olish
    public function getProductsForExpense($dayid, $kingardenid)
    {
        $join = Number_children::where('number_childrens.day_id', $dayid)
            ->where('kingar_name_id', $kingardenid)
            ->leftjoin('active_menus', function ($join) {
            // $join->on('day_id', '=', $today);
            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
        })
            ->where('active_menus.day_id', $dayid)
            ->join('products', 'active_menus.product_name_id', '=', 'products.id')
            ->get();
        // dd($join);	
        $ages = Age_range::all();
        $agerange = array();
        foreach ($ages as $row) {
            $agerange[$row->id] = 0;
        }
        $productscount = array_fill(1, 500, $agerange);
        $workproduct = array_fill(1, 500, 0);
        $workerfood = titlemenu_food::where('titlemenu_foods.day_id', ($dayid - 1))->get();
        // dd($workerfood);
        foreach ($join as $row) {
            if ($row->age_range_id == 4 and $row->menu_meal_time_id = 3) {
                foreach ($workerfood as $ww) {
                    if ($row->menu_food_id == $ww->food_id) {
                        $workproduct[$row->product_name_id] += $row->weight;
                        $workproduct[$row->product_name_id . 'div'] = $row->div;
                        $workproduct[$row->product_name_id . 'wcount'] = $row->workers_count;
                    }
                }
            }
            $productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
            $productscount[$row->product_name_id][$row->age_range_id . '-children'] = $row->kingar_children_number;
            $productscount[$row->product_name_id][$row->age_range_id . 'div'] = $row->div;
            $productscount[$row->product_name_id]['product_name'] = $row->product_name;
        }

        $bool = minus_multi_storage::where('day_id', $dayid)->where('kingarden_name_id', $kingardenid)->get();

        $products = [];

        if ($bool->count() == 0) {
            foreach ($productscount as $key => $row) {
                if (isset($row['product_name'])) {
                    $summ = 0;
                    foreach ($ages as $age) {
                        if (isset($row[$age['id'] . '-children'])) {
                            $summ += ($row[$age['id']] * $row[$age['id'] . '-children']) / $row[$age['id'] . 'div'];
                        }
                    }
                    if (isset($workproduct[$key . 'wcount'])) {
                        $summ += ($workproduct[$key] * $workproduct[$key . 'wcount']) / $workproduct[$key . 'div'];
                    }
                    $products[] = [
                        'id' => $key,
                        'product_name' => $row['product_name'],
                        'product_weight' => $summ,
                    ];
                }
            }
        }

        $kindgarden = Kindgarden::where('id', $kingardenid)->first();

        return response()->json([
            'products' => $products,
            'kindgarden' => $kindgarden,
            'day_id' => $dayid
        ]);
    }

    // Mahsulotlarni sarflash
    public function saveProductExpense(Request $request)
    {
        // Ma'lumotlarni validatsiya qilish
        $validated = $request->validate([
            'day_id' => 'required|integer',
            'kingarden_id' => 'required|integer',
            'products' => 'required|array',
            'products.*' => 'numeric|min:0'
        ]);

        foreach ($request->products as $product_id => $weight) {
            if ($weight > 0) {
                // Mavjud yozuvni tekshirish
                $existing = minus_multi_storage::where('day_id', $request->day_id)
                    ->where('kingarden_name_id', $request->kingarden_id)
                    ->where('product_name_id', $product_id)
                    ->where('kingar_menu_id', -1)
                    ->first();

                if ($existing) {
                    // Mavjud bo'lsa, yangilash
                    $existing->update([
                        'product_weight' => $weight
                    ]);
                }
                else {
                    // Yangi yozuv yaratish
                    minus_multi_storage::create([
                        'day_id' => $request->day_id,
                        'kingarden_name_id' => $request->kingarden_id,
                        'kingar_menu_id' => -1,
                        'product_name_id' => $product_id,
                        'product_weight' => $weight
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Mahsulotlar muvaffaqiyatli sarflandi!']);
    }

    // ... existing code ...

    // Taxminiy menyular uchun alohida PDF fayllarini yaratish va ZIP arxiv qilish
    public function downloadAllKindergartensMenusPDF(Request $request)
    {
        try {
            // Region ID ni tekshirish
            $regionId = $request->input('region_id');

            if (!$regionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Iltimos, hududni tanlang!'
                ], 400);
            }

            // Tanlangan region bo'yicha bog'chalarni olish
            $kindergartens = Kindgarden::where('region_id', $regionId)
                ->with('age_range')
                ->get();

            if ($kindergartens->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanlangan hududda hech qanday bog\'cha topilmadi!'
                ], 404);
            }

            // Vaqtinchalik papka yaratish
            $tempDir = storage_path('app/temp_menus_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];
            $createdCount = 0;

            foreach ($kindergartens as $kindergarten) {
                // Har bir bog'cha uchun PDF yaratish
                foreach ($kindergarten->age_range as $age) {
                    $pdfPath = $this->createKindergartenMenuPDF($kindergarten->id, $age->id, $tempDir);
                    if ($pdfPath && file_exists($pdfPath)) {
                        $pdfFiles[] = $pdfPath;
                        $createdCount++;
                    }
                }
            }

            if (empty($pdfFiles)) {
                // Vaqtinchalik papkani tozalash
                $this->deleteDirectory($tempDir);
                return response()->json([
                    'success' => false,
                    'message' => 'Hech qanday PDF fayl yaratilmadi!'
                ], 404);
            }

            // ZIP fayl yaratish
            $regionName = \App\Models\Region::find($regionId)->region_name ?? 'region_' . $regionId;
            $zipFileName = 'barcha_menyular_' . $regionName . '_' . date('Y-m-d\TH-i-s') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            // Eski ZIP faylni o'chirish
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            $zip = new \ZipArchive();
            $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            if ($result === TRUE) {
                // Har bir PDF faylni ZIP ga qo'shish
                foreach ($pdfFiles as $pdfFile) {
                    if (file_exists($pdfFile)) {
                        $fileName = basename($pdfFile);
                        // Faylni ZIP ga qo'shish
                        if (!$zip->addFile($pdfFile, $fileName)) {
                            \Log::error("PDF fayl qo'shilmadi: " . $pdfFile);
                        }
                    }
                }

                // ZIP faylni to'g'ri yopish
                $zip->close();

                // ZIP fayl mavjudligini va hajmini tekshirish
                if (!file_exists($zipPath) || filesize($zipPath) == 0) {
                    $this->deleteDirectory($tempDir);
                    return response()->json([
                        'success' => false,
                        'message' => 'ZIP fayl yaratilmadi yoki bo\'sh!'
                    ], 500);
                }

                // Vaqtinchalik papkani tozalash
                $this->deleteDirectory($tempDir);

                // ZIP faylni yuklab olish
                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend();

            }
            else {
                // Vaqtinchalik papkani tozalash
                $this->deleteDirectory($tempDir);
                return response()->json([
                    'success' => false,
                    'message' => 'ZIP fayl yaratishda xatolik yuz berdi! Xatolik kodi: ' . $result
                ], 500);
            }

        }
        catch (\Exception $e) {
            \Log::error('ZIP yaratishda xatolik: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Har bir bog'cha uchun alohida PDF yaratish
    // ... existing code ...

    // Har bir bog'cha uchun alohida PDF yaratish
    public function createKindergartenMenuPDF($garden_id, $age_id, $tempDir)
    {
        try {
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $garden_id],
                ['king_age_name_id', '=', $age_id]
            ])
                ->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();

            // Menu mavjudligini tekshirish
            if ($menu->isEmpty()) {
                \Log::info("Menu topilmadi: garden_id={$garden_id}, age_id={$age_id}");
                return null;
            }

            // Menu ma'lumotlarini olish
            $menuData = $menu->first();
            if (!$menuData || !isset($menuData->kingar_menu_id)) {
                \Log::info("Menu ma'lumotlari to'liq emas: garden_id={$garden_id}, age_id={$age_id}");
                return null;
            }

            $taomnoma = Titlemenu::where('id', $menuData->kingar_menu_id)->first();

            $products = Product::where('hide', 1)
                ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('sort', 'ASC')->get(['products.*', 'sizes.size_name']);

            $menuitem = Menu_composition::where('title_menu_id', $menuData->kingar_menu_id)
                ->where('age_range_id', $age_id)
                ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('menu_meal_time_id')
                ->get();

            // xodimlar ovqati uchun
            $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')->first(['days.day_number', 'days.id as id', 'months.month_name', 'years.year_name']);

            $workerfood = titlemenu_food::where('day_id', $day->id)
                ->where('worker_age_id', $age_id)
                ->where('titlemenu_id', $menuData->kingar_menu_id)
                ->get();

            $costs = bycosts::where('day_id', bycosts::where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->get();
            $narx = [];
            foreach ($costs as $row) {
                if (!isset($narx[$row->praduct_name_id])) {
                    $narx[$row->praduct_name_id] = $row->price_cost;
                }
            }
            $nextdaymenuitem = [];
            $workerproducts = [];
            // kamchilik bor boshlangich qiymat berishda
            $productallcount = array_fill(1, 500, 0);

            foreach ($menuitem as $item) {
                $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
                $productallcount[$item->product_name_id] += $item->weight;
                for ($i = 0; $i < count($products); $i++) {
                    if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                        $products[$i]['yes'] = 1;
                    }
                }
            }

            // Xodimlar uchun ovqat gramajlarini hisoblash
            $workerproducts = array_fill(1, 500, 0);
            foreach ($workerfood as $tr) {
                // Tushlikdagi birinchi ovqat va nondan yeyishadi
                if (isset($nextdaymenuitem[3][$tr->food_id])) {
                    foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                        if ($key != 'foodname' and $key != 'foodweight') {
                            $workerproducts[$key] += $value;
                        }
                    }
                }
            }

            $today = new \DateTime();
            $nextWorkDay = clone $today;
            $nextWorkDay->modify('+1 day');

            // dam olish kunlari: 6 = shanba, 7 = yakshanba
            while (in_array($nextWorkDay->format('N'), [6, 7])) {
                $nextWorkDay->modify('+1 day');
            }

            $day->day_number = $nextWorkDay->format('d');
            $day->month_name = $nextWorkDay->format('F');
            $day->year_name = $nextWorkDay->format('Y');

            // Snappy bilan PDF yaratish
            $pdf = \PDF::loadView('pdffile.technolog.alltable', [
                'productallcount' => $productallcount,
                'workerproducts' => $workerproducts,
                'menu' => $menu,
                'menuitem' => $nextdaymenuitem,
                'products' => $products,
                'workerfood' => $workerfood,
                'taomnoma' => $taomnoma,
                'narx' => $narx,
                'day' => $day
            ]);

            // PDF sozlamalari
            $pdf->setPaper('a4', 'landscape')
                ->setOptions([
                'encoding' => 'UTF-8',
                'enable-javascript' => true,
                'javascript-delay' => 1000,
                'enable-smart-shrinking' => true,
                'no-stop-slow-scripts' => true,
                'disable-smart-shrinking' => false,
                'print-media-type' => true,
                'dpi' => 300,
                'image-quality' => 100,
                'margin-top' => 10,
                'margin-right' => 10,
                'margin-bottom' => 10,
                'margin-left' => 10,
                'enable-local-file-access' => true,
                'load-error-handling' => 'ignore',
                'load-media-error-handling' => 'ignore',
                'load-error-handling' => 'ignore',
            ]);

            $age_id = Age_range::where('id', $age_id)->first()->id;
            $age_name = '';
            if ($age_id == 3) {
                $age_name = 'Qisqa_guruh';
            }
            elseif ($age_id == 4) {
                $age_name = '3-7_yosh';
            }


            $fileName = $this->cleanFileName(Kindgarden::where('id', $garden_id)->first()->kingar_name) . 'DMTT_' . $age_name . '_Taxminiy_' . $day->day_number . '.' . $day->month_name . '.' . $day->year_name . '.pdf';

            $pdfPath = $tempDir . '/' . $fileName;


            // PDF faylni saqlash
            $pdf->save($pdfPath);

            // Bitta PDF fayl qaytarish
            return $pdfPath;

        }
        catch (\Exception $e) {
            \Log::error('PDF yaratishda xatolik: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    // ... existing code ...

    // ... existing code ...

    // Taxminiy menyular uchun alohida PDF fayllarini yaratish va ZIP arxiv qilish
    public function downloadAllKindergartensMenusPDF2(Request $request)
    {
        try {
            // Barcha bog'chalarni olish
            $kindergartens = Kindgarden::where('hide', 1)->with('age_range')->get();

            // Vaqtinchalik papka yaratish
            $tempDir = storage_path('app/temp_menus_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];

            foreach ($kindergartens as $kindergarten) {
                // Har bir bog'cha uchun PDF yaratish
                foreach ($kindergarten->age_range as $age) {
                    $pdfPath = $this->createKindergartenMenuPDF($kindergarten, $age, $tempDir);
                    if ($pdfPath && file_exists($pdfPath)) {
                        $pdfFiles[] = $pdfPath;
                    }
                }
            }

            if (empty($pdfFiles)) {
                // Vaqtinchalik papkani tozalash
                $this->deleteDirectory($tempDir);
                return redirect()->back()->with('error', 'Hech qanday PDF fayl yaratilmadi!');
            }

            // ZIP fayl yaratish
            $zipFileName = 'Taxminiy_menular_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            $zip = new \ZipArchive();
            $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            if ($result === TRUE) {
                foreach ($pdfFiles as $pdfFile) {
                    if (file_exists($pdfFile)) {
                        $fileName = basename($pdfFile);
                        $zip->addFile($pdfFile, $fileName);
                    }
                }

                // ZIP faylni to'g'ri yopish
                $zip->close();

                // ZIP fayl mavjudligini tekshirish
                if (!file_exists($zipPath)) {
                    $this->deleteDirectory($tempDir);
                    return redirect()->back()->with('error', 'ZIP fayl yaratilmadi!');
                }

                // Vaqtinchalik papkani tozalash
                // $this->deleteDirectory($tempDir);

                // ZIP faylni yuklab olish
                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend();
            }
            else {
                // Vaqtinchalik papkani tozalash
                $this->deleteDirectory($tempDir);
                return redirect()->back()->with('error', 'ZIP fayl yaratishda xatolik yuz berdi! Xatolik kodi: ' . $result);
            }

        }
        catch (\Exception $e) {
            \Log::error('ZIP yaratishda xatolik: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    // Har bir bog'cha uchun alohida PDF yaratish
    public function createKindergartenMenuPDFAction($garden_id, $age_id, $tempDir)
    {
        try {
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $garden_id],
                ['king_age_name_id', '=', $age_id]
            ])
                ->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
            $taomnoma = Titlemenu::where('id', $menu[0]['kingar_menu_id'])->first();

            $products = Product::where('hide', 1)
                ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('sort', 'ASC')->get(['products.*', 'sizes.size_name']);

            $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                ->where('age_range_id', $age_id)
                ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('menu_meal_time_id')
                ->get();

            // dd($menuitem);
            // xodimlar ovqati uchun
            $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')->first(['days.day_number', 'days.id as id', 'months.month_name', 'years.year_name']);
            // dd($day);
            $workerfood = titlemenu_food::where('day_id', $day->id)
                ->where('worker_age_id', $age_id)
                ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                ->get();
            // dd($workerfood);
            $costs = bycosts::where('day_id', bycosts::where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->get();
            $narx = [];
            foreach ($costs as $row) {
                if (!isset($narx[$row->praduct_name_id])) {
                    $narx[$row->praduct_name_id] = $row->price_cost;
                }
            }
            $nextdaymenuitem = [];
            $workerproducts = [];
            // kamchilik bor boshlangich qiymat berishda
            $productallcount = array_fill(1, 500, 0);
            // dd($menuitem);
            foreach ($menuitem as $item) {
                $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
                $productallcount[$item->product_name_id] += $item->weight;
                for ($i = 0; $i < count($products); $i++) {
                    if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                        $products[$i]['yes'] = 1;
                    }
                }
            }

            // Xodimlar uchun ovqat gramajlarini hisoblash
            $workerproducts = array_fill(1, 500, 0);
            foreach ($workerfood as $tr) {
                // Tushlikdagi birinchi ovqat va nondan yeyishadi
                if (isset($nextdaymenuitem[3][$tr->food_id])) {
                    foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                        if ($key != 'foodname' and $key != 'foodweight') {
                            $workerproducts[$key] += $value;
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        // $productallcount[$key] += $value;
                        }
                    }
                }
            }

            $today = new \DateTime();
            $nextWorkDay = clone $today;
            $nextWorkDay->modify('+1 day');

            // dam olish kunlari: 6 = shanba, 7 = yakshanba
            while (in_array($nextWorkDay->format('N'), [6, 7])) {
                $nextWorkDay->modify('+1 day');
            }

            $day->day_number = $nextWorkDay->format('d');
            $day->month_name = $nextWorkDay->format('F');
            $day->year_name = $nextWorkDay->format('Y');

            // PDF yaratish
            $dompdf = new Dompdf('UTF-8');

            $html = mb_convert_encoding(view('pdffile.technolog.alltable', [
                'productallcount' => $productallcount,
                'workerproducts' => $workerproducts,
                'menu' => $menu,
                'menuitem' => $nextdaymenuitem,
                'products' => $products,
                'workerfood' => $workerfood,
                'taomnoma' => $taomnoma,
                'narx' => $narx,
                'day' => $day
            ]), 'HTML-ENTITIES', 'UTF-8');

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Fayl nomini yaratish (maxsus belgilarni tozalash)
            $fileName = $this->cleanFileName(Kindgarden::where('id', $garden_id)->first()->kingar_name) . '_' . Age_range::where('id', $age_id)->first()->age_name . '_' . date('Y-m-d') . '.pdf';

            $pdfPath = $tempDir . '/' . $fileName;
            file_put_contents($pdfPath, $dompdf->output());

            // Bitta PDF fayl qaytarish
            return $pdfPath;

        }
        catch (\Exception $e) {
            \Log::error('PDF yaratishda xatolik: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    // Fayl nomini tozalash
    private function cleanFileName($fileName)
    {
        // Maxsus belgilarni tozalash
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fileName);
        $fileName = preg_replace('/_+/', '_', $fileName);
        $fileName = trim($fileName, '_');

        return $fileName ?: 'unnamed';
    }

    // Papkani o'chirish
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            }
            else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public function shareMenuTelegram(Request $request, $garden_id, $age_id)
    {
        try {
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $garden_id],
                ['king_age_name_id', '=', $age_id]
            ])
                ->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
            $taomnoma = Titlemenu::where('id', $menu[0]['kingar_menu_id'])->first();

            $products = Product::where('hide', 1)
                ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('sort', 'ASC')->get(['products.*', 'sizes.size_name']);

            $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                ->where('age_range_id', $age_id)
                ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('menu_meal_time_id')
                ->get();

            // dd($menuitem);
            // xodimlar ovqati uchun
            $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')->first(['days.day_number', 'days.id as id', 'months.month_name', 'years.year_name']);
            // dd($day);
            $workerfood = titlemenu_food::where('day_id', $day->id)
                ->where('worker_age_id', $age_id)
                ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                ->get();
            // dd($workerfood);
            $costs = bycosts::where('day_id', bycosts::where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->get();
            $narx = [];
            foreach ($costs as $row) {
                if (!isset($narx[$row->praduct_name_id])) {
                    $narx[$row->praduct_name_id] = $row->price_cost;
                }
            }
            $nextdaymenuitem = [];
            $workerproducts = [];
            // kamchilik bor boshlangich qiymat berishda
            $productallcount = array_fill(1, 500, 0);
            // dd($menuitem);
            foreach ($menuitem as $item) {
                $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
                $productallcount[$item->product_name_id] += $item->weight;
                for ($i = 0; $i < count($products); $i++) {
                    if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                        $products[$i]['yes'] = 1;
                    }
                }
            }

            // Xodimlar uchun ovqat gramajlarini hisoblash
            $workerproducts = array_fill(1, 500, 0);
            foreach ($workerfood as $tr) {
                // Tushlikdagi birinchi ovqat va nondan yeyishadi
                if (isset($nextdaymenuitem[3][$tr->food_id])) {
                    foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                        if ($key != 'foodname' and $key != 'foodweight') {
                            $workerproducts[$key] += $value;
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        // $productallcount[$key] += $value;
                        }
                    }
                }
            }

            $day->day_number = $day->day_number + 1;
            // oy va yilni o'zgartirish
            if ($day->day_number > date('d', strtotime('+1 day'))) {
                $day->month_name = date('F', strtotime('+1 month'));
                $day->year_name = date('Y', strtotime('+1 year'));
            }
            // PDF yaratish
            $dompdf = new Dompdf('UTF-8');
            $html = mb_convert_encoding(view('pdffile.technolog.alltable', [
                'productallcount' => $productallcount,
                'workerproducts' => $workerproducts,
                'menu' => $menu,
                'menuitem' => $nextdaymenuitem,
                'products' => $products,
                'workerfood' => $workerfood,
                'taomnoma' => $taomnoma,
                'narx' => $narx,
                'day' => $day
            ]), 'HTML-ENTITIES', 'UTF-8');

            // dd($html);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // PDF ni vaqtinchalik faylga saqlash
            $pdfPath = storage_path('app/public/temp/menu_' . uniqid() . '.pdf');
            if (!file_exists(dirname($pdfPath))) {
                @mkdir(dirname($pdfPath), 0755, true);
            }
            file_put_contents($pdfPath, $dompdf->output());
            // PDF ni JPG ga o'tkazish
            $imagick = new \Imagick();
            $imagick->readImage($pdfPath);
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality(90);

            // JPG ni saqlash
            $jpgPath = storage_path('app/public/temp/menu_' . uniqid() . '.jpg');
            $imagick->writeImage($jpgPath);
            // dd($jpgPath);
            // Telegramga yuborish
            $telegram = new \Telegram\Bot\Api(config('services.telegram.bot_token'));

            $caption = $menu[0]['kingar_name'] . " - " . $menu[0]['age_name'] . " yosh guruhi uchun menyu";

            // Guruh ID GET parametridan yoki configdan olinadi, bo'lmasa bog'cha telegram_user_id ishlatiladi
            $groupId = request()->get('group_id', config('services.telegram.group_id'));
            $chatId = $groupId ?: Kindgarden::where('id', $garden_id)->value('telegram_user_id');

            if (empty($chatId)) {
                // Fayllarni tozalashdan oldin xatoni qaytaramiz
                @unlink($pdfPath);
                @unlink($jpgPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram chat_id topilmadi. Iltimos group_id ni sozlamalarda belgilang.'
                ], 422);
            }

            $telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => fopen($jpgPath, 'r'),
                'caption' => $caption
            ]);

            // Vaqtinchalik fayllarni o'chirish
            @unlink($pdfPath);
            @unlink($jpgPath);

            return response()->json([
                'success' => true,
                'message' => 'Menyu telegramga muvaffaqiyatli yuborildi'
            ]);

        }
        catch (\Exception $e) {
            \Log::error('Telegram share error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createSharePdf(Request $request, $garden_id, $age_id)
    {
        try {
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $garden_id],
                ['king_age_name_id', '=', $age_id]
            ])
                ->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
            $taomnoma = Titlemenu::where('id', $menu[0]['kingar_menu_id'])->first();

            $products = Product::where('hide', 1)
                ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('sort', 'ASC')->get(['products.*', 'sizes.size_name']);

            $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                ->where('age_range_id', $age_id)
                ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('menu_meal_time_id')
                ->get();

            // dd($menuitem);
            // xodimlar ovqati uchun
            $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')->first(['days.day_number', 'days.id as id', 'months.month_name', 'years.year_name']);
            // dd($day);
            $workerfood = titlemenu_food::where('day_id', $day->id)
                ->where('worker_age_id', $age_id)
                ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                ->get();
            // dd($workerfood);
            $costs = bycosts::where('day_id', bycosts::where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $garden_id)->first()->region_id)->orderBy('day_id', 'DESC')->get();
            $narx = [];
            foreach ($costs as $row) {
                if (!isset($narx[$row->praduct_name_id])) {
                    $narx[$row->praduct_name_id] = $row->price_cost;
                }
            }
            $nextdaymenuitem = [];
            $workerproducts = [];
            // kamchilik bor boshlangich qiymat berishda
            $productallcount = array_fill(1, 500, 0);
            // dd($menuitem);
            foreach ($menuitem as $item) {
                $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
                $productallcount[$item->product_name_id] += $item->weight;
                for ($i = 0; $i < count($products); $i++) {
                    if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                        $products[$i]['yes'] = 1;
                    }
                }
            }

            // Xodimlar uchun ovqat gramajlarini hisoblash
            $workerproducts = array_fill(1, 500, 0);
            foreach ($workerfood as $tr) {
                // Tushlikdagi birinchi ovqat va nondan yeyishadi
                if (isset($nextdaymenuitem[3][$tr->food_id])) {
                    foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                        if ($key != 'foodname' and $key != 'foodweight') {
                            $workerproducts[$key] += $value;
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        // $productallcount[$key] += $value;
                        }
                    }
                }
            }

            $day->day_number = $day->day_number + 1;
            // oy va yilni o'zgartirish
            if ($day->day_number > date('d', strtotime('+1 day'))) {
                $day->month_name = date('F', strtotime('+1 month'));
                $day->year_name = date('Y', strtotime('+1 year'));
            }
            // PDF yaratish
            $dompdf = new Dompdf('UTF-8');
            $html = mb_convert_encoding(view('pdffile.technolog.alltable', [
                'productallcount' => $productallcount,
                'workerproducts' => $workerproducts,
                'menu' => $menu,
                'menuitem' => $nextdaymenuitem,
                'products' => $products,
                'workerfood' => $workerfood,
                'taomnoma' => $taomnoma,
                'narx' => $narx,
                'day' => $day
            ]), 'HTML-ENTITIES', 'UTF-8');

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // PDF ni vaqtinchalik faylga saqlash
            $fileName = 'menu_' . uniqid() . '.pdf';
            $filePath = storage_path('app/public/temp/' . $fileName);
            file_put_contents($filePath, $dompdf->output());

            // Fayl URL ni qaytarish
            return response()->json([
                'success' => true,
                'file_url' => '/storage/temp/' . $fileName
            ]);

        }
        catch (\Exception $e) {
            \Log::error('PDF yaratishda xatolik: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTempFile(Request $request)
    {
        try {
            $filePath = storage_path('app/public' . str_replace('/storage', '', $request->file_path));
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return response()->json(['success' => true]);
        }
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function testZipCreation()
    {
        try {
            // Test PDF fayllar yaratish
            $tempDir = storage_path('app/test_zip_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Bir nechta test PDF fayl yaratish
            $pdfFiles = [];
            $kindergartens = Kindgarden::where('region_id', 1)->with('age_range')->limit(2)->get();

            foreach ($kindergartens as $kindergarten) {
                foreach ($kindergarten->age_range as $age) {
                    $pdfPath = $this->createKindergartenMenuPDF($kindergarten->id, $age->id, $tempDir);
                    // dd($kindergarten->id, $age->id, $tempDir, $pdfPath, $pdfPath && file_exists($pdfPath));
                    if ($pdfPath && file_exists($pdfPath)) {
                        $pdfFiles[] = $pdfPath;
                        echo "PDF yaratildi: " . basename($pdfPath) . " (" . filesize($pdfPath) . " bytes)\n";
                    }
                }
            }

            if (empty($pdfFiles)) {
                echo "Hech qanday PDF fayl yaratilmadi!\n";
                return;
            }

            // ZIP yaratish
            $zipFileName = 'test_menyular_' . date('Y-m-d\TH-i-s') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            $zip = new \ZipArchive();
            $result = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            if ($result === TRUE) {
                foreach ($pdfFiles as $pdfFile) {
                    if (file_exists($pdfFile)) {
                        $fileName = basename($pdfFile);
                        if ($zip->addFile($pdfFile, $fileName)) {
                            echo "ZIP ga qo'shildi: " . $fileName . "\n";
                        }
                        else {
                            echo "ZIP ga qo'shilmadi: " . $fileName . "\n";
                        }
                    }
                }

                $zip->close();

                if (file_exists($zipPath)) {
                    echo "ZIP yaratildi: " . $zipPath . "\n";
                    echo "ZIP hajmi: " . filesize($zipPath) . " bytes\n";
                }
                else {
                    echo "ZIP yaratilmadi!\n";
                }
            }
            else {
                echo "ZIP yaratishda xatolik: " . $result . "\n";
            }

            // Test papkasini tozalash
            $this->deleteDirectory($tempDir);

        }
        catch (\Exception $e) {
            echo "Xatolik: " . $e->getMessage() . "\n";
        }
    }

    public function downloadShowdateMenusPDF(Request $request)
    {
        try {
            // Region ID ni tekshirish
            $regionId = $request->input('region_id');

            if (!$regionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Iltimos, hududni tanlang!'
                ], 400);
            }

            // Tanlangan region bo'yicha bog'chalarni olish
            $kindergartens = Kindgarden::where('region_id', $regionId)
                ->with('age_range')
                ->get();

            if ($kindergartens->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanlangan hududda hech qanday bog\'cha topilmadi!'
                ], 404);
            }

            // Vaqtinchalik papka yaratish
            $tempDir = storage_path('app/temp_zip_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];
            $dayId = $request->input('day_id'); // Kun ID sini olish

            foreach ($kindergartens as $kindergarten) {
                // Har bir bog'cha uchun har bir yosh guruhida PDF yaratish
                $ageRanges = Age_range::all();

                foreach ($ageRanges as $ageRange) {
                    // Number_childrens jadvalidan ma'lumotlarni olish
                    $numberChildren = Number_children::where([
                        ['kingar_name_id', '=', $kindergarten->id],
                        ['day_id', '=', $dayId],
                        ['king_age_name_id', '=', $ageRange->id]
                    ])->first();
                    if ($numberChildren) {
                        $pdfPath = $this->createShowdateMenuPDF($numberChildren, $kindergarten, $ageRange, $dayId, $tempDir);
                        if ($pdfPath) {
                            $pdfFiles[] = $pdfPath;
                        }
                    }
                }
            }

            if (empty($pdfFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hech qanday PDF fayl yaratilmadi!'
                ], 404);
            }

            // ZIP fayl yaratish
            $zipFileName = 'showdate_menus_' . $regionId . '_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                return response()->json([
                    'success' => false,
                    'message' => 'ZIP fayl yaratishda xatolik!'
                ], 500);
            }

            foreach ($pdfFiles as $pdfFile) {
                $zip->addFile($pdfFile, basename($pdfFile));
            }

            $zip->close();

            // Temporary fayllarni o'chirish
            foreach ($pdfFiles as $pdfFile) {
                if (file_exists($pdfFile)) {
                    unlink($pdfFile);
                }
            }
            rmdir($tempDir);

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createShowdateMenuPDF($numberChildren, $kindergarten, $ageRange, $dayId, $tempDir)
    {
        try {
            // Active_menu dan ma'lumotlarni olish
            $menu = Number_children::where([
                ['kingar_name_id', '=', $kindergarten->id],
                ['day_id', '=', $dayId],
                ['king_age_name_id', '=', $ageRange->id]
            ])->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
                ->join('age_ranges', 'number_childrens.king_age_name_id', '=', 'age_ranges.id')->get();
            // dd($menu);  
            $products = Product::where('hide', 1)
                ->orderBy('sort', 'ASC')->get();

            $menuitem = Active_menu::where('day_id', $dayId)
                ->where('title_menu_id', $menu[0]['kingar_menu_id'])
                ->where('age_range_id', $ageRange->id)
                ->join('meal_times', 'active_menus.menu_meal_time_id', '=', 'meal_times.id')
                ->join('food', 'active_menus.menu_food_id', '=', 'food.id')
                ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                ->orderBy('menu_meal_time_id')
                ->orderBy('menu_food_id')
                ->get();
            $day = Day::where('days.id', $dayId)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')
                ->first(['days.day_number', 'days.id as id', 'months.month_name', 'years.year_name']);
            // dd($day);
            $workerfood = titlemenu_food::where('day_id', ($dayId - 1))
                ->where('worker_age_id', $ageRange->id)
                ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                ->get();

            // $costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $today)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->get();
            // $narx = [];
            // foreach($costs as $row){
            // 	if(!isset($narx[$row->praduct_name_id])){
            // 		$narx[$row->praduct_name_id] = $row->price_cost;
            // 	}
            // }
            $protsent = Protsent::where('region_id', $kindergarten->region_id)->where('age_range_id', $ageRange->id)->first();

            $nextdaymenuitem = [];
            $workerproducts = [];
            $productallcount = array_fill(1, 500, 0);
            foreach ($menuitem as $item) {
                $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name;
                $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodweight'] = $item->food_weight;
                $productallcount[$item->product_name_id] += $item->weight;
                for ($i = 0; $i < count($products); $i++) {
                    if (empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id) {
                        $products[$i]['yes'] = 1;
                    }
                }
            }
            $workerproducts = array_fill(1, 500, 0);
            foreach ($workerfood as $tr) {
                // Tushlikdagi birinchi ovqat va nondan yeyishadi
                if (isset($nextdaymenuitem[3][$tr->food_id])) {
                    foreach ($nextdaymenuitem[3][$tr->food_id] as $key => $value) {
                        if ($key != 'foodname' and $key != 'foodweight') {
                            $workerproducts[$key] += $value;
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        // $productallcount[$key] += $value;
                        }
                    }
                }
            }
            $dompdf = new Dompdf('UTF-8');
            $html = mb_convert_encoding(view('pdffile.technolog.activmenu', ['protsent' => $protsent, 'day' => $day, 'productallcount' => $productallcount, 'workerproducts' => $workerproducts, 'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');

            if (isset($kindergarten->number_of_org)) {
                $fileName = $this->cleanFileName($kindergarten->kingar_name) . '-DMTT_' . $ageRange->age_name . '_' . $day->day_number . '-' . $day->month_name . '-' . $day->year_name . '.pdf';
            }
            else {
                $fileName = $this->cleanFileName($kindergarten->kingar_name) . '-DMTT_' . $ageRange->age_name . '_' . $day->day_number . '-' . $day->month_name . '-' . $day->year_name . '.pdf';
            }

            $pdfPath = $tempDir . '/' . $fileName;

            $dompdf->render();
            file_put_contents($pdfPath, $dompdf->output());

            return $pdfPath;

        }
        catch (\Exception $e) {
            return null;
        }
    }

    // ... existing code ...

    public function editWorkersCount(Request $request)
    {
        try {
            $request->validate([
                'day_id' => 'required|integer',
                'kingar_name_id' => 'required|integer',
                'workers_count' => 'required|integer|min:0'
            ]);

            // Number_childrens jadvalida barcha mos qatorlarni yangilash
            $updated = Number_children::where('day_id', $request->day_id)
                ->where('kingar_name_id', $request->kingar_name_id)
                ->update(['workers_count' => $request->workers_count]);

            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xodimlar soni muvaffaqiyatli yangilandi!',
                    'updated_count' => $updated
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'Hech qanday qator topilmadi!'
                ], 404);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Copy children numbers from selected day to nextday_nambers
     */
    public function copyChildrenNumbers(Request $request)
    {
        try {
            $ageId = $request->input('age_id');
            $dayId = $request->input('day_id');

            if (!$ageId || !$dayId) {
                return redirect()->back()->with('error', 'Yosh guruhi va kun tanlanishi shart!');
            }

            // Tanlangan kundagi bolalar sonlarini olish
            $sourceChildren = Number_children::where('day_id', $dayId)
                ->where('king_age_name_id', $ageId)
                ->get();

            if ($sourceChildren->isEmpty()) {
                return redirect()->back()->with('error', 'Tanlangan kunda hech qanday ma\'lumot topilmadi!');
            }

            // Mavjud nextday_nambers ma'lumotlarini vaqtincha saqlash
            $backupData = [];
            $nextdayRecords = Nextday_namber::where('king_age_name_id', $ageId)->get();

            foreach ($nextdayRecords as $record) {
                $backupData[] = [
                    'kingar_name_id' => $record->kingar_name_id,
                    'king_age_name_id' => $record->king_age_name_id,
                    'kingar_children_number' => $record->kingar_children_number,
                    'workers_count' => $record->workers_count,
                    'kingar_menu_id' => $record->kingar_menu_id,
                ];
            }

            // Backup ma'lumotlarni session ga saqlash
            session(['backup_children_data_' . $ageId => $backupData]);

            // Yangi ma'lumotlarni nusxalash
            $updatedCount = 0;
            foreach ($sourceChildren as $sourceChild) {
                $nextdayRecord = Nextday_namber::where('kingar_name_id', $sourceChild->kingar_name_id)
                    ->where('king_age_name_id', $ageId)
                    ->first();

                if ($nextdayRecord) {
                    // Mavjud qatorni yangilash
                    $nextdayRecord->update([
                        'kingar_children_number' => $sourceChild->kingar_children_number,
                        'workers_count' => $sourceChild->workers_count,
                    ]);
                    $updatedCount++;
                }
                else {
                    // Yangi qator yaratish
                    Nextday_namber::create([
                        'kingar_name_id' => $sourceChild->kingar_name_id,
                        'king_age_name_id' => $ageId,
                        'kingar_children_number' => $sourceChild->kingar_children_number,
                        'workers_count' => $sourceChild->workers_count,
                        'kingar_menu_id' => $sourceChild->kingar_menu_id,
                    ]);
                    $updatedCount++;
                }
            }

            return redirect()->back()->with('success',
                "Muvaffaqiyatli! {$updatedCount} ta bog'cha uchun bolalar sonlari nusxalandi. " .
                "Mavjud ma'lumotlar vaqtincha saqlanadi va kerak bo'lsa qayta tiklanadi."
            );

        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Restore backup children numbers
     */
    public function restoreChildrenNumbers(Request $request)
    {
        try {
            $ageId = $request->input('age_id');

            if (!$ageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yosh guruhi tanlanishi shart!'
                ], 400);
            }

            // Session dan backup ma'lumotlarni olish
            $backupData = session('backup_children_data_' . $ageId);

            if (!$backupData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Qayta tiklash uchun ma\'lumot topilmadi!'
                ], 404);
            }

            // Backup ma'lumotlarni qayta tiklash
            $restoredCount = 0;
            foreach ($backupData as $data) {
                $record = Nextday_namber::where('kingar_name_id', $data['kingar_name_id'])
                    ->where('king_age_name_id', $ageId)
                    ->first();

                if ($record) {
                    $record->update([
                        'kingar_children_number' => $data['kingar_children_number'],
                        'workers_count' => $data['workers_count'],
                        'kingar_menu_id' => $data['kingar_menu_id'],
                    ]);
                    $restoredCount++;
                }
            }

            // Session dan backup ma'lumotlarni o'chirish
            session()->forget('backup_children_data_' . $ageId);

            return response()->json([
                'success' => true,
                'message' => "Muvaffaqiyatli! {$restoredCount} ta bog'cha uchun ma'lumotlar qayta tiklandi."
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bolalar soni o'zgartirish tarixini olish
     */
    public function getChildrenCountHistory($gardenId, $ageId)
    {
        try {
            $history = ChildrenCountHistory::where('kingar_name_id', $gardenId)
                ->where('king_age_name_id', $ageId)
                ->with('changedBy')
                ->orderBy('changed_at', 'desc')
                ->get()
                ->map(function ($record) {
                return [
                'old_children_count' => $record->old_children_count,
                'new_children_count' => $record->new_children_count,
                'changed_by_name' => $record->changedBy ? $record->changedBy->name : 'Noma\'lum',
                'changed_at_formatted' => $record->changed_at->setTimezone('Asia/Tashkent')->format('d.m.Y H:i'),
                'change_reason' => $record->change_reason
                ];
            });

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificationlarni olish
     */
    public function getNotifications()
    {
        try {
            $notifications = Notification::getUnreadForUser(auth()->user()->id);

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'count' => $notifications->count()
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notification ni o'qilgan deb belgilash
     */
    public function markNotificationAsRead($notificationId)
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', auth()->user()->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Notification topilmadi'], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Barcha notificationlarni o'qilgan deb belgilash
     */
    public function markAllNotificationsAsRead()
    {
        try {
            Notification::where('notifiable_type', User::class)
                ->where('notifiable_id', auth()->user()->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test notification yaratish
     */
    public function createTestNotification()
    {
        try {
            $data = [
                'garden_name' => 'Test Bog\'cha',
                'age_name' => 'Test Yosh Guruhi',
                'old_count' => 10,
                'new_count' => 15,
                'changed_by' => 'Test User',
                'changed_at' => now()->setTimezone('Asia/Tashkent')->format('d.m.Y H:i'),
                'message' => 'Test notification - bu test xabari'
            ];

            $notification = Notification::createNotification(
                'children_count_changed',
                auth()->user(),
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Test notification yaratildi',
                'notification' => $notification
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Barcha menyular ro'yxatini olish
     */
    public function getAllMenus()
    {
        try {
            $menus = Titlemenu::leftjoin('seasons', 'titlemenus.menu_season_id', '=', 'seasons.id')
                ->get(['titlemenus.id', 'titlemenus.menu_name', 'seasons.season_name']);

            return response()->json([
                'success' => true,
                'menus' => $menus
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tanlangan menyuni active_menus jadvaliga ko'chirish
     */
    public function assignMenuToActive(Request $request)
    {
        try {
            $request->validate([
                'day_id' => 'required|integer',
                'kingar_name_id' => 'required|integer',
                'age_range_id' => 'required|integer',
                'title_menu_id' => 'required|integer'
            ]);

            $dayId = $request->day_id;
            $kingarId = $request->kingar_name_id;
            $ageId = $request->age_range_id;
            $menuId = $request->title_menu_id;
            $check = Active_menu::where('day_id', $dayId)->where('title_menu_id', $menuId)->delete();

            // 2. Menu_compositions dan ma'lumotlarni olish
            $compositions = Menu_composition::where('title_menu_id', $menuId)
                ->where('age_range_id', $ageId)
                ->get();

            // 3. Active_menus ga ko'chirish
            foreach ($compositions as $comp) {
                Active_menu::create([
                    'day_id' => $dayId,
                    'title_menu_id' => $menuId,
                    'menu_meal_time_id' => $comp->menu_meal_time_id,
                    'menu_food_id' => $comp->menu_food_id,
                    'product_name_id' => $comp->product_name_id,
                    'age_range_id' => $ageId,
                    'weight' => $comp->weight
                ]);
            }

            // 4. Number_childrens jadvalidagi kingar_menu_id ni yangilash
            Number_children::where('day_id', $dayId)
                ->where('kingar_name_id', $kingarId)
                ->where('king_age_name_id', $ageId)
                ->update(['kingar_menu_id' => $menuId]);

            return response()->json([
                'success' => true,
                'message' => 'Menyu muvaffaqiyatli o\'zgartirildi!'
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

}