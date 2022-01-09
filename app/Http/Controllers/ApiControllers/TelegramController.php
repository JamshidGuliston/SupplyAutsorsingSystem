<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;

class TelegramController extends Controller
{
    
    public function telegrambot(){
        $content = file_get_contents("php://input");
        $data = json_decode($content, true);

        if(isset($data['callback_query']))
            $data = $data['callback_query'];
        if(isset($data['message']))
            $data = $data['message'];

        $message = mb_strtolower(($data['text'] ? $data['text']
            : $data['data']) , 'utf-8' );
        $method = 'sendMessage';
        switch ($message){
            case '/start':
                $send_data = [
                    'text'=>'Hi'
                ];
                break;
            default:
                $send_data = [
                    'text'=>'Try another text'
                ];
        }
        $send_data['chat_id']=$data['chat']['id'];
        return $this->sendTelegram($method,$send_data);
    }
    
    private function sendTelegram($method,$data,$headers=[]){
        $handle = curl_init('https://api.telegram.org/bot1249266736:AAHO9TC8CilXjkMpcbTjEk2vBNADRmUeTLM'.'/'.$method);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($handle, CURLOPT_HTTPHEADER,
            array_merge( array("Content-Type: application/json"),
                $headers ) );
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);

        $result = curl_exec($handle);
        curl_close($handle);
        return ( json_decode($result,1) ? json_decode($result,1) : $result);
    }
}
