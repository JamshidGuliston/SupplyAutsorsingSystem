<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Menu_composition;
use App\Models\minus_multi_storage;
use App\Models\Nextday_namber;
use App\Models\Number_children;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Temporary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Certificate;
use App\Models\ChildrenCountHistory;
use App\Models\Notification;

class ChefController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        $user = User::where('id', auth()->user()->id)->with('kindgarden')->first();
        $kindgarden = Kindgarden::where('id', $user->kindgarden[0]['id'])->with('age_range')->first();
        $sendchildcount = ChildrenCountHistory::where('kingar_name_id', $kindgarden->id)->where('created_at', '>=', now()->subHours(12))->get();
        
        // Bugungi kun uchun bolalar soni tarixini olish
        $todayChildrenCount = ChildrenCountHistory::where('kingar_name_id', $kindgarden->id)
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->where('created_at', '<=', date('Y-m-d 23:59:59'))
            ->with(['ageRange', 'changedBy'])
            ->orderBy('changed_at', 'desc')
            ->get();
        $productall = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('id', 'DESC')->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($day);
        $bool = minus_multi_storage::where('day_id', $day->id + 1)->where('kingarden_name_id', $kindgarden->id)->get();
        $ages = Age_range::all();
		foreach($ages as $age){
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $kindgarden->id],
                ['king_age_name_id', '=', $age->id]
            ])->get();	
            if(count($menu) == 0){
                continue;
            }
            for($i = 0; $i<count($productall); $i++){
                $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                    ->where('age_range_id', $age->id)
                    ->where('product_name_id', $productall[$i]['id'])
                    ->get();
                // echo $menuitem->count().' | ';
                if($menuitem->count() > 0){
                    $productall[$i]['yes'] = 1;
                }
            }
        }

        $oder = order_product::where('kingar_name_id', $kindgarden->id)
                    ->where('document_processes_id', 4)
                    ->orderBy('id', 'DESC')
                    ->first();
        $inproducts = [];
        if(isset($oder->day_id) > 0 and $day->id-$oder->day_id <= 3){
            $inproducts = order_product_structure::where('order_product_name_id', $oder->id)
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        }
        // dd($productall);
        return view('chef.home', compact('productall', 'kindgarden', 'sendchildcount', 'day', 'bool', 'inproducts', 'todayChildrenCount'));
    }
    public function minusproducts(Request $request){
        $bool = minus_multi_storage::where('day_id', $request->dayid + 1)->where('kingarden_name_id', $request->kindgarid)->get();
        if($bool->count() == 0){
            foreach($request->orders as $key => $value){
                $val = "";
                $bool = 1;
                for($i = 0; $i < strlen($value); $i++){
                    if (($value[$i] == ',' or $value[$i] == '.') and $bool){
                        $val = $val . '.';
                        $bool = 0;
                    }
                    elseif(is_numeric($value[$i])){
                        $val = $val . $value[$i];
                    }
                }
                if($val == ""){
                    $val = 0;
                }
                minus_multi_storage::create([
                    'day_id' => $request->dayid + 1,
                    'kingarden_name_id' => $request->kindgarid,
                    'kingar_menu_id' => 0,
                    'product_name_id' => $key,
                    'product_weight' => $val,
                ]);
            }
        }

        return redirect()->route('chef.home');
    }
    public function sendnumbers(Request $request)
    {
        // dd($request->all());
        $row = Nextday_namber::where('kingar_name_id', $request->kingar_id)->get();
        if($row != null){
            foreach($request->agecount as  $key => $value){
                try {
                    $currentRecord = $row->where('king_age_name_id', $key)->first();
                    $oldCount = $currentRecord->kingar_children_number;
                    
                    // Tarixni saqlash
                    ChildrenCountHistory::create([
                        'kingar_name_id' => $request->kingar_id,
                        'king_age_name_id' => $key,
                        'old_children_count' => $oldCount,
                        'new_children_count' => $value,
                        'changed_by' => auth()->user()->id,
                        'changed_at' => now(),
                        'change_reason' => 'Oshpaz tomonidan kunlik bolalar soni yuborildi'
                    ]);
                    
                    // Technologlarga notification yuborish
                    Notification::createChildrenCountChangeNotification(
                        $request->kingar_id,
                        $key,
                        $oldCount,
                        $value,
                        auth()->user()->id
                    );
                    
                    $currentRecord->update(['kingar_children_number' => $value]);
                } catch (\Exception $e) {
                    $age = Age_range::where('id', $key)->first();
                    return redirect()->route('chef.home')->with('error', 'Xatolik yuz berdi: ' . $age->age_name . ' keyingi ish kuni uchun qo\'shilmagan: ' . $e->getMessage());
                }
            }
        }
        else{
            return redirect()->route('chef.home')->with('error', 'Keyingi ish kuni uchun ma\'lumotlar mavjud emas');
        }
        return redirect()->route('chef.home')->with('success', 'Muvaffaqiyatli qo\'shildi');
    }

    public function right(Request $request)
    {
        $day = Day::orderby('id', 'DESC')->first();
        order_product::where('id', $request->orderid)->update([
            'document_processes_id' => 5
        ]);
        DB::transaction(function () use ($request, $day) {
            $order = order_product::where('id', $request->orderid)->first();
            $product = order_product_structure::where('order_product_name_id', $request->orderid)->get();
            foreach ($product as $row) {
                $exists = plus_multi_storage::where('order_product_id', $order['id'])
                                    ->where('kingarden_name_d', $order['kingar_name_id'])
                                    ->where('product_name_id', $row['product_name_id'])
                                    ->exists();
                if(!$exists){
                    plus_multi_storage::create([
                        'day_id' => $day->id,
                        'shop_id' => $order['shop_id'],
                        'kingarden_name_d' => $order['kingar_name_id'],
                        'order_product_id' => $order['id'],
                        'residual' => 0,
                        'product_name_id' => $row['product_name_id'],
                        'product_weight' => $row['product_weight'],
                    ]);
                }
            }
        });

        return redirect()->route('home');
    }

    public function certificates()
    {
        // Faqat faol va muddati o'tmagan sertifikatlarni olish
        $certificates = Certificate::with('products')
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->get();

        // Muddati yaqinlashgan sertifikatlarni olish
        $expiringCertificates = Certificate::expiringSoon()->get();
        
        return view('chef.certificates', compact('certificates', 'expiringCertificates'));
    }

    /**
     * Bolalar soni tarixini ko'rsatish
     */
    public function childrenCountHistory(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->with('kindgarden')->first();
        $kindgardenId = $user->kindgarden[0]['id'];
        
        $history = ChildrenCountHistory::getHistoryForKindgarden($kindgardenId);
        
        return view('chef.children_count_history', compact('history'));
    }

    /**
     * Nextday_namber jadvalini har kuni tozalash
     * Bu funksiya har kuni ertalab ishga tushirilishi kerak
     */
    public function clearNextdayNumbers()
    {
        try {
            // Barcha Nextday_namber ma'lumotlarini o'chirish
            Nextday_namber::truncate();
            
            // Yoki soft delete ishlatish mumkin
            // Nextday_namber::query()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Nextday_namber jadvali muvaffaqiyatli tozalandi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bolalar sonini qo'lda o'zgartirish (admin uchun)
     */
    public function updateChildrenCount(Request $request)
    {
        $request->validate([
            'kingar_name_id' => 'required|exists:kindgardens,id',
            'king_age_name_id' => 'required|exists:age_ranges,id',
            'new_count' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $nextdayRecord = Nextday_namber::where('kingar_name_id', $request->kingar_name_id)
                ->where('king_age_name_id', $request->king_age_name_id)
                ->first();

            if (!$nextdayRecord) {
                return redirect()->back()->with('error', 'Ma\'lumot topilmadi');
            }

            $oldCount = $nextdayRecord->kingar_children_number;

            // Tarixni saqlash
            ChildrenCountHistory::create([
                'kingar_name_id' => $request->kingar_name_id,
                'king_age_name_id' => $request->king_age_name_id,
                'old_children_count' => $oldCount,
                'new_children_count' => $request->new_count,
                'changed_by' => auth()->user()->id,
                'changed_at' => now(),
                'change_reason' => $request->reason ?? 'Admin tomonidan qo\'lda o\'zgartirildi'
            ]);

            // Yangi sonni saqlash
            $nextdayRecord->update(['kingar_children_number' => $request->new_count]);

            return redirect()->back()->with('success', 'Bolalar soni muvaffaqiyatli o\'zgartirildi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Oshpaz tomonidan bolalar sonini o'zgartirish
     */
    public function updateChildrenCountByChef(Request $request)
    {
        $request->validate([
            'age_id' => 'required|exists:age_ranges,id',
            'new_count' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $user = User::where('id', auth()->user()->id)->with('kindgarden')->first();
            $kindgardenId = $user->kindgarden[0]['id'];

            $nextdayRecord = Nextday_namber::where('kingar_name_id', $kindgardenId)
                ->where('king_age_name_id', $request->age_id)
                ->first();

            if (!$nextdayRecord) {
                return redirect()->back()->with('error', 'Ma\'lumot topilmadi');
            }

            $oldCount = $nextdayRecord->kingar_children_number;

            // Tarixni saqlash
            ChildrenCountHistory::create([
                'kingar_name_id' => $kindgardenId,
                'king_age_name_id' => $request->age_id,
                'old_children_count' => $oldCount,
                'new_children_count' => $request->new_count,
                'changed_by' => auth()->user()->id,
                'changed_at' => now(),
                'change_reason' => $request->reason ?? 'Oshpaz tomonidan qo\'lda o\'zgartirildi'
            ]);
            
            // notification yuborish
            Notification::createChildrenCountChangeNotification(
                $kindgardenId,
                $request->age_id,
                $oldCount,
                $request->new_count,
                auth()->user()->id
            );

            // Yangi sonni saqlash
            $nextdayRecord->update(['kingar_children_number' => $request->new_count]);

            return redirect()->back()->with('success', 'Bolalar soni muvaffaqiyatli o\'zgartirildi');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
