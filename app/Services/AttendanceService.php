<?php

namespace App\Services;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Overtime;
use App\Models\TimeSheet;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class AttendanceService{

    public function processOvertime(){

    }

    public function processAttendance(){

    }

    public function addLog($data){
        //check user time

        $date1=date('Y-m-d',strtotime($data['punch']));
        $date2=date('Y-m-d',strtotime($data['punch']."+1 day"));
        $user_id=$data['user_id'];
        $log=null;

        Log::info(['from'=>$date1,'to'=>$date2,'user_id'=>$user_id]);

        $fix=TimeSheet::where('punch','>',$data['punch'])->where('punch','<',$date2)->where('user_id',$user_id)->exists();

        if($fix){
            Log::info(['from'=>$date1,'to'=>$date2,'fix'=>1]);
            $log=TimeSheet::create($data);
            $this->fixTimeSheet($date1,$user_id);
            $this->recompute($date1,$date1,[$user_id]);
        }else{
            $count=TimeSheet::where('punch','>=',$date1)->where('punch','<=',$data['punch'])->where('user_id',$user_id)->count();
            $data['status']=$count%2;
            $log=TimeSheet::create($data);

            ProcessOvertime::dispatch($log);
            ProcessAttendance::dispatch($log);  
        }

        return $log;
    }

    public function timeReorder($from,$to,$users){
        $interval = DateInterval::createFromDateString('1 day');
        $from=new DateTime($from);
        $to=new DateTime($to);
        $period = new DatePeriod($from, $interval,$to->modify('+1 day'));

        foreach($period as $date) {
            foreach($users as $user){
                $this->fixTimeSheet($date,$user);
            }
        }
    }

    public function fixTimeSheet($date,$user_id){
        $from=$date;
        $to=$date." 23:59:59";
        $data['status']=1;
        $records=TimeSheet::where('punch','>=',$from)->where('punch','<=',$to)->where('user_id',$user_id)->orderBy('punch','asc')->get();
        $status=1;
        Log::info(['date'=>$from,'to'=>$to]);
        foreach($records as $record){
            $status=($status+1)%2;
            Log::info(['date'=>$record->punch,'status'=>$status]);
            $record->status=$status;
            $record->save();
        }
    }

    public function recompute($from,$to,$users){
        $this->resetAttendance($from,$to,$users);
        $this->deleteOT($from,$to,$users);

        $interval = DateInterval::createFromDateString('1 day');
        $from=new DateTime($from);
        $to=new DateTime($to);
        $period = new DatePeriod($from, $interval,$to->modify('+1 day'));

        foreach($period as $dt) {
            $this->addSchedule($users,$dt->format('Y-m-d'));
            foreach($users as $user){
                $this->fixTimeSheet($dt->format('Y-m-d'),$user);
                $time_logs=TimeSheet::where('punch','>=',$dt->format('Y-m-d'))->where('punch','<=',$dt->format('Y-m-d 23:59:59'))->where('user_id',$user)->orderby('punch','asc')->get();
                foreach($time_logs as $log){
                    ProcessOvertime::dispatchNow($log);
                    ProcessAttendance::dispatchNow($log);
                }
            }
        }
    }

    public function addAttendance0($log){
        $dt=strtotime($log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$log->user_id;

        $is_holiday=Holiday::where('h_date',$date)->exists();
        if($is_holiday){
            return true;
        }

        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();
        
        if($attendance){
            $schedule=[
                "in"=>$attendance->sc_in,
                "out"=>$attendance->sc_out
            ];
            if($log->status==0){
                if($attendance->in){
                    if($time <= $schedule['in'] && $attendance->in < $time){
                        $attendance->in=$time;
                        $attendance->late_min=$this->lateFine($time,$schedule['in']);
                        $attendance->status=$attendance->late_min>0?'Late':'Normal';
                        $attendance->save();
                    }
                }else{
                    $attendance->in=$time;
                    $attendance->late_min=$this->lateFine($time,$schedule['in']);
                    $attendance->status=$attendance->late_min>0?'Late':'Normal';
                    $attendance->save();
                }

            }else{
                if($time<=$schedule['in']){
                    $attendance->in="";
                    $attendance->out="";
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
                "in"=>date('H:i:s',strtotime('08:00')),
                "out"=>date('H:i:s',strtotime('16:00'))
            ];
            $late_min=$this->lateFine($time,$schedule['in']);
            $status=$late_min>0?'Late':'Normal';
            Attendance::create(['user_id'=>$user_id,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out'],'in'=>$time,'late_min'=>$late_min,'status'=>$status]);
        }

    }

    public function addAttendance($log){
        $dt=strtotime($log->punch);
        $date=date('Y-m-d',$dt);
        $time=date('H:i:s',$dt);
        $user_id=$log->user_id;

        $is_holiday=Holiday::where('h_date',$date)->exists();
        if($is_holiday){
            return true;
        }

        $attendance=Attendance::where(['user_id'=>$user_id])->where('ck_date',$date)->first();
        
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

    private function deleteOT($from,$to,$users){
        $ots=Overtime::where('ck_date','>=',$from)->where('ck_date','<=',$to);
        if($users){
            $ots=$ots->whereIn('user_id',$users);
        }
        $ots->delete();
    }

    private function deleteAttendance($from,$to,$users){
        $attendances=Attendance::where('ck_date','>=',$from)->where('ck_date','<=',$to);
        if($users){
            $attendances=$attendances->whereIn('user_id',$users);
        }
        $attendances->delete();
    }

    private function resetAttendance($from,$to,$users){
        $attendances=Attendance::where('ck_date','>=',$from)->where('ck_date','<=',$to);
        if($users){
            $attendances=$attendances->whereIn('user_id',$users);
        }

        foreach($attendances->get() as $attendance){
            $attendance->in=Null;
            $attendance->out=Null;
            $attendance->status=Null;
            $attendance->save();
        }


    }

    public function addSchedule($employees,$date){
        $is_holiday=Holiday::where('h_date',$date)->exists();
        if($is_holiday){
            foreach($employees as $employee){
                Attendance::create(['user_id'=>$employee,'ck_date'=>$date,'status'=>'Holiday']);
            }
        }else{
            $schedule=[
                "in"=>date('H:i:s',strtotime('08:00')),
                "out"=>date('H:i:s',strtotime('16:00'))
            ];
            foreach($employees as $employee){
                if(!Attendance::where('user_id',$employee)->where('ck_date',$date)->exists()){
                    Attendance::create(['user_id'=>$employee,'ck_date'=>$date,'sc_in'=>$schedule['in'],'sc_out'=>$schedule['out']]);
                }
            }
        }
    }

}