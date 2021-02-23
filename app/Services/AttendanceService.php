<?php

namespace App\Services;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
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
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class AttendanceService{

    public function addLog($data){
        //check user time
        $punch=date('Y-m-d H:i:s',strtotime($data['punch']));
        $date=date('Y-m-d',strtotime($data['punch']));
        $day_start=new DateTime($date);
        $day_end=new DateTime($date." 23:59:59");
        $user_id=$data['user_id'];
        $timeLog=null;

        Log::info(['from'=>$day_start,'to'=>$day_end,'user_id'=>$user_id]);

        $fix=TimeSheet::where('punch','>=',$data['punch'])->where('punch','<',$day_end)->where('user_id',$user_id)->pluck('punch')->toArray();

        Log::info([$punch,$fix]);

        if(in_array($punch,$fix)){
            return;
        }
        if($fix){
            Log::info(['from'=>$day_start,'to'=>$day_end,'fix'=>1]);
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
        Log::info(['date'=>$from,'to'=>$to]);
        foreach($records as $record){
            $status=($status+1)%2;
            Log::info(['date'=>$record->punch,'status'=>$status]);
            $record->status=$status;
            $record->save();
        }
    }

    public function recompute($from,$to,$user_id){
        foreach(date_range($from,$to) as $date){
            $this->deleteOT($date,$user_id);
            $this->addSchedule($date,$user_id);
            TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00:00'))->where('punch','<=',$date->format('Y-m-d 23:59:59'))->where('user_id',$user_id)->delete();
            ZKTSync::dispatchNow(['from'=>$date->format('Y-m-d'),'to'=>$date->format('Y-m-d 23:59:59'),'user_id'=>$user_id]);
        }
    }

    public function addAttendance($log){
        $dt=strtotime($log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$log->user_id;

        $attendable=[Null,"","Absent","Late","Normal"];

        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();
        
        if(!$attendance){
            throw new Exception("attendance entry not found at add Attendance {$date}");
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
        }else{
            $schedule=[
                "in"=>date('H:i:s',strtotime('08:00')),
                "out"=>date('H:i:s',strtotime('16:00'))
            ];
            $late_min=$this->lateFine($time,$schedule['in']);
            $status=$late_min>0?'Late':'Normal';
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out'],'in'=>$time,'late_min'=>$late_min,'status'=>$status]);
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

    private function resetDailyOT($date,$user_id){
        Overtime::where('ck_date','=',$date)
                                ->where('user_id',$user_id)
                                ->delete();
        
        $time_logs=TimeSheet::where('punch','>=',$date->format('Y-m-d 00:00'))
                            ->where('punch','<=',$date->format('Y-m-d 23:59:59'))
                            ->where('user_id',$user_id)
                            ->get();

        foreach($time_logs as $time_log){
            ProcessOvertime::dispatchNow($time_log);
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
                $schedule=[
                    "in"=>date('H:i:s',strtotime('08:00')),
                    "out"=>date('H:i:s',strtotime('16:00'))
                ];
                Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out']]);
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

        $schedule=[
            "in"=>date('H:i:s',strtotime('08:00')),
            "out"=>date('H:i:s',strtotime('16:00'))
        ];

        $dt=strtotime($timelog->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$timelog->user_id;
        //process OT
        $attendable=Attendance::where('ck_date',$date)->where('user_id',$user_id)->first();
        if(!$attendable){
            return;
        }
        if(in_array($attendable->status,['Normal','Late'])){
            if($timelog->status==0){
                if($time < $schedule['in'] || $time >= $schedule['out']){
                    Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$time]);
                }
            }else{
                $last_ot=Overtime::where('user_id',$user_id)
                            ->where('ck_date',$date)
                            ->whereNull('out')->first();
                if($last_ot){
                    if($last_ot->in < $schedule['in']){
                        if($time >= $schedule['in']){
                            $last_ot->out=$schedule['in'];
                        }else{
                            $last_ot->out=$time;
                        }
                        $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                        $last_ot->save();
        
                        if($time > $schedule['out']){
                            $ot=$this->calculateOT($schedule['out'],$time);
                            Overtime::create(['user_id'=>$user_id,'ck_date'=>$date,'in'=>$schedule['out'],'out'=>$time,'ot'=>$ot]);
                        }
                    }elseif($last_ot->in >= $schedule['out']){
                        $last_ot->out=$time;
                        $last_ot->ot=$this->calculateOT($last_ot->in,$last_ot->out);
                        $last_ot->save();
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