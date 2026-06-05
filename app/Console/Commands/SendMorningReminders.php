<?php

namespace App\Console\Commands;

use App\Constants\Roles;
use App\Services\Push\PushService;
use Illuminate\Console\Command;

class SendMorningReminders extends Command
{
    protected $signature = 'chef:morning-reminder';
    protected $description = 'Send morning push reminder to all chefs (08:00 Asia/Tashkent)';

    public function handle(PushService $push): int
    {
        $result = $push->sendToRole(
            Roles::CHEF,
            'Davomatni unutmang',
            'Bog\'chaga kelganingizda ChefMobile ilovasini oching va "Keldim" tugmasini bosing.',
            ['kind' => 'morning_reminder']
        );
        $this->info('Sent: ' . $result['success_count'] . ', invalid: ' . count($result['invalid_tokens']));
        return self::SUCCESS;
    }
}
