<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\Rules\Exists;

class AddSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date_from,$date_to,$user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->date_from=isset($data['from'])?new \DateTime($data['from']):(new \DateTime());
        $this->date_to=isset($data['to'])?new \DateTime($data['to']):($this->date_from)->modify('+1 day');
        $this->user_id=isset($data['user_id'])?$data['user_id']:null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->user_id){
            $user=new User();
            $user->id=$this->user_id;
            $employees=[$user];
        }else{
            $employees=User::select('id')->get();
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($this->date_from, $interval, $this->date_to);

        foreach($period as $dt) {
            $this->addSchedule($employees,$dt->format('Y-m-d'));
        }
        
    }

    private function addSchedule($employees,$date){
        if(in_array(date('D',strtotime($date)),['Sat','Fri'])){
            Holiday::updateOrCreate([
                'h_date'=>$date,
                'description'=>'Holiday'
            ]);
        }
        $is_holiday=Holiday::where('h_date',$date)->exists();


        foreach($employees as $employee){
            $work_saturday=User::where('id',$employee->id)
                                ->whereHas('department',function($q){
                                    $q->where('work_on_saturday',1);
                                })->exists() && date('D',strtotime($date))=='Sat';
            if($is_holiday && !$work_saturday){
                Attendance::create(['user_id'=>$employee->id,'ck_date'=>$date,'status'=>'Holiday']);
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
                if(!Attendance::where('user_id',$employee->id)->where('ck_date',$date)->exists()){
                    Attendance::create(['user_id'=>$employee->id,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out']]);
                }
            }
        }

    }


}
