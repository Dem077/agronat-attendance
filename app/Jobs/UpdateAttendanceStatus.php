<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\AttendStatus;
use App\Models\Holiday;
use App\Models\User;
use DateInterval;
use DatePeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAttendanceStatus implements ShouldQueue
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

        foreach ($period as $dt) {
            $this->updateAttendenceStatus($employees,$dt->format('Y-m-d'));
        }
    }

    private function updateAttendenceStatus($employees,$date){
        $is_holiday=Holiday::where('h_date',$date)->exists();
        foreach($employees as $employee){
            $att=Attendance::with('attend_status')->where('user_id',$employee->id)->where('ck_date',$date)->first();

            if($att){
                if(!$att->status){ 
                    if($is_holiday){
                        $att->status='Holiday';
                    }
                    elseif($att->attend_status){
                        $att->status=$att->attend_status->status;
                    }
                    elseif(!$att->in){
                        $att->status='Absent';
                    }
                    elseif($att->in>$att->sc_in){
                        $att->status='Late';
                    }
                    elseif($att->in<=$att->sc_in){
                        $att->status='Normal';
                    }else{
                        $att->status='Absent';
                    }
                    $att->save();
                }
            }
        }
    }
}
