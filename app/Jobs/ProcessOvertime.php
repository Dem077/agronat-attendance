<?php

namespace App\Jobs;

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


class ProcessOvertime implements ShouldQueue
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
            "in"=>date('H:i',strtotime('08:00')),
            "out"=>date('H:i',strtotime('16:00'))
        ];

        $dt=strtotime($this->punch_log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i',$dt);
        $user_id=$this->punch_log->user_id;

        //process OT

        if($this->punch_log->status==0){
            if($time < $schedule['in'] || $time >= $schedule['out']){
                Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
            }
        }else{
            $nxt=date('Y-m-d',strtotime($date." +1 day"));
            $last_ot=Overtime::where('user_id',1)
                        ->whereBetween('ck_date',[$date,$nxt])
                        ->whereNull('out')->first();
            if(!$last_ot){
                if($time > $schedule['out']){
                    Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$schedule['out'],'out'=>$time]);
                }
            }else{
                if($last_ot->in < $schedule['in']){
                    if($time >= $schedule['in']){
                        $last_ot->out=$schedule['in'];
                    }else{
                        $last_ot->out=$time;
                    }
                    $last_ot->save();
    
                    if($time > $schedule['out']){
                        Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$schedule['out'],'out'=>$time]);
                    }
                }elseif($last_ot->in >= $schedule['out']){
                    $last_ot->out=$time;
                    $last_ot->save();
                }
            }

        }
        
    }
}
