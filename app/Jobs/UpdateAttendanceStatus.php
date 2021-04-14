<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\AttendStatus;
use App\Models\Holiday;
use App\Models\Leave;
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
use Illuminate\Support\Facades\Log;

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
        $this->date_from=isset($data['from'])?new \DateTime($data['from']):new \DateTime();
        $this->date_to=isset($data['to'])?new \DateTime($data['to']):(clone $this->date_from)->modify('+1 day');
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
            $employees=[$this->user_id];
        }else{
            $employees=User::pluck('id')->toArray();
        }

        foreach (date_range($this->date_from,$this->date_to) as $dt) {
            $this->updateAttendenceStatus($employees,$dt);
        }
    }

    private function updateAttendenceStatus($employees,$date){
        $holidays=Holiday::where('h_date',$date)
                        ->pluck('h_date')->toArray();
        $today=new DateTime('+1 day');
        foreach($employees as $employee){
            $att=Attendance::where('user_id',$employee)->where('ck_date',$date)->first();

            if($att){
                //loop range
                if(in_array($date->format('Y-m-d'),$holidays)){
                    //add holidays to each employee
                    $att->status='Holiday';
                }else{
                    $leave=Leave::with('type')->where('user_id',$employee)
                                ->where('from','<=',$date)
                                ->where('to','>=',$date)
                                ->first();
                    if($leave){
                        $att->status=$leave->type->title;
                    }else{
                        //get schedule for each employee

                        if($date<$today){
                            $att->status='';
                        }
                        elseif(!$att->in){
                            $att->status='Absent';
                        }
                        elseif($att->late_min>0){
                            $att->status='Late';
                        }
                        elseif(new DateTime($att->in)<=new DateTime($att->sc_out)){
                            $att->status='Normal';
                        }else{
                            $att->status='';
                        }
                    }
                }
                $att->save();
            }

        }



    }
}
