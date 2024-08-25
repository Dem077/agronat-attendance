<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Holiday;
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

        $dt=strtotime($this->punch_log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$this->punch_log->user_id;

        $is_holiday=Holiday::where('h_date',$date)->exists();
        $work_saturday=User::where('id',$user_id)
                                ->whereHas('department',function($q){
                                    $q->where('work_on_saturday',1);
                                })->exists() && date('D',$dt)=='Sat';
        if($is_holiday && !$work_saturday){
            return true;
        }
        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();

        
        if($attendance){
            $schedule=[
                "in"=>$attendance->sc_in,
                "out"=>$attendance->sc_out
            ];
            if($this->punch_log->status==0){
                if($attendance->in){
                    if($time <= $schedule['in'] && $attendance->in < $time){
                        $attendance->in=$time;
                        $attendance->late_min=$this->lateFine($time,$schedule['in'])>480?480:$this->lateFine($time,$schedule['in']);
                        $attendance->status=$attendance->late_min>0?'Late':'Normal';
                        $attendance->save();
                    }
                }else{
                    $attendance->in=$time;
                    $attendance->late_min=$this->lateFine($time,$schedule['in'])>480?480:$this->lateFine($time,$schedule['in']);
                    $attendance->status=$attendance->late_min>0?'Late':'Normal';
                    $attendance->save();
                }

            }else{
                if($time<=$schedule['in']){
                    $attendance->in=NULL;
                    $attendance->out=NULL;
                    $attendance->late_min=0;
                    $attendance->status=Null;
                    $attendance->save();
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
            $schedule=[
                "in"=>date('H:i:s',strtotime(env('SC_IN','08:00'))),
                "out"=>date('H:i:s',strtotime(env('SC_OUT','16:00')))
            ];
            if($work_saturday){
                $schedule=[
                    "in"=>date('H:i:s',strtotime(env('SC_SAT_IN','09:00'))),
                    "out"=>date('H:i:s',strtotime(env('SC_SAT_OUT','16:00')))
                ];
            }
            $late_min=$this->lateFine($time,$schedule['in'])>480?480:$this->lateFine($time,$schedule['in']);
            $status=$late_min>0?'Late':'Normal';
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out'],'in'=>$time,'late_min'=>$late_min,'status'=>$status]);
        }

    }

    public function lateFine($ck_in,$sc_in){
        $sc_in=strtotime($sc_in);
        $ck_in=strtotime($ck_in);
        $late_min=floor(($ck_in-$sc_in)/60);
        return $late_min>0?$late_min:0;
    }
    
}
