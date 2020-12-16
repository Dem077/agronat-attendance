<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Overtime;
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

    protected $punch_log;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($punch_log)
    {
        $this->punch_log=$punch_log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $schedule=[
            "in"=>date('H:i:s',strtotime('08:00')),
            "out"=>date('H:i:s',strtotime('16:00'))
        ];

        $dt=strtotime($this->punch_log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$this->punch_log->user_id;

        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();

        
        if($attendance){
            if($this->punch_log->status==0){
                if($time <= $schedule['in'] && $attendance->in < $time){
                    $attendance->in=$time;
                    $attendance->late_min=$this->lateFine($time,$schedule['in']);
                    $attendance->save();
                }

            }else{
                if($time<=$schedule['in']){
                    $attendance->delete();
                }elseif($attendance->out){
                    if($attendance->out < $schedule['out'] && $time >= $schedule['out']){
                        $attendance->out = $time;
                        $attendance->save();
                    }
                }elseif($time > $schedule['in']){
                    $attendance->out = $time;
                    $attendance->save();
                }

            }

        }else{
            $late_min=$this->lateFine($time,$schedule['in']);
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time,'late_min'=>$late_min]);
        }

    }

    public function lateFine($ck_in,$sc_in){
        $sc_in=strtotime($sc_in);
        $ck_in=strtotime($ck_in);
        $late_min=floor(($ck_in-$sc_in)/60);
        return $late_min>0?$late_min:0;
    }
    
}
