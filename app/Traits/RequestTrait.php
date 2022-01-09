<?php

namespace App\Traits;

trait RequestTrait
{
    private function apiRequest($method, $parameters = [])
    {
        $url = 'https://api.telegram.org/bot'.env('TELEGRAM_TOKEN').'/'.$method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        $response = curl_exec($ch);
        if($response === false){
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        $response = json_decode(json_encode($ch), true);
        dd($response);
        if($response['ok'] == false)
        {
            return false;
        }
        $response = $response['result'];
        return $response;
    }
}

?>