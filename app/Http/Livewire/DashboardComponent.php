<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use App\Traits\UserTrait;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardComponent extends Component
{
    use UserTrait;
    public $attendance,$period,$from_date,$to_date,$month;

    public function render()
    {
        if(!$this->to_date){
            $this->to_date=(new DateTime())->format('Y-m-24');
            $this->month=(new DateTime($this->to_date))->format("F");
        }

        if(!$this->from_date){
            $this->from_date=(new DateTime($this->to_date))->modify('last month')->format('Y-m-25');
        }

        if(auth()->user()->can('admin_dashboard-view')){
            return $this->getAdminDashboard();
        }else{
            return $this->getDashboard();
        }

    }


    public function getAdminDashboard(){
        
        $this->getDailyAttendance();
        $this->getAttendanceByPeriod();
        return view('livewire.admin-dashboard');
    }

    public function getDashboard(){
        
        $this->getMonthlyAttendance(auth()->id());
        return view('livewire.dashboard');
    }

    public function getDailyAttendance(){
        $this->attendance=[];
        $types=["Normal"=>"Present","Late"=>"Present","Absent"=>"Absent","Duty Travel"=>"Present","Leave"=>"Leave"];
        // foreach(LeaveType::all() as $leave){
        //     if(!isset($types[$leave->name])){
        //         $types[$leave->name]="Leave";
        //     }
        // }
        foreach($types as $k=>$v){
            $this->attendance[$v]=0;
        }

        $att=Attendance::where('ck_date',new DateTime())
                                ->select(DB::raw("status,count(1) as count"))
                                ->groupby('status')->get();

        foreach($att as $st){
            if(isset($types[$st->status])){
                $this->attendance[$types[$st->status]]+=$st->count;
            }
        }
    }
    public function getMonthlyAttendance($user_id=null){
        $this->attendance=[];
        $types=["Normal"=>"Present","Late"=>"Present","Absent"=>"Absent","Duty Travel"=>"Present","Leave"=>"Leave"];
        // foreach(LeaveType::all() as $leave){
        //     if(!isset($types[$leave->name])){
        //         $types[$leave->name]="Leave";
        //     }
        // }
        foreach($types as $k=>$v){
            $this->attendance[$v]=0;
        }

        $att=Attendance::where('ck_date','>=',$this->from_date)
                                ->where('ck_date','<=',$this->to_date)
                                ->select(DB::raw("status,count(1) as count"))
                                ->where('user_id',$user_id)
                                ->groupby('status')->get();
        $this->attendance['Latemin']=Attendance::where('ck_date','>=',$this->from_date)
                                ->where('ck_date','<=',$this->to_date)
                                ->where('user_id',$user_id)
                                ->sum('late_min');

        foreach($att as $st){
            if(isset($types[$st->status])){
                $this->attendance[$types[$st->status]]+=$st->count;
            }
        }
    }
    
    public function getAttendanceByPeriod(){
        
        $att=Attendance::whereBetween('ck_date',[$this->from_date,$this->to_date])->select(DB::raw("ck_date,case when status='Late' or status='Normal' then 'Present' when status='Absent' or status is Null then 'Absent' else status end as status,count(1) as count"))->groupby('ck_date','status')->get();
        $this->period=['data'=>["Present"=>[],"Absent"=>[]],'labels'=>[],'min'=>0,'max'=>0];

        foreach(date_range($this->from_date,$this->to_date) as $dt){
            $dt=$dt->format('Y-m-d');
            $this->period['labels'][]=$dt;
            $this->period['data']["Present"][$dt]=0;
            $this->period['data']["Absent"][$dt]=0;
        }
        foreach($att as $st){
            if(isset($this->period['data'][$st->status][$st->ck_date])){
                $this->period['data'][$st->status][$st->ck_date]+=$st->count;
                if($this->period['max']<$this->period['data'][$st->status][$st->ck_date]){
                    $this->period['max']=$this->period['data'][$st->status][$st->ck_date];
                }
            }
        }

    }
}
