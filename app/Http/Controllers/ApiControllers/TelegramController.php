<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;
use App\Models\Day;
use App\Models\Person;
use App\Models\Kindgarden;
use App\Models\Temporary;
use App\Models\Age_range;
use App\Models\Nextday_namber;
use App\Models\Number_children;

class TelegramController extends Controller
{
    
    public function telegrambot(){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);

        if(isset($data['callback_query'])){
        	$func_param = explode("_", $this->getText($data));
	        $func = $func_param[0];
	        $chat_id = $data['callback_query']['message']['chat']['id'];
	    	$mid = $data['callback_query']['message']['message_id'];
        	
        	// $this->$func($data, $chat_id, $mid);
            
            $sdata = $data['callback_query'];
        }
        if(isset($data['message']))
            $sdata = $data['message'];

        $message = mb_strtolower(($sdata['text'] ? $sdata['text']
            : $sdata['data']) , 'utf-8' );
        
        $chat_id = $sdata['chat']['id'];
        
        $per = Person::where('telegram_id', $chat_id)->first();
        
        
        switch ($message){
            case '/start':
                $this->start($chat_id, $sdata);
                break;
            case is_numeric($message) and $per->count() and $per->childs_count != 0:
            	$this->setnumber($chat_id, $message, $per);
            	break;
            default:
            	if($per->count() and $per->childs_count != 0){
            		$this->sendMessage($chat_id, 'Faqat son kiriting.');
            	}
            	else{
            		$this->sendMessage($chat_id, 'Try another text');
            	}
        }
        
       
    }
    
    public function setnumber($chat_id, $text, $person){
    	$param = explode("_", $person->childs_count);
    	// return $this->sendMessage($chat_id, "Ma'lumotlar yuborildi. Raxmat! ". $param[0]);
    	Temporary::where('kingar_name_id', $person->kingar_id)
    		->where('age_id', $param[0])->delete();
    	
    	Temporary::create([
    		'kingar_name_id' => $person->kingar_id,
    		'age_id' => $param[0],
    		'age_number' => $text
    	]);
    	
    	$up = "";
    	for($i = 1; $i < count($param); $i++){
    		$up = $up . $param[$i];
    		if($i < count($param)-1){
    			$up = $up . '_';	
    		}
    	}
    	if($up == ""){
    		$upd = Person::where('telegram_id', $chat_id)->update(['childs_count' => 0]);
    		$this->sendMessage($chat_id, "Ma'lumotlar yuborildi. Raxmat!");
    	}
    	else{
	    	Person::where('telegram_id', $chat_id)->update(['childs_count' => $up]);
	    	
	    	$param = explode("_", $up);
	    	
	    	$kind = Kindgarden::where('id', $person->kingar_id)->with('age_range')->first();
	    	
	    	$this->sendMessage($chat_id, "Bugungi " . $kind->age_range[$param[0]-1]['age_name'] ."li bolalar sonini kiriting.");
    	}
    	
    }
    
    public function start($chat_id, $data){
    	$text = "Hi " .$chat_id. $data['chat']['first_name'];
    	$user = Person::where('telegram_id', $chat_id)->get();
    	
    	if($user->count() == 0){
    		$name = $data['chat']['first_name'];
    		Person::create([
    			'kingar_id' => 0,
                'shop_id' => -1,
	            'telegram_id' => $chat_id,
	            'telegram_name' => $name,
	            'childs_count' => 0,
	            'telegram_password' => 123,
    		]);
    		$text = "Shaxsingiz aniqlanmoqda...";
    	}
    	
        
        $this->sendMessage($chat_id, $text);
    }

    public function sendtoallgarden(){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        
    	Temporary::truncate();
    	$kind = Kindgarden::where('hide', 1)->with('age_range')->get();
    	
    	
    	foreach($kind as $row){
    		$person = Person::where('telegram_id', $row->telegram_user_id)->first();
    		$tx = "";
    		$loop = 0;
    		foreach($row->age_range as $ageid){
    			$tx = $tx . $ageid->id;
    			if($loop++ < count($row->age_range)-1) 
    				$tx = $tx . '_';
    		}
    		
    		Person::where('telegram_id', $row->telegram_user_id)
	    		->update([
	    			'childs_count' => $tx
	    		]);
    		
    		$this->sendMessage($row->telegram_user_id, "Bugungi " . $row->age_range[0]['age_name'] ."li bolalar sonini kiriting.");
    	}
    	
    	return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }
    
    public function sendtoonegarden(Request $request, $id){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        
        $kind = Kindgarden::where('id', $id)->with('age_range')->first();
        
        $tmp = Temporary::where('kingar_name_id', $kind->id)->delete();
        
        $person = Person::where('telegram_id', $kind->telegram_user_id)->first();
        
        $tx = "";
		$loop = 0;
		foreach($kind->age_range as $ageid){
			$tx = $tx . $ageid->id;
			if($loop++ < count($kind->age_range)-1) 
				$tx = $tx . '_';
		}
		
		Person::where('telegram_id', $kind->telegram_user_id)
			->update([
				'childs_count' => $tx
			]);
        
        $this->sendMessage($kind->telegram_user_id, "Bugungi " . $kind->age_range[0]['age_name'] ."li bolalar sonini kiriting.");
    	
    	return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }
    
    
    // Taxminiy menyularni yuborish hammasiga
    public function nextsendmenutoallgarden(){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        $kind = Kindgarden::where('hide', 1)->with('age_range')->get();
    	// $kind = Kindgarden::where('hide', 1)->with('age_range')->get();$row->telegram_user_id
    	$endday = Day::orderBy('id', 'DESC')->first();
    	foreach($kind as $row){
    		$dayid = $endday['id'];
    		$kinid = $row->id;
    		$buttons = array( );
            // $docurl = "http://cj56359.tmweb.ru/pdf/'$dayid'-'$kinid'-nextnaklad.pdf";
        	$this->sendMessage($row->telegram_user_id, "Кейинги иш куни учун боғчангиз тахминий менюлари:"); 
            $url = "https://cj56359.tmweb.ru/nextnakladnoyPDF/'$kinid'";        
            $buttons[] = [
                $this->buildInlineKeyBoardButton("Тахминий Накладной", "", $url)
            ];
            // $this->sendMessage($row->telegram_user_id, "<a href='https://cj56359.tmweb.ru/nextnakladnoyPDF/".$row->id."'>".$d."-Тахминий Накладной</a>"); $row->telegram_user_id
    		foreach($row->age_range as $ageid){
    			$kid = $row->id;
    			$urlpdf = "https://cj56359.tmweb.ru/nextnakladnoyPDF/'$kid'"."/".$ageid->id;        
                $buttons[] = [
                    $this->buildInlineKeyBoardButton("Тахминий ".$ageid->age_name." менюси", "", $urlpdf)
                ];
    			// $this->sendMessage($row->telegram_user_id, "<a href='https://cj56359.tmweb.ru/nextdaymenuPDF/".$row->id."/".$ageid->id."'>".$d."-Тахминий ".$ageid->age_name."  менюси</a>");          
    		}

            $fields = [
                'chat_id' => $row->telegram_user_id,
                'text' => "Тахминий манюлар (тугмани босинг)",
                'reply_markup' => $this->buildInlineKeyBoard($buttons),
                'parse_mode' => 'html',
            ];
    
            $this->sendTelegram("sendMessage", $fields);
    	}
    	
    	return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);	
    }
    // activ menular
    public function activsendmenutoallgardens($dayid){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        $kind = Kindgarden::where('hide', 1)->with('age_range')->get();
    	// $kind = Kindgarden::where('hide', 1)->with('age_range')->get(); 640892021
    	// dd($kind); $row->telegram_user_id
        $buttons = "";
    	foreach($kind as $row){
            $buttons = array( );
    		// $docurl = "http://cj56359.tmweb.ru/pdf/".$dayid.'-'.$row->id."-activnaklad.pdf";
            $this->sendMessage($row->telegram_user_id, "Бугунги боғчангиз менюлари:");
            $url = "https://cj56359.tmweb.ru/activnakladPDF/".$dayid."/".$row->id;
            $buttons[] = [
                $this->buildInlineKeyBoardButton("Накладной", "", $url)
            ];          
        	
            // $this->sendMessage($row->telegram_user_id, "<a href='https://cj56359.tmweb.ru/activnakladPDF/".$dayid."/".$row->id."'>".$dayid."-Накладной</a>"); 
    		foreach($row->age_range as $ageid){
    			// $docurl = "http://cj56359.tmweb.ru/pdf/".$dayid.'-'.$row->id.'-'.$ageid->id."-activemenu.pdf";
        		// $this->sendTelegram("sendDocument", [
                    //    'chat_id' => $row->telegram_user_id, 
                    //    'document' => $docurl,
                    //    'caption' => $ageid->age_name." менюси"
                    // ]);
                $url = "https://cj56359.tmweb.ru/activmenuPDF/".$dayid.'/'.$row->id.'/'.$ageid->id;
                $buttons[] = [
                    $this->buildInlineKeyBoardButton($ageid->age_name." менюси", "", $url)
                ]; 
    			// $this->sendMessage($row->telegram_user_id, "<a href='https://cj56359.tmweb.ru/activmenuPDF/".$dayid."/".$row->id."/".$ageid->id."'>".$dayid."-".$ageid->age_name."</a>");          
    		}
            $fields = [
                'chat_id' => $row->telegram_user_id,
                'text' => "Бугунги манюлар (тугмани босинг)",
                'reply_markup' => $this->buildInlineKeyBoard($buttons),
                'parse_mode' => 'html',
            ];
            $this->sendTelegram("sendMessage", $fields);
    	}
    	
    	// return redirect()->route('technolog.sendmenu', ['day' => $dayid]);	
    }
    // <>
    public function nextsendmenutoonegarden(Request $request, $id){
    	date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        $endday = Day::orderBy('id', 'DESC')->first();
        // $kingar->telegram_user_id
    	$kingar = Kindgarden::where('id', $id)->where('hide', 1)->with('age_range')->first();
        $this->sendMessage($kingar->telegram_user_id, "Кейинги иш куни учун боғчангиз тахминий менюлари:"); 
        $url = "https://cj56359.tmweb.ru/nextnakladnoyPDF/".$id;        
        $buttons[] = [
            $this->buildInlineKeyBoardButton("Тахминий Накладной", "", $url)
        ];
        
        // $docurl = "http://cj56359.tmweb.ru/pdf/".$endday['id'].'-'.$id."-nextnaklad.pdf";
                
		foreach($kingar->age_range as $ageid){
			// $docurl = "http://cj56359.tmweb.ru/pdf/".$endday['id'].'-'.$id.'-'.$ageid->id."-nextmenu.pdf";
        	$url = "https://cj56359.tmweb.ru/nextdaymenuPDF/".$id."/".$ageid->id;        
            $buttons[] = [
                $this->buildInlineKeyBoardButton("Тахминий ".$ageid->age_name." менюси", "", $url)
            ];
        
		}

    	$fields = [
            'chat_id' => $kingar->telegram_user_id,
            'text' => "Тахминий манюлар (тугмани босинг)",
            'reply_markup' => $this->buildInlineKeyBoard($buttons),
            'parse_mode' => 'html',
        ];

        $this->sendTelegram("sendMessage", $fields);
        
    	return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    // activ menu $kingar->telegram_user_id
    public function activsendmenutoonegarden(Request $request, $dayid, $id){
    	date_default_timezone_set('Asia/Tashkent');
        // $docurl = "http://cj56359.tmweb.ru/pdf/".$dayid.'-'.$id."-activnaklad.pdf";
        $d = strtotime("-10 hours 30 minutes");
    	$kingar = Kindgarden::where('id', $id)->where('hide', 1)->with('age_range')->first();
        
        $this->sendMessage($kingar->telegram_user_id, "Бугунги боғчангиз менюлари:");
        $url = "https://cj56359.tmweb.ru/activnakladPDF/".$dayid."/".$id;
        $buttons[] = [
            $this->buildInlineKeyBoardButton("Накладной", "", $url)
        ];    
    	// $kingar->telegram_user_id
		foreach($kingar->age_range as $ageid){
			// $docurl = "http://cj56359.tmweb.ru/pdf/".$dayid.'-'.$id.'-'.$ageid->id."-activemenu.pdf";
			$url = "https://cj56359.tmweb.ru/activmenuPDF/".$dayid.'/'.$id.'/'.$ageid->id;
            $buttons[] = [
                $this->buildInlineKeyBoardButton($ageid->age_name." менюси", "", $url)
            ]; 
		}

    	$fields = [
            'chat_id' => $kingar->telegram_user_id,
            'text' => "Бугунги манюлар (тугмани босинг)",
            'reply_markup' => $this->buildInlineKeyBoard($buttons),
            'parse_mode' => 'html',
        ];
        $this->sendTelegram("sendMessage", $fields);

    	return redirect()->route('technolog.sendmenu', ['day' => $dayid]);
    }
    
    public function sendordertoallshop(){
    	
    }
    // <>
    public function sendordertooneshop(){
    	$text = "";
    	$kingar = Kindgarden::where('id', 1)->where('hide', 1)->with('age_range')->first();
		foreach($kingar->age_range as $ageid){
			
			$this->sendMessage(640892021, "<a href='https://cj56359.tmweb.ru/downloadPDF/1/2'>".$ageid->age_name."/a>");          
		}
    }
    
    private function getType($data)
    {
        if (isset($data['callback_query'])) {
            return "callback_query";
        } elseif (isset($data['message']['text'])) {
            return "message";
        } elseif (isset($data['message']['photo'])) {
            return "photo";
        } else {
            return false;
        }
    }
    
    private function getText($data)
    {
        if ($this->getType($data) == "callback_query") {
            return $data['callback_query']['data'];
        }
        return $data['message']['text'];
    }

    /** Кнопка inline
     * @param $text
     * @param string $callback_data
     * @param string $url
     * @return array
     */
    public function buildInlineKeyboardButton($text, $callback_data = '', $url = '')
    {
        // рисуем кнопке текст
        $replyMarkup = [
            'text' => $text,
        ];
        // пишем одно из обязательных дополнений кнопке
        if ($url != '') {
            $replyMarkup['url'] = $url;
        } elseif ($callback_data != '') {
            $replyMarkup['callback_data'] = $callback_data;
        }
        // возвращаем кнопку
        return $replyMarkup;
    }

    /** набор кнопок inline
     * @param array $options
     * @return string
     */
    public function buildInlineKeyBoard(array $options)
    {
        // собираем кнопки
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        // преобразуем в JSON объект
        $encodedMarkup = json_encode($replyMarkup, true);
        // возвращаем клавиатуру
        return $encodedMarkup;
    }

    /** кнопка клавиатуры
     * @param $text
     * @param bool $request_contact
     * @param bool $request_location
     * @return array
     */
    public function buildKeyboardButton($text, $request_contact = false, $request_location = false)
    {
        $replyMarkup = [
            'text' => $text,
            'request_contact' => $request_contact,
            'request_location' => $request_location,
        ];
        return $replyMarkup;
    }

    /** готовим набор кнопок клавиатуры
     * @param array $options
     * @param bool $onetime
     * @param bool $resize
     * @param bool $selective
     * @return string
     */
    public function buildKeyBoard(array $options, $onetime = false, $resize = false, $selective = true)
    {
        $replyMarkup = [
            'keyboard' => $options,
            'one_time_keyboard' => $onetime,
            'resize_keyboard' => $resize,
            'selective' => $selective,
        ];
        $encodedMarkup = json_encode($replyMarkup, true);
        return $encodedMarkup;
    }

    //////////////////////////////////
    // Взаимодействие с Бот Апи
    //////////////////////////////////
    /** Отправляем текстовое сообщение с inline кнопками
     * @param $user_id
     * @param $text
     * @param null $buttons
     * @return mixed
     */
    private function sendMessage($user_id, $text, $buttons = NULL)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'text' => $text,
            'parse_mode' => 'html'
        ];
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        // отправляем текстовое сообщение
        return $this->sendTelegram("sendMessage", $data_send);
    }

    /** Меняем содержимое сообщения
     * @param $user_id
     * @param $message_id
     * @param $text
     * @param null $buttons
     * @return mixed
     */
    private function editMessageText($user_id, $message_id, $text, $buttons = NULL)
    {
        // готовим массив данных
        $data_send = [
            'chat_id' => $user_id,
            'text' => $text,
            'message_id' => $message_id,
            'parse_mode' => 'html'
        ];
        // если переданны кнопки то добавляем их к сообщению
        if (!is_null($buttons) && is_array($buttons)) {
            $data_send['reply_markup'] = $this->buildInlineKeyBoard($buttons);
        }
        // отправляем текстовое сообщение
        return $this->sendTelegram("editMessageText", $data_send);
    }
    
    private function sendTelegram($method,$data,$headers=[]){
        $handle = curl_init('https://api.telegram.org/bot5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k'.'/'.$method);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($handle, CURLOPT_HTTPHEADER,
            array_merge( array("Content-Type: application/json"),
                $headers ) );
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($handle);
        curl_close($handle);
        return ( json_decode($result,1) ? json_decode($result,1) : $result);
    }
    
    private function botApiQuery($method, $fields = array())
    {
        $ch = curl_init('https://api.telegram.org/bot5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k' . '/' . $method);
        curl_setopt_array($ch, array(
            CURLOPT_POST => count($fields),
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ));
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $r;
    }

}
