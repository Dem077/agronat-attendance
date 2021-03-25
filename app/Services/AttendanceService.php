<?php

namespace App\Services;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
use App\Jobs\UpdateAttendanceStatus;
use App\Jobs\ZKTSync;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\TimeSheet;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class AttendanceService{

    private $schedule;

    public function __construct()
    {
        $this->schedule=[
            "in"=>date('H:i:s',strtotime('08:00')),
            "out"=>date('H:i:s',strtotime('14:00'))
        ];
    }

    public function addLog($data){
        //check user time
        $punch=date('Y-m-d H:i:s',strtotime($data['punch']));
        $date=date('Y-m-d',strtotime($data['punch']));
        $day_start=new DateTime($date);
        $day_end=new DateTime($date." 23:59:59");
        $user_id=$data['user_id'];
        $timeLog=null;

        $fix=TimeSheet::withTrashed()->where('punch','>=',$data['punch'])->where('punch','<',$day_end)->where('user_id',$user_id)->pluck('punch')->toArray();

        if(in_array($punch,$fix)){
            return;
        }
        if($fix){
            $timeLog=TimeSheet::create($data);
            $this->fixTimeSheet($day_start,$user_id);
            $this->resetDailyAttendance($day_start,$user_id);
            $this->resetDailyOT($day_start,$user_id);
        }else{
            $count=TimeSheet::where('punch','>=',$day_start)->where('punch','<=',$data['punch'])->where('user_id',$user_id)->count();
            $data['status']=$count%2;
            $timeLog=TimeSheet::create($data);

            $this->addAttendance($timeLog);
            $this->addOT($timeLog);
 
        }

        return $timeLog;
    }

    public function fixTimeSheet($date,$user_id){
        $from=$date;
        $to=(clone $from)->modify('+1 day');
        $data['status']=1;
        $records=TimeSheet::where('punch','>=',$from)->where('punch','<',$to)->where('user_id',$user_id)->orderBy('punch','asc')->get();
        $status=1;
        foreach($records as $record){
            $status=($status+1)%2;
            $record->status=$status;
            $record->save();
        }
    }

    public function recompute($from,$to,$user_id){
        foreach(date_range($from,$to) as $date){
            $this->deleteOT($date,$user_id);
            $this->addSchedule($date,$user_id);
            $this->fixTimeSheet($date,$user_id);
            $time_logs=TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00'))
                ->where('punch','<=',$date->format('Y-m-d 23:59:59'))
                ->where('user_id',$user_id)
                ->orderBy('punch','asc')
                ->get();

            foreach($time_logs as $time_log){
                $this->addAttendance($time_log);
                $this->addOT($time_log);
            }
            //TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00:00'))->where('punch','<=',$date->format('Y-m-d 23:59:59'))->where('sync','1')->where('user_id',$user_id)->delete();
            //ZKTSync::dispatchNow(['from'=>$date->format('Y-m-d'),'to'=>$date->format('Y-m-d 23:59:59'),'user_id'=>$user_id]);
        }

        UpdateAttendanceStatus::dispatchNow(['from'=>$from,'to'=>$to,'user_id'=>$user_id]);

    }

    public function addAttendance($log){
        $dt=strtotime($log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$log->user_id;

        $attendable=[Null,"","Absent","Late","Normal"];

        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();
        
        if(!$attendance){
            $this->addSchedule(new DateTime($date),$user_id);
            return $this->addAttendance($log);
        }
        if(!in_array($attendance->status,$attendable)){
            return;
        }
        
        if($attendance){
            $schedule=[
                "in"=>$attendance->sc_in,
                "out"=>$attendance->sc_out
            ];
            if($log->status==0){
                if(!$attendance->in){
                    $attendance->in=$time;
                    $attendance->late_min=$this->lateFine($time,$schedule['in']);
                    $attendance->status=$attendance->late_min>0?'Late':'Normal';
                    $attendance->save();
                }

            }else{
                $attendance->out = $time;
                $attendance->save();
            }

            UpdateAttendanceStatus::dispatchNow(['from'=>$date,'user_id'=>$user_id]);

        }else{
            $late_min=$this->lateFine($time,$this->schedule['in']);
            $status=$late_min>0?'Late':'Normal';
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$this->schedule['in'],'sc_out'=>$this->schedule['out'],'in'=>$time,'late_min'=>$late_min,'status'=>$status]);
        }



    }

    public function lateFine($ck_in,$sc_in){
        Log::info(['ck_in'=>$ck_in,'sc_in'=>$sc_in]);
        $sc_in=strtotime($sc_in);
        $ck_in=strtotime($ck_in);
        $late_min=floor(($ck_in-$sc_in)/60);
        return $late_min>0?$late_min:0;
    }

    public function resetAttendance($from,$to,$user_id){
        foreach(date_range($from,$to) as $date){
            $this->fixTimeSheet($date,$user_id);
            $this->resetDailyAttendance($date,$user_id);
            $this->resetDailyOT($date,$user_id);
        }
    }

    private function resetDailyAttendance($date,$user_id){
        $attendance=Attendance::where('ck_date','=',$date)
                                ->where('user_id',$user_id)
                                ->whereIn('status',['Normal','Late',Null])
                                ->first();
        if(!$attendance){
            return;
        }
        $attendance->in=Null;
        $attendance->out=Null;
        $attendance->status=Null;
        $attendance->save();
        
        $time_logs=TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00'))
                            ->where('punch','<=',$date->format('Y-m-d 23:59:59'))
                            ->where('user_id',$user_id)
                            ->orderBy('punch','asc')
                            ->get();

        foreach($time_logs as $time_log){
            $this->addAttendance($time_log);
        }

    }

    public function resetDailyOT($date,$user_id){
        Overtime::where('ck_date','=',$date)
                                ->where('user_id',$user_id)
                                ->delete();
        
        $time_logs=TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00'))
                            ->where('punch','<=',$date->format('Y-m-d 23:59:59'))
                            ->where('user_id',$user_id)
                            ->orderBy('punch','asc')
                            ->get();

        foreach($time_logs as $time_log){
            $this->addOT($time_log);
        }
    }

    public function addSchedule($date,$user_id){
        //store all holidays in the range for fast processing
        Attendance::where('ck_date',$date)->where('user_id',$user_id)->delete();
        $holidays=Holiday::where('h_date',$date)
                        ->pluck('h_date')->toArray();
        //loop range
        if(in_array($date->format('Y-m-d'),$holidays)){
            //add holidays to each employee
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'status'=>'Holiday']);
        }else{
            $leave=Leave::with('type')->where('user_id',$user_id)
                        ->where('from','<=',$date)
                        ->where('to','>=',$date)
                        ->first();
            if($leave){
                Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'status'=>$leave->type->title]);
            }else{
                //get schedule for each employee
                Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$this->schedule['in'],'sc_out'=>$this->schedule['out']]);
            }
        }
    }

    private function deleteOT($date,$user_id){
        Overtime::where('ck_date',$date)->where('user_id',$user_id)->delete();
    }

    private function deleteAttendance($date,$user_id){
        Attendance::where('ck_date',$date)->where('user_id',$user_id)->delete();
    }

    public function addOT($timelog)
    {

        $dt=strtotime($timelog->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i',$dt);
        $user_id=$timelog->user_id;
        //process OT
        $attendable=Attendance::where('ck_date',$date)->where('user_id',$user_id)->first();
        if(!$attendable){
            return;
        }
        $attendable->sc_in=date('H:i',strtotime($attendable->sc_in));
        $attendable->sc_out=date('H:i',strtotime($attendable->sc_out));

        if(in_array($attendable->status,['Normal','Late'])){
            $this->weekdayOT($timelog,$attendable);
        }elseif(in_array($attendable->status,['Holiday'])){
            $this->holidayOT($timelog);
        }
        
    }
    private function holidayOT($timelog){
        $dt=strtotime($timelog->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i',$dt);
        $user_id=$timelog->user_id;
        if($timelog->status==0){
            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
        }else{
            $last_ot=Overtime::where('user_id',$user_id)
                                ->where('ck_date',$date)
                                ->whereNull('out')->first();
            if($last_ot){
                $last_ot->out=$time;
                $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                $last_ot->save();
            }
        }
    }

    private function weekdayOT($timelog,$attendable){
        $dt=strtotime($timelog->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i',$dt);
        $user_id=$timelog->user_id;

        if($timelog->status==0){
            if($time < $attendable->sc_in || $time >= $attendable->sc_out){
                Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
            }
        }else{
            $last_ot=Overtime::where('user_id',$user_id)
                            ->where('ck_date',$date)
                            ->whereNull('out')->first();
            if($last_ot){
                $last_ot->in=date('H:i',strtotime($last_ot->in));
                if($time <= $attendable->sc_in){
                    $last_ot->out=$time;
                    $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                    $last_ot->save();

                }elseif($time > $attendable->sc_in){
                    if($time < $attendable->sc_out){
                        $last_ot->out=$attendable->sc_in;
                    }elseif($time > $attendable->sc_out){
                        if($last_ot->in < $attendable->sc_in){
                            $last_ot->out=$attendable->sc_in;

                            //split for after hour ot
                            $after_hour_ot=$this->calculateOT($attendable->sc_out,$time);
                            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$attendable->sc_out,'out'=>$time,'ot'=>$after_hour_ot]);
                        }elseif($last_ot->in > $attendable->sc_out){
                            $last_ot->out=$time;

                        }
                    }

                    if($last_ot->out){
                        $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                        $last_ot->save();
                    }
                }

            }elseif($time > $attendable->sc_out){
                $after_hour_ot=$this->calculateOT($attendable->sc_out,$time);
                Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$attendable->sc_out,'out'=>$time,'ot'=>$after_hour_ot]);
            }
        }
    }

    public function addOT0($timelog)
    {

        $dt=strtotime($timelog->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i',$dt);
        $user_id=$timelog->user_id;
        //process OT
        $attendable=Attendance::where('ck_date',$date)->where('user_id',$user_id)->first();
        if(!$attendable){
            return;
        }
        $attendable->sc_in=date('H:i',strtotime($attendable->sc_in));
        $attendable->sc_out=date('H:i',strtotime($attendable->sc_out));

        if(in_array($attendable->status,['Normal','Late'])){
            if($timelog->status==0){
                if($time < $attendable->sc_in || $time >= $attendable->sc_out){
                    Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
                }
            }else{
                $last_ot=Overtime::where('user_id',$user_id)
                            ->where('ck_date',$date)
                            ->whereNull('out')->first();
                
                if($last_ot){
                    $last_ot->in=date('H:i',strtotime($last_ot->in));
                    if($time <= $attendable->sc_in){
                        $last_ot->out=$time;
                        $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                        $last_ot->save();
                    }elseif($last_ot->in < $attendable->sc_in && $time > $attendable->sc_in){
                        if($time<=$attendable->sc_out){
                            $last_ot->out=$attendable->sc_in;
                            $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                            $last_ot->save();
                        }else{
                            $last_ot->out=$attendable->sc_in;
                            $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                            $last_ot->save();
                            //split for after hour ot
                            $after_hour_ot=$this->calculateOT($attendable->sc_out,$time);
                            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$attendable->sc_out,'out'=>$time,'ot'=>$after_hour_ot]);
                        }
                    }elseif($time > $attendable->sc_out){
                        $last_ot->out=$time;
                        $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                        $last_ot->save();
                    }

                }else{
                    $last_log=TimeSheet::where('user_id',$user_id)
                        ->where('punch','<',$timelog->punch)
                        ->where('punch','>',$date)->orderBy('punch','desc')->first();
                    if($last_log){
                        $log_log_time=date('H:i',strtotime($last_log->punch));
                        if($log_log_time<=$attendable->sc_out && $time>=$attendable->sc_out){
                            $after_hour_ot=$this->calculateOT($attendable->sc_out,$time);
                            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$attendable->sc_out,'out'=>$time,'ot'=>$after_hour_ot]);
                        }
                        elseif($log_log_time>=$attendable->sc_out){
                            $after_hour_ot=$this->calculateOT($log_log_time,$time);
                            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$log_log_time,'out'=>$time,'ot'=>$after_hour_ot]);
                        }
                    }

                }


            }
        }elseif(in_array($attendable->status,['Holiday'])){
            if($timelog->status==0){
                Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
            }else{
                $last_ot=Overtime::where('user_id',$user_id)
                            ->where('ck_date',$date)
                            ->whereNull('out')->first();
                if($last_ot){
                    $last_ot->out=$time;
                    $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                    $last_ot->save();
                }

            }
        }
        
    }

    public function calculateOT($in,$out){
        $in=strtotime($in);
        $out=strtotime($out);
        $ot=round(($out-$in)/60,2);
        return $ot;
    }

}