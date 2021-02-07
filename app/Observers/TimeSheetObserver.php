<?php

namespace App\Observers;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\TimeSheet;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Log;

class TimeSheetObserver
{
    private $attendanceService;
    public function __construct()
    {
        $this->attendanceService=new AttendanceService();
    }
    /**
     * Handle the TimeSheet "created" event.
     *
     * @param  \App\Models\TimeSheet  $timeSheet
     * @return void
     */
    public function created(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Handle the TimeSheet "updated" event.
     *
     * @param  \App\Models\TimeSheet  $timeSheet
     * @return void
     */
    public function updated(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Handle the TimeSheet "deleted" event.
     *
     * @param  \App\Models\TimeSheet  $timeSheet
     * @return void
     */
    public function deleted(TimeSheet $timeSheet)
    {
        
        $from=date('Y-m-d',strtotime($timeSheet->punch));
        $to=date('Y-m-d',strtotime($timeSheet->punch." +1 day"));

        $this->attendanceService->recompute($from,$to,[$timeSheet->user_id]);

    }

    /**
     * Handle the TimeSheet "restored" event.
     *
     * @param  \App\Models\TimeSheet  $timeSheet
     * @return void
     */
    public function restored(TimeSheet $timeSheet)
    {
        //
    }

    /**
     * Handle the TimeSheet "force deleted" event.
     *
     * @param  \App\Models\TimeSheet  $timeSheet
     * @return void
     */
    public function forceDeleted(TimeSheet $timeSheet)
    {
        //
    }
}
