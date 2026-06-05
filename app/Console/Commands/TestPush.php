<?php

namespace App\Console\Commands;

use App\Services\Push\PushService;
use Illuminate\Console\Command;

class TestPush extends Command
{
    protected $signature = 'chef:test-push {user_id : Target user ID}';
    protected $description = 'Send a test push notification to all FCM devices of the given user';

    public function handle(PushService $push): int
    {
        $userId = (int) $this->argument('user_id');
        $result = $push->sendToUser(
            $userId,
            'Test xabari',
            'ChefMobile push tizimidan test xabar (' . now()->setTimezone('Asia/Tashkent')->format('H:i') . ')',
            ['kind' => 'test']
        );
        $this->info(json_encode($result));
        return self::SUCCESS;
    }
}
