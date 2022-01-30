<?php

header("HTTP/1.1 200 OK");
header('Content-type: text/html; charset=utf-8');

$ni = new NishonBot();
$ni->init();	


class NishonBot
{
	 // dastlabki ma'lumotlar
	private $path = "https://api.telegram.org/bot";
    private $token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
    private $img_path = "img"; // rasmlar bilan katalogga yo'l
    private $host = 'localhost';
    private $db = 'cj56359_mtm';
    private $user = 'cj56359_mtm';
    private $pass = 'gA6C8yQy';
    private $charset = 'utf8mb4';
    /**
     * @var PDO
     */
    private $pdo;
	
	
	public function init()
    {
        
        $this->setPdo();
        
        $rawData = json_decode(file_get_contents('php://input'), true);
        
        
        $this->router($rawData);
       
        return true;
    }
    
    private function router($data)
    {
        
        $chat_id = $data['message']['chat']['id'];
        $text = $this->getText($data);
        
        // $mtt = $this->pdo->query("SELECT * FROM kindgardens");
        
        // while($item = $mtt->fetch()){
        // 	$this->botApiQuery("sendMessage", [
	       //    'chat_id' => $item['telegram_user_id'],
	       //    'text' => "Ассалому Алайкум! Сиздан илтимос эртанги кун овқатлари учун боғчангиз болалар сонини киритинг.",
	       //    'parse_mode' => 'markdown'
	       // ]);	
        // }
        
        // file_get_contents($this->path.$this->token."/sendmessage?chat_id=640892021&text=Юборган маълумотингиз топилмади");
   
        if (array_key_exists("message", $data)) {
        	
        	if (array_key_exists("text", $data['message'])) {
        		if($text == "/start") {
        			$this->startBot($chat_id, $data);
        		}
        		elseif($text[0] == "@") {
        			$this->password($chat_id, $data);
        		}
        		else{
        			file_get_contents($this->path.$this->token."/sendmessage?chat_id=".$chat_id."&text=Юборган маълумотингиз топилмади");
        		}
        		
        	}
        	elseif (array_key_exists("photo", $data['message'])) {
        		
        	}
        	else {
        		file_get_contents($this->path.$this->token."/sendmessage?chat_id=".$chat_id."&text=Илтимос бизга файл юборманг.");
        	}
        }
        elseif(array_key_exists("callback_query", $data)){
            $func_param = explode("_", $text);
            $func = $func_param[0];
        	$chat_id2 = $data['callback_query']['message']['chat']['id'];
        	$mid = $data['callback_query']['message']['message_id'];
            $this->$func($data['callback_query'], $chat_id2, $mid);
        }
        else {
        	file_get_contents($this->path.$this->token."/sendmessage?chat_id=".$chat_id."&text=Илтимос бизга файл юборманг.");
        }
        
        return true;
    }
    
    private function startBot($chat_id, $data)
    {
        
        $user = $this->pdo->prepare("SELECT * FROM people WHERE telegram_id = :user_id");
        $user->execute(['user_id' => $chat_id]);
        
        $text = "Nishon invest MCHJнинг телеграм ботига ҳуш келибсиз!";
        
        
        if ($user->rowCount() == 0) {
        	$newUser = $this->pdo->prepare("INSERT INTO people SET kingar_id = :kingarid, telegram_id = :user_id, telegram_name = :tl_name, telegram_password = :password, childs_count = :childs_count");
	        $newUser->execute([
	        	'kingar_id' => 0,
                'shop_id' => -1,
	            'telegram_id' => $chat_id,
	            'telegram_name' =>  $data['message']['chat']['first_name'] . ' ' . $data['message']['chat']['last_name'],
	            'telegram_password' => 123,
	            'childs_count' => '0_0_0'
	        ]);
        	
        	$this->botApiQuery("sendMessage", [
	           'chat_id' => $chat_id,
	           'text' => $text,
	           'parse_mode' => 'html'
	        ]);    
        }
        
    }
	
	private function ClickGarden($data, $chat_id2, $mid){
        $obj = $data['data'];
        $param = explode("_", $obj);
        
        
        
        $this->editMessageText($chat_id2, $mid, "Шу боғча учун паролни киритинг.");
     
	}
	
	private function password($chat_id, $data)
    {
    	$text = $this->getText($data);
    	
    	$user = $this->pdo->prepare("SELECT * FROM people WHERE telegram_id = :user_id");
        $user->execute(['user_id' => $chat_id]);
        $user = $user->fetch();
        
    	if($text == $user['telegram_password']){
    		$up_product = $this->pdo->prepare("UPDATE kindgardens SET telegram_user_id = :TGID WHERE id = :id");
    		$up_product->execute(['TGID' => $user['telegram_id'], 'id' => $user['kingar_id']]);
    	
    		$this->botApiQuery("sendMessage", [
	           'chat_id' => $chat_id,
	           'text' => "Шу боғча учун ҳар кунлик маълумотларни юборувчи ходим сифатида сизни белгиладик",
	           'parse_mode' => 'html'
	        ]);
    	}
    	else{
    		$this->botApiQuery("sendMessage", [
	           'chat_id' => $chat_id,
	           'text' => "Қайта уруниб кўринг!",
	           'parse_mode' => 'html'
	        ]);
    	}
    	
    }
    
    private function addnumber($data, $chat_id2, $mid){
    	
    	$param = explode("_", $data['data']);
    	
    	$user = $this->pdo->prepare("SELECT * FROM people WHERE telegram_id = :user_id");
        $user->execute(['user_id' => $chat_id2]);
        
    	$buttons[] = [
    		$this->buildInlineKeyBoardButton("1", "addnumber_1"),
    		$this->buildInlineKeyBoardButton("2", "addnumber_2"),
    		$this->buildInlineKeyBoardButton("3", "addnumber_3")
    	];
    	$buttons[] = [
    		$this->buildInlineKeyBoardButton("4", "addnumber_4"),
    		$this->buildInlineKeyBoardButton("5", "addnumber_5"),
    		$this->buildInlineKeyBoardButton("6", "addnumber_6")
    	];
    	$buttons[] = [
    		$this->buildInlineKeyBoardButton("7", "addnumber_7"),
    		$this->buildInlineKeyBoardButton("8", "addnumber_8"),
    		$this->buildInlineKeyBoardButton("9", "addnumber_9")
    	];
    	$buttons[] = [
    		$this->buildInlineKeyBoardButton("0", "addnumber_0"),
    		$this->buildInlineKeyBoardButton("<", "addnumber_<")
    	];
    	
    	$obj = $user->fetch()['childs_count'];
    	$count = explode("_", $obj);
    	
    	$ok = "  ";
    	$textcount1 = "";
    	$textcount2 = "";
    	
    	if(count($count) == 1){
 
    		$updateSql = $this->pdo->prepare("UPDATE people SET childs_count = :childs_count  WHERE telegram_id = :telegram_id");
        	
        	$ok = "                             .";
        	
        	if($param[1] != '@'){
        		if($param[1] == '<'){
					$textcount1 = substr($count[0], 0, -1);
				}
				else{
        			$textcount1 = $count[0].$param[1];
				}
        		$textcount1 = (int)$textcount1;
        		$buttons[] = [
		    		$this->buildInlineKeyBoardButton("4-7 ёшгача", "addnumber_@")
		    	];
        	}
        	else{
        		$textcount1 = $count[0]."_0";
        		$ok = " <b>4-7 ёшгача: = ?</b>";
        	}
        	
	        if (!$updateSql->execute([
	            'childs_count' => $textcount1,
	            'telegram_id' => $chat_id2
	        ])) {
	            $this->notice($data['id'], "xato", true);
	        }
    	}
		
		if(count($count) == 2){
			$buttons[] = [
	    		$this->buildInlineKeyBoardButton("Юбориш", "childnumbertotemp_ok")
	    	];
			$textcount1 = $count[0];
			
			if($param[1] == '<'){
				$textcount2 = substr($count[1], 0, -1);
			}
			else{
				$textcount2 = $count[1].$param[1];	
			}
    		
    		$ok = " <b>4-7 ёшгача: = </b>".(int)$textcount2;
    		
    		$updateSql = $this->pdo->prepare("UPDATE people SET childs_count = :childs_count  WHERE telegram_id = :telegram_id");
          
	        if (!$updateSql->execute([
	            'childs_count' => $count[0]."_".(int)$textcount2,
	            'telegram_id' => $chat_id2
	        ])) {
	            $this->notice($data['id'], "xato", true);
	        }
		}
    	
    	
    	$this->editMessageText($chat_id2, $mid, "<b>3 ёшгача = </b> ".(int)$textcount1." ".$ok, $buttons);	
    }
    
    private function childnumbertotemp($data, $chat_id2, $mid){
    	$user = $this->pdo->prepare("SELECT * FROM people WHERE telegram_id = :user_id");
        $user->execute(['user_id' => $chat_id2]);
        $row = $user->fetch();
        $param = explode("_", $row['childs_count']);
    	
        // $newUser = $this->pdo->query("INSERT INTO temporaries SET kingar_name_id = :kingarid, age_id = :user_id,  age_number = :childs_count");
        // $newUser->execute(['kingarid' => $user['kingar_id'],  'user_id' =>1, 'childs_count' => $param[0]]);
        // $newUser = $this->pdo->prepare("INSERT INTO temporaries SET kingar_name_id = 4, age_id = 2,  age_number = 5");
        // $newUser->execute(['kingarid' => $user['kingar_id'],  'user_id' => 2, 'childs_count' => $param[1]]);
        
        file_get_contents("https://cj56359.tmweb.ru/gow/?bogcha=".$row['kingar_id']."&yoshi=2&soni=".$param[0]);
        file_get_contents("https://cj56359.tmweb.ru/gow/?bogcha=".$row['kingar_id']."&yoshi=1&soni=".$param[1]);
        
        $this->editMessageText($chat_id2, $mid, "Болалар сони 3-4 ёшгача = ".$param[0]." та ва 4-7 ёшгача = ".$param[1]." та. Маълумотлар юборилди, тез орада менюни оласиз!" );
        
    }
    //////////////////////////////////
    // Вспомогательные методы
    //////////////////////////////////
    /**
     *  Создаем соединение с БД
     */
    private function setPdo()
    {
        // задаем тип БД, хост, имя базы данных и чарсет
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        // дополнительные опции
        $opt = [
            // способ обработки ошибок - режим исключений
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // тип получаемого результата по-умолчанию - ассоциативный массив
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // отключаем эмуляцию подготовленных запросов
            PDO::ATTR_EMULATE_PREPARES => false,
            // определяем кодировку запросов
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        // записываем объект PDO в свойство $this->pdo
        $this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
    }

    /** проверяем на админа
     * @param $chat_id
     * @return bool
     */
    private function isAdmin($chat_id)
    {

        $admins = $this->pdo->query('SELECT * FROM bot_shop_admins');
        // проверяем на количество категорий
        if ($admins->rowCount() > 0) {
            // проходим циклом по полученным данным из базы
            while ($row = $admins->fetch()) {
                if($row['user_id'] == $chat_id){
                    return 1;
                }
            }
        } 

        return 0;
        
    }

    private function getChatId($data)
    {
        if ($this->getType($data) == "callback_query") {
            return $data['callback_query']['message']['chat']['id'];
        }
        return $data['message']['chat']['id'];
    }

    /** Получаем id сообщения
     * @param $data
     * @return mixed
     */
    private function getMessageId($data)
    {
        if ($this->getType($data) == "callback_query") {
            return $data['callback_query']['message']['message_id'];
        }
        return $data['message']['message_id'];
    }

    private function getLongitude($data)
    {
        if ($this->getType($data) == "callback_query") {
            return $data['callback_query']['message']['location']['longitude'];
        }
        return $data['message']['location']['longitude'];
    }

    /** получим значение текст
     * @return mixed
     */
    private function getText($data)
    {
        if ($this->getType($data) == "callback_query") {
            return $data['callback_query']['data'];
        }
        return $data['message']['text'];
    }

    /** Узнаем какой тип данных пришел
     * @param $data
     * @return bool|string
     */
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
        return $this->botApiQuery("sendMessage", $data_send);
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
        return $this->botApiQuery("editMessageText", $data_send);
    }


    /** Уведомление в клиенте
     * @param $cbq_id
     * @param $text
     * @param bool $type
     */
    private function notice($cbq_id, $text = "", $type = false)
    {
        $data = [
            'callback_query_id' => $cbq_id,
            'show_alert' => $type,
        ];

        if (!empty($text)) {
            $data['text'] = $text;
        }

        $this->botApiQuery("answerCallbackQuery", $data);
    }

    /** Запрос к BotApi
     * @param $method
     * @param array $fields
     * @return mixed
     */
    private function botApiQuery($method, $fields = array())
    {
        $ch = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);
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

// file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Ассалому Алайкум! Сиздан илтимос эртанги кун овқатлари учун боғчангиз болалар сонини киритинг.");
// file_get_contents($path."/sendPhoto?chat_id=".$chatId."&photo=https://abror-a.uz/svmart/img/1614323545.jpg&caption=Yoqimli Ishtaxa");
?>