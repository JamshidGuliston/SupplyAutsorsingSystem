<?php

namespace App\Services\ChildrenCount;

use App\Exceptions\ChildrenCount\AlreadySubmittedTodayException;
use App\Exceptions\ChildrenCount\IncompleteSubmissionException;
use App\Exceptions\ChildrenCount\InvalidAgeForKindgardenException;
use App\Exceptions\ChildrenCount\KindgardenNotAssignedException;
use App\Exceptions\ChildrenCount\NextdayNotReadyException;
use App\Exceptions\ChildrenCount\TimeWindowClosedException;
use App\Models\ChildrenCountHistory;
use App\Models\Kindgarden;
use App\Models\Nextday_namber;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChildrenCountService
{
    public function __construct(private ChildrenCountWindow $window) {}

    /**
     * @return array{
     *   kindgarden: array{id:int, kingar_name:string},
     *   age_ranges: array<int, array{id:int, age_name:string}>,
     *   submitted: bool,
     *   today_counts: array<int,int>|null,
     *   submitted_at: string|null,
     *   time_window: array{allowed:bool, from:string, to:string, current_tashkent:string},
     *   nextday_ready: bool
     * }
     */
    public function getTodayState(User $user): array
    {
        $kindgarden = $this->resolveKindgarden($user);
        $kindgarden->load('age_range');
        $ageRanges = $kindgarden->age_range->map(fn ($a) => [
            'id' => (int) $a->id,
            'age_name' => (string) $a->age_name,
        ])->values()->all();

        $now = Carbon::now();
        $lastHistory = $this->lastSubmissionHistory($kindgarden->id);
        $lastAt = $lastHistory ? Carbon::parse($lastHistory->changed_at) : null;
        $submitted = $this->window->isInCooldown($lastAt, $now);

        $todayCounts = null;
        if ($submitted) {
            $todayCounts = Nextday_namber::where('kingar_name_id', $kindgarden->id)
                ->get(['king_age_name_id', 'kingar_children_number'])
                ->mapWithKeys(fn ($r) => [(int) $r->king_age_name_id => (int) $r->kingar_children_number])
                ->all();
        }

        $nextdayReady = Nextday_namber::where('kingar_name_id', $kindgarden->id)->exists();

        $tashkent = $now->copy()->setTimezone(ChildrenCountWindow::TIMEZONE);

        return [
            'kindgarden' => [
                'id' => (int) $kindgarden->id,
                'kingar_name' => (string) $kindgarden->kingar_name,
            ],
            'age_ranges' => $ageRanges,
            'submitted' => $submitted,
            'today_counts' => $todayCounts,
            'submitted_at' => $submitted && $lastAt ? $lastAt->copy()->utc()->toIso8601String() : null,
            'time_window' => [
                'allowed' => $this->window->isAllowedNow($now),
                'from' => sprintf('%02d:00', ChildrenCountWindow::OPEN_HOUR),
                'to' => sprintf('%02d:00', ChildrenCountWindow::CLOSE_HOUR),
                'current_tashkent' => $tashkent->format('H:i'),
            ],
            'nextday_ready' => $nextdayReady,
        ];
    }

    /**
     * @param array<int,int> $counts  age_id => count
     * @return array  same shape as getTodayState() after submission
     */
    public function submit(User $user, array $counts): array
    {
        $kindgarden = $this->resolveKindgarden($user);
        $kindgarden->load('age_range');

        $now = Carbon::now();
        if (!$this->window->isAllowedNow($now)) {
            throw new TimeWindowClosedException();
        }

        $lastHistory = $this->lastSubmissionHistory($kindgarden->id);
        $lastAt = $lastHistory ? Carbon::parse($lastHistory->changed_at) : null;
        if ($this->window->isInCooldown($lastAt, $now)) {
            throw new AlreadySubmittedTodayException();
        }

        $nextdayRows = Nextday_namber::where('kingar_name_id', $kindgarden->id)->get();
        if ($nextdayRows->isEmpty()) {
            throw new NextdayNotReadyException();
        }

        $allowedAgeIds = $kindgarden->age_range->pluck('id')->map(fn ($i) => (int) $i)->all();
        $submittedAgeIds = array_map('intval', array_keys($counts));
        foreach ($submittedAgeIds as $ageId) {
            if (!in_array($ageId, $allowedAgeIds, true)) {
                throw new InvalidAgeForKindgardenException($ageId);
            }
        }
        $missing = array_values(array_diff($allowedAgeIds, $submittedAgeIds));
        if (!empty($missing)) {
            throw new IncompleteSubmissionException($missing);
        }

        DB::transaction(function () use ($kindgarden, $counts, $nextdayRows, $user, $now) {
            foreach ($counts as $ageId => $newValue) {
                $current = $nextdayRows->firstWhere('king_age_name_id', (int) $ageId);
                if (!$current) {
                    throw new NextdayNotReadyException();
                }
                $oldValue = (int) $current->kingar_children_number;

                ChildrenCountHistory::create([
                    'kingar_name_id' => $kindgarden->id,
                    'king_age_name_id' => (int) $ageId,
                    'old_children_count' => $oldValue,
                    'new_children_count' => (int) $newValue,
                    'changed_by' => $user->id,
                    'changed_at' => $now,
                    'change_reason' => 'Oshpaz mobil ilova orqali kunlik bolalar soni yuborildi',
                ]);

                Notification::createChildrenCountChangeNotification(
                    $kindgarden->id,
                    (int) $ageId,
                    $oldValue,
                    (int) $newValue,
                    $user->id
                );

                $current->update(['kingar_children_number' => (int) $newValue]);
            }
        });

        return $this->getTodayState($user->fresh());
    }

    private function resolveKindgarden(User $user): Kindgarden
    {
        $kg = $user->kindgarden()->first();
        if (!$kg) {
            throw new KindgardenNotAssignedException();
        }
        return $kg;
    }

    private function lastSubmissionHistory(int $kindgardenId): ?ChildrenCountHistory
    {
        return ChildrenCountHistory::where('kingar_name_id', $kindgardenId)
            ->orderByDesc('changed_at')
            ->first();
    }
}
