<?php

namespace App\Observers;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\TimeSheet;
use Illuminate\Support\Facades\Log;

class TimeSheetObserver
{
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
        Log::info('delete 1');
        $date1=date('Y-m-d',strtotime($timeSheet->punch));
        $date2=date('Y-m-d',strtotime($timeSheet->punch." +1 day"));
        $user_id=$timeSheet->user_id;
        $log=null;

        $fix=TimeSheet::where('punch','>',$timeSheet->punch)->where('punch','<',$date2)->where('user_id',$user_id)->count();
        if($fix){

            Attendance::where('ck_date',$date1)->where('user_id',$user_id)->delete();
            Overtime::where('ck_date',$date1)->where('user_id',$user_id)->delete();

            $records=TimeSheet::whereBetween('punch',[$date1,$date2])->where('user_id',$user_id)->orderBy('punch','asc')->get();

            $status=1;
            foreach($records as $record){
                $status=($status+1)%2;
                $record->status=$status;
                $record->save();
    
                ProcessOvertime::dispatch($record);
                ProcessAttendance::dispatch($record);
            }
        }

        Log::info('delete 2');
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
