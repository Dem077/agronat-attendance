<?php

namespace App\Jobs;

use App\Services\AttendanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecomputeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $from, $to, $user_id, $in, $out , $progressKey;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($from, $to, $user_id, $in, $out, $progressKey = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->user_id = $user_id;
        $this->in = $in;
        $this->out = $out;
        $this->progressKey = $progressKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $attendanceService = new AttendanceService(['in' => $this->in, 'out' => $this->out]);
        $attendanceService->recompute($this->from, $this->to, $this->user_id);

        // Update progress in cache
        if ($this->progressKey) {
            cache()->increment($this->progressKey);
        }
    }
}
