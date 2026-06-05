<?php

namespace App\Services\Push;

use App\Models\ChefDevice;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

class PushService
{
    public function __construct(
        private Messaging $messaging,
        private MulticastResultParser $parser,
    ) {}

    public function sendToDevices(Collection $devices, string $title, string $body, array $data = []): array
    {
        $tokens = $devices->pluck('fcm_token')->filter()->unique()->values()->all();
        if (empty($tokens)) {
            return ['success_count' => 0, 'failure_count' => 0, 'invalid_tokens' => []];
        }
        $message = CloudMessage::new()
            ->withNotification(['title' => $title, 'body' => $body])
            ->withData($data);
        try {
            $report = $this->messaging->sendMulticast($message, $tokens);
        } catch (Throwable $e) {
            \Log::error('FCM sendMulticast failed', ['error' => $e->getMessage(), 'token_count' => count($tokens)]);
            return ['success_count' => 0, 'failure_count' => count($tokens), 'invalid_tokens' => []];
        }
        $perTokenReports = [];
        foreach ($report->getItems() as $item) {
            $error = $item->error();
            $errorCode = null;
            if ($error !== null) {
                $errorCode = method_exists($error, 'errors')
                    ? ($error->errors()[0]['reason'] ?? $error->getMessage())
                    : $error->getMessage();
            }
            $perTokenReports[] = [
                'token' => $item->target()->value(),
                'success' => $item->isSuccess(),
                'errorCode' => $errorCode,
            ];
        }
        $classified = $this->parser->classify($perTokenReports, $tokens);
        if (!empty($classified['invalid_tokens'])) {
            $this->pruneInvalidTokens($classified['invalid_tokens']);
        }
        return $classified;
    }

    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $devices = ChefDevice::where('user_id', $userId)->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    public function sendToRole(int $roleId, string $title, string $body, array $data = []): array
    {
        $devices = ChefDevice::whereIn('user_id', function ($q) use ($roleId) {
            $q->select('id')->from('users')->where('role_id', $roleId);
        })->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    public function sendToChefsOfKindgarden(int $kindgardenId, string $title, string $body, array $data = []): array
    {
        // Pivot table verified from migration 2022_04_09_114934_create_user_kindgardens_table.php:
        // table = user_kindgardens, columns = user_id + kindgarden_id (NOT kingar_name_id).
        $devices = ChefDevice::whereIn('user_id', function ($q) use ($kindgardenId) {
            $q->select('user_id')
              ->from('user_kindgardens')
              ->where('kindgarden_id', $kindgardenId);
        })->get();
        return $this->sendToDevices($devices, $title, $body, $data);
    }

    private function pruneInvalidTokens(array $invalidTokens): void
    {
        if (empty($invalidTokens)) {
            return;
        }
        ChefDevice::whereIn('fcm_token', $invalidTokens)->delete();
    }
}
