<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $logs=TimeSheet::where('status',0)->orderBy('punch','asc')->get();
        
        $schedule=[
            "in"=>date('H:i',strtotime('08:00')),
            "out"=>date('H:i',strtotime('16:00'))
        ];

        $user_statuses=[];

        foreach(TimeSheet::where('status',0)->cursor() as $log){
            $dt=strtotime($log->punch);
            $date=date('Y-m-d',$dt);
            $time=date('H:i',$dt);
            $user_id=$log->user_id;

            if(!isset($user_statuses[$date])){
                $user_statuses[$date]=[];
                $nxt=date('Y-m-d',strtotime($date.' +1 day'));
                $user_statuses[$date][$user_id]=TimeSheet::where('status',0)->where(['user_id'=>$user_id])->whereBetween('punch',[$date,$nxt])->count();
            }

            if(!isset($user_statuses[$date][$user_id])){
                $user_statuses[$date][$user_id]=TimeSheet::where('status',0)->where(['user_id'=>$user_id])->whereBetween('punch',[$date,$nxt])->count();
            }

            $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();

            if($attendance){
                if($attendance->in < $time && $time <= $schedule['in']){
                    $attendance->in = $time;
                    $attendance->save();
                }elseif(!$attendance->out && $time >= $schedule['out']){
                    $attendance->out = $time;
                    $attendance->save();
                }
            }else{
                Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
            }

            $log->status=1;
            $log->save();
        }
        Log::info('test');
        //log get user schedule
        //if time <= schedule->in: 
            //if attendance->in: 
                //if attendance->in > time:
                    //attendance->in=time 
        //elseif(not attendance->in): 
            //attendance->in = time
        //elseif not attendance->out: 
            //if time >= schedule->out: 
                //attendance->out=time

        
    }
}
