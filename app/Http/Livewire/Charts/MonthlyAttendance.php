<?php

namespace App\Http\Livewire\Charts;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveType;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonthlyAttendance extends Component
{
    public $user_id,$period,$from_date,$to_date;
    public function render()
    {
        $this->user_id=auth()->id();
        $this->to_date=new DateTime();
        $this->from_date=(clone $this->to_date)->modify('-6 months');
        $this->getAttendanceByPeriod();
        return view('livewire.charts.monthly-attendance');
    }

    public function getAttendanceByPeriod(){

        $att=Attendance::where('ck_date','>=',$this->from_date)
                            ->where('ck_date','<=',$this->to_date)
                            ->where('user_id','=',$this->user_id)
                            ->select(DB::raw("DATE_FORMAT(ck_date,'%Y-%m') as month,status,count(1) as count"))
                            ->groupby(DB::raw("DATE_FORMAT(ck_date,'%Y-%m')"),'status')
                            ->orderby('ck_date')
                            ->get();
        $this->period=['data'=>[],'min'=>0,'max'=>30];

        $types=["Present"=>"Present","Late"=>"Present","Absent"=>"Absent","Duty Travel"=>"Present","Leave"=>"Leave"];
        // foreach(LeaveType::all() as $leave){
        //     if(!isset($types[$leave->name])){
        //         $types[$leave->name]="Leave";
        //     }
        // }
        foreach($types as $key=>$v){
            $this->period["data"][$v]=[];
        }

        foreach(date_range($this->from_date,$this->to_date,"1 month") as $dt){
            $dt=$dt->format('Y-m');
            $this->period['labels'][]=$dt;

            foreach($types as $k=>$v){
                $this->period['data'][$v][$dt]=0;
            }

        }

        foreach($att as $st){
            $dt=$st->month;
            if(isset($types[$st->status])){
                $this->period['data'][$types[$st->status]][$dt]+=$st->count;
            }
        }

    }
}
