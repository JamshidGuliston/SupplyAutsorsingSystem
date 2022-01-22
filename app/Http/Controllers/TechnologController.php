<?php

namespace App\Http\Controllers;

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
use App\Models\Menu_composition;
use App\Models\Number_children;
use App\Models\Titlemenu;
use App\Models\order_product;
use App\Models\history_process;
use App\Models\Meal_time;
use App\Models\order_product_structure;
use App\Models\Product;
use App\Models\Product_category;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Size;
use Dompdf\Dompdf;
use TCG\Voyager\Models\Category;

class TechnologController extends Controller
{
    public function index(Request $request)
    {
        $month = Month::where('month_active', 1)->get();
        // dd($month[0]->id);
        // faqat aktiv oy sanalarini oladi
        $days = Day::where('month_id', $month[0]->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->select('days.id', 'days.day_number', 'days.month_id', 'months.month_name', 'years.year_name')
            ->orderBy('days.id', 'DESC')->get();
        $kingar = Kindgarden::all();
        date_default_timezone_set('Asia/Tashkent');
        // date("h:i:sa:M-d-Y");
        $d = strtotime("+0 day");
        // dd($days[0]->day_number);
        return view('technolog.home', ['date' => $days, 'tomm' => $d, 'kingardens' => $kingar]);
    }

    // yangi kun ishlari
    public function newday(Request $request)
    {
        Temporary::truncate();
        $months = Month::all();
        $year = Year::orderBy('id', 'DESC')->first();
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("+0 day");
        foreach ($months as $month) {
            if ($month->month_en == date("F", $d)) {
                Month::where('month_en', $request->daymonth)
                    ->update(['month_active' => 1]);
                $activeID = $month;
            } else {
                Month::where('month_en', $month->month_en)
                    ->update(['month_active' => 0]);
            }
        }
        if (empty($year->year_name)) {
            $rr = Year::create([
                'year_name' => $request->dayyear,
                'year_active' => 1
            ]);
            $year = $rr;
        }
        if (date("Y", $d) != $year->year_name) {
            Year::where('id', $year->id)
                ->update(['year_active' => 0]);
            $rr = Year::create([
                'year_name' => $request->dayyear,
                'year_active' => 1
            ]);
            $year = $rr;
        }
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

        $users = Kindgarden::where('hide', 1)->get();
        $path = "https://api.telegram.org/bot";
        $token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
        $text = "Боғчангиз учун эртанги овқатлар менюсига болалар сонини критинг. <b>3-4 ёшгача = ?</b>";
        $buttons = '{"inline_keyboard":[[{"text":"1","callback_data":"addnumber_1"}, {"text":"2","callback_data":"addnumber_2"}, {"text":"3","callback_data":"addnumber_3"}], [{"text":"4","callback_data":"addnumber_4"}, {"text":"5","callback_data":"addnumber_5"}, {"text":"6","callback_data":"addnumber_6"}], [{"text":"7","callback_data":"addnumber_7"}, {"text":"8","callback_data":"addnumber_8"}, {"text":"9","callback_data":"addnumber_9"}], [{"text":"0","callback_data":"addnumber_0"}, {"text":"<","callback_data":"remove_<"}]]}';
        // dd($users);
        foreach ($users as $user) {
            Person::where('telegram_id', $user->telegram_user_id)->update(array('childs_count' => '0'));
            $this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user->telegram_user_id . '&text=' . $text . '&parse_mode=html&reply_markup=' . $buttons);
        }

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }


    public function sendmenu($day)
    {
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("+0 day");
        $ages = Age_range::all();
        // dd($ages);
        if ($day == date("d-F-Y", $d)) {
            $sid = Season::where('hide', 1)->first();
            // dd($sid);
            $menus = Titlemenu::where('menu_season_id', $sid->id)->get();
            $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')
                ->orderby('kindgardens.id', 'ASC')->get();

            $gar = Kindgarden::with('age_range')->get();
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
            return view('technolog.newday', ['ages' => $ages, 'menus' => $menus, 'temps' => $mass, 'gardens' => $gar, 'activ'=>$activ]);
        } else {
            return view('technolog.showdate', ['ages' => $ages]);
        }
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
        $html = [];
        foreach ($results[0]->age_range as $rows) {
            // $html = $html + "<input type='text' value='salom'>";
            array_push($html, "<div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' name='ages[]' data-id=" . $rows['id'] . "  class='form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default'>
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
                'hide' => $request->hide,
            ]);
        return redirect()->route('technolog.home');
    }

    public function getage(Request $request, $bogid)
    {
        $results = Kindgarden::where('id', $bogid)->with('age_range')->get();
        // dd($results[0]->age_range);
        $htmls = [];
        array_push($htmls, "<h3>" . $results[0]['kingar_name'] . "</h3> <input type='hidden' class='kingarediteid' value=" . $results[0]['id'] . " >");
        foreach ($results[0]->age_range as $rows) {
            $edite =  Temporary::where('kingar_name_id', $bogid)->where('age_id', $rows['id'])->first();
            if (empty($edite['age_number'])) {

                $edite['age_number'] = 0;
            }
            // $html = $html + "<input type='text' value='salom'>";
            array_push($htmls, "  <div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' required name='ages[]' value=" . $edite['age_number'] . " data-id=" . $rows['id'] . "  class='form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default'>
            </div>");
        }
        return $htmls;
    }

    public function editage(Request $request, $bogid, $ageid, $qiymati)
    {
        $find = Temporary::where('kingar_name_id', $bogid)->where('age_id', $ageid)->get();

        // dd($find);

        if (empty($find[0])) {
            Temporary::create([
                'kingar_name_id' => $bogid,
                'age_id' => $ageid,
                'age_number' => $qiymati
            ]);
        } else {
            Temporary::where('kingar_name_id', $bogid)->where('age_id', $ageid)->update([
                'kingar_name_id' => $bogid,
                'age_id' => $ageid,
                'age_number' => $qiymati
            ]);
        }
    }

    // mayda skladlarga product buyurtma berish

    public function addproduct()
    {
        $months = Month::all();
        $days = Day::orderby('id', 'DESC')->get();
        $orederproduct = order_product::join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            ->where('day_id', $days[1]->id)
            ->select('order_products.id', 'days.day_number', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name') 
            ->orderby('order_products.id', 'DESC')
            ->get();
        $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->get();
        // dd($orederproduct);
        $kingar = Kindgarden::where('hide', 1)->get();
        
        foreach($orederproduct as $item){
            $t = 0;
            foreach($kingar as $ki){
                if($item->kingar_name == $ki->kingar_name)
                {
                    $kingar[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.addproduct', ['gardens' => $kingar, 'orders' => $orederproduct, 'products'=>$orederitems, 'months'=>$months]);
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
        if (empty($orederproduct->day_number) or $days[1]->day_number != $orederproduct->day_number or $days[1]->month_id != $orederproduct->month_id) {
            return redirect()->route('technolog.addproduct');
        }
        // shu joyida hide ishlatishimiz kerak majbur
        $newsproduct = Product::all();
        $items = order_product_structure::where('order_product_name_id', $id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name')
            ->get();
        foreach($items as $item){
            $t = 0;
            foreach($newsproduct as $pro){
                if($item->product_name == $pro->product_name){
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
        order_product_structure::create([
            'order_product_name_id' => $request->titleid,
            'product_name_id' => $request->productsid,
            'product_weight' => $request->sizeproduct,
        ]);
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
        } else {
            $result = 0;
        }
        return $result;
    }
    // botga start bosganlarni tashkilotiga bog'lash
    public function getbotusers(Request $request){
        $users = Person::with('shop')->with('garden')->orderby('id', 'DESC')->get();
        $gardens = Kindgarden::all();
        $shops = Shop::all();
        // dd($users);
        return view('technolog.botusers', compact('users', 'gardens', 'shops'));
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
            <input  type='number' data-producy=" . $number['id'] . " value=" . $number['product_weight'] . " required class='form-control  product_order'  placeholder='raqam kiriting'></div>";

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

    public function bindgarden(Request $request){
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

    public function bindshop(Request $request){
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
	
	public function go(Request $request)
	{
		$days = Day::orderBy('id', 'DESC')->first();
		$chil_number = Temporary::all();
		foreach ($chil_number as $child) {
			$workers = Kindgarden::where('id', $child->kingar_name_id)->first();
			// dd($workers['worker_count']);
			$menusi = $request['manuone'];
			if($child->age_id == 3){
				$menusi = $request['two'];
			}
			Number_children::create([
				'kingar_name_id' => $child->kingar_name_id,
				'day_id' => (int)$days['id'],
				'king_age_name_id' => $child->age_id,
				'kingar_children_number' => $child->age_number,
				'workers_count' => $workers['worker_count'],
				'kingar_menu_id' => $menusi,
			]);
			$path = "https://api.telegram.org/bot";
			$token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
			$tday = $days['id'];
			$yosh = "";
			if($child->age_id == 1){
				$yosh = "4-7 yoshli";
			}
			if($child->age_id == 2){
				$yosh = "3-4 yoshli";
			}
			if($child->age_id == 3){
				$yosh = "qisqa gurux";
			}
			$urlpdf ='https://cj56359.tmweb.ru/downloadPDF/' . $child->kingar_name_id . '/' . $tday . '/'.$child->age_id;
			$user = Kindgarden::where('id', '=', $child->kingar_name_id)->first();
			$this->curl_get_contents($path . $token . '/sendmessage?chat_id=694792808&text=<a href="'.$urlpdf.'">'.$yosh.'</a>&parse_mode=html');
			// $this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user->telegram_user_id . '&text=<a href="'.$urlpdf.'">'.$yosh.'</a>&parse_mode=html');
		}

		$temp = Temporary::truncate();
		$gr = Kindgarden::all();

		return redirect()->route('technolog.home');
	}

    public function allproducts(Request $request)
    {
        $products = Product::with('shop')->get();
        // dd($products);
        return view('technolog.allproducts', compact('products'));
    }

    public function settingsproduct(Request $request, $id)
    {
        $product = Product::where('id', $id)->first();
        $categories = Product_category::all();
        $sizes = Size::all(); 
        // dd($product);
        return view('technolog.settingsproduct', compact('product', 'categories', 'sizes'));
    }

    public function updateproduct(Request $request)
    {
        // dd($request->all());
        Product::where('id', $request['productid'])
            ->update([
                'size_name_id' => $request['sizeid'],
                'category_name_id' => $request['catid'],
                'div' => $request['div'],
                'sort' => $request['sort'],
                'hide' => $request['hide']
            ]);
        return redirect()->route('technolog.allproducts');
    }

    public function shops(Request $request)
    {
        $shops = Shop::all();
        // dd($shops);
        return view('technolog.shops', compact('shops'));
    }

    public function shopsettings(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('product')->with('kindgarden')->first();
        $products = Product::all();
        $gardens = Kindgarden::all();
        return view('technolog.shopsettings', compact('shop', 'products', 'gardens'));
    }

    public function updateshop(Request $request)
    {
        $shop = Shop::find($request->shopid);
        $prd = $request->products;
        $shop->product()->sync($prd);
        $grd = $request->gardens;
        $shop->kindgarden()->sync($grd);
        $shop->update([
                'shop_name' => $request->shopname,
                'hide' => $request->hide
            ]);
        return redirect()->route('technolog.shops');
    }

    public function addshop()
    {
        $products = Product::all();
        $gardens = Kindgarden::all();

        return view('technolog.addshop', compact('products', 'gardens'));
    }

    public function createshop(Request $request)
    {
        $shop = Shop::create([
            'shop_name' => $request->name,
            'telegram_id' => 0,
            'hide' => 1
        ]);
        $prd = $request->products;
        $shop->product()->sync($prd);
        $grd = $request->gardens;
        $shop->kindgarden()->sync($grd);

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
    	        'meal_time_id' => $request->timeid
            ]);
        
        return redirect()->route('food');
    }

    public function fooditem(Request $request, $id)
    {
        $productall = Product::all();
        $food = Food_composition::where('food_name_id', $id)->join('food', 'food.id', '=', 'food_compositions.food_name_id')
                        ->join('products', 'products.id', '=', 'food_compositions.product_name_id')
                        ->get(['food_compositions.id', 'food.food_name','products.product_name']);
        // dd($food);
        foreach($food as $item){
            $t = 0;
            foreach($productall as $pro){
                if($item->product_name == $pro->product_name){
                    $productall[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.fooditem', compact('food', 'productall', 'id'));
    }

    public function addproductfood(Request $request)
    {
        Food_composition::create([
            'food_name_id' => $request->titleid,
    	    'product_name_id' => $request->productid
        ]);
        return redirect()->route('fooditem', $request->titleid);
    }

    public function deleteproductfood(Request $request)
    {
        Food_composition::where('id', $request->id)->delete();
    }

    public function editproductfood(Request $request)
    {
        Food_composition::where('id', $request->id)
            ->update([
    	        'product_name_id' => $request->productid
            ]);
        
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
            'food_image' => 'png.png'
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
        $menus = Titlemenu::where('menu_season_id', $id)->get();

        return view('technolog.menus', compact('menus', 'id'));
    }

    public function addtitlemenu(Request $request, $id)
    {
        $ages = Age_range::all();
        return view('technolog.addtitlemenu', compact('id', 'ages'));
    }

    public function createmenu(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::create([
            'menu_name' => $request->name,
            'menu_season_id' => $request->seasonid
        ]);

        $age = $request->yongchek;
        $menu->age_range()->sync($age);

        return redirect()->route('technolog.menus', $request->seasonid);
    }

    public function menuitem(Request $request, $id)
    {
        $times = Meal_time::all();
        $titlemenu = Titlemenu::where('id', $id)->with('age_range')->first();
        $menuitem = Menu_composition::where('title_menu_id', $id)
                ->join('titlemenus', 'titlemenus.id', '=', 'menu_compositions.title_menu_id')
                ->join('meal_times', 'meal_times.id', '=', 'menu_compositions.menu_meal_time_id')
                ->join('food', 'food.id', '=', 'menu_compositions.menu_food_id')
                ->join('products', 'products.id', '=', 'menu_compositions.product_name_id')
                ->join('age_ranges', 'age_ranges.id', '=', 'menu_compositions.age_range_id')
                ->orderby('menu_compositions.menu_meal_time_id', 'ASC')
                ->orderby('menu_compositions.id', 'ASC')
                ->get([
                    'titlemenus.menu_name', 
                    'titlemenus.id as menuid', 
                    'meal_times.meal_time_name', 
                    'meal_times.id as meal_timeid', 
                    'food.food_name', 
                    'food.id as foodid', 
                    'products.product_name', 
                    'products.id as productid', 
                    'age_ranges.id as ageid', 
                    'menu_compositions.weight',
                    'menu_compositions.id'
                ]); 
        // dd($menuitem);
        return view('technolog.menuitem', compact('id', 'times', 'titlemenu', 'menuitem'));
    }

    // ajax

    public function getfood(Request $request)
    {
        $food = Food::where('meal_time_id', $request->id)
                ->orwhere('meal_time_id', 0)
                ->get();

        $html = "<select id='foodid' name='foodid' onchange='change()' class='form-select' required aria-label='Default select example'>
                        <option value=''>--Taomni tanlang--</option>";
        foreach($food as $row){
            $html = $html."<option value=".$row->id.">".$row->food_name."</option>";
        }
        $html = $html."</select>";
        return $html;
    }

    public function getfoodcomposition(Request $request)
    {
        $menu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $foodcom = Food_composition::where('food_name_id', $request->id)
                ->join('products', 'products.id', '=', 'food_compositions.product_name_id')->get();
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach($menu->age_range as $row){
            $html = $html."<th scope='col'>".$row['age_name']."</th>";
        }
        $html = $html."</tr>
                </thead>
                <tbody>";
        foreach($foodcom as $product){
            $html = $html."<tr>
                <td><input type='hidden' name='products[]' value='".$product->id."'></td>
                <td>".$product->product_name."</td>";
                foreach($menu->age_range as $row){
                    $html = $html."<td><input type='number' name='ages".$product->id."[]' required style='width: 100%;'></td>";
                }
                
                $html = $html."</tr>";
        }
        $html = $html."</tbody>
            </table>";
        
        return $html;
    }

    public function createmenucomposition(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::where('id', $request->titleid)->with('age_range')->first();
        foreach($request->products as $product)
        {
            $ages = "ages".$product;
            $t = 0;
            foreach($menu->age_range as $age)
            {
                // echo "menu: ".$request->titleid." mealtime: ".$request->timeid." food: ".$request->foodid." product: ".$product." age: ".$age->id." weight: ".$request[$ages][$t++]." <br/>";
                Menu_composition::create([
                    'title_menu_id' => $request->titleid,
                    'menu_meal_time_id' => $request->timeid,
                    'menu_food_id' => $request->foodid,
                    'product_name_id' => $product,
                    'age_range_id' => $age->id,
                    'weight' => $request[$ages][$t++]
                ]);
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
                ->get(['menu_compositions.id', 'products.product_name', 'age_range_id', 'weight']);
        // dd($foodcom);
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach($menu->age_range as $row){
            $html = $html."<th scope='col'>".$row['age_name']."</th>";
        }
        $html = $html."</tr>
                </thead>
                <tbody>";
        for($it = 0; $it < count($foodcom); $it++){
            $html = $html."<tr>
                <td></td>
                <td>".$foodcom[$it]['product_name']."</td>";
                foreach($menu->age_range as $row){
                    $html = $html."<td><input type='number' name='ages[]' value='".$foodcom[$it]['weight']."' required style='width: 100%;'></td>";
                    $html = $html."<input type='hidden' name='rows[]' value='".$foodcom[$it]['id']."'>";
                    $it++;
                }
                
                $html = $html."</tr>";
        }
        $html = $html."</tbody>
            </table>";
        
        return $html;

    }

    public function editemenuproduct(Request $request)
    {
        // dd($request->all());
        $it = 0;
        foreach($request->rows as $row){
            Menu_composition::where('id', $row)
                    ->update([
                        'weight' => $request->ages[$it]
                    ]);
            $it++;
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
}
