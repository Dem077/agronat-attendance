<?php

namespace App\Jobs;

use App\Services\AttendanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    protected $from;
    protected $to;
    protected $userIds;
    protected $user_id;
    protected $in;
    protected $out;
    protected $progressKey;
    protected $lateGraceMinutes;

    public function __construct($from, $to, $userIds, $in, $out, $progressKey = null, $lateGraceMinutes = null)
    {
        $this->onQueue('recompute');
        $this->from = $from;
        $this->to = $to;
        $this->userIds = is_array($userIds) ? $userIds : [$userIds];
        $this->in = $in;
        $this->out = $out;
        $this->progressKey = $progressKey;
        $this->lateGraceMinutes = $lateGraceMinutes;
    }

    public function handle()
    {
        $attendanceService = new AttendanceService(
            ['in' => $this->in, 'out' => $this->out],
            $this->lateGraceMinutes
        );

        foreach ($this->userIdsForProcessing() as $userId) {
            $attendanceService->recompute($this->from, $this->to, $userId);

            if ($this->progressKey) {
                cache()->increment($this->progressKey);
            }
        }
    }

    protected function userIdsForProcessing(): array
    {
        if (! empty($this->userIds)) {
            return $this->userIds;
        }

        if (! empty($this->user_id)) {
            return [$this->user_id];
        }

        return [];
    }
}
