<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;

class TelegramController extends Controller
{
    use MakeComponents;
    use RequestTrait;
    public function webhook()
    {
        return $this->apiRequest('setWebhook', ['url' => url(route('webhook'))]) ? ['success'] : ['something wrong'];
    }

    public function index()
    {
        $result = json_decode(file_get_contents('php://input'));
        $action = $result->message->text;
        
    }
}
