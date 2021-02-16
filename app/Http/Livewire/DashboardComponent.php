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
    public $attendance,$period,$from_date,$to_date;

    public function render()
    {
        if(!$this->to_date){
            $this->to_date=(new DateTime())->modify('+1 day')->format('Y-m-24');
        }

        if(!$this->from_date){
            $this->from_date=(new DateTime($this->to_date))->modify('last month')->format('Y-m-25');
        }

        return $this->getAdminDashboard();
    }


    public function getAdminDashboard(){
        
        $this->getDailyAttendance();
        $this->getAttendanceByPeriod();
        return view('livewire.admin-dashboard');
    }

    public function getDashboard(){
        
        $this->getMonthlyAttendance(auth()->id());
        $this->getAttendanceByPeriod(auth()->id());
        return view('livewire.dashboard');
    }

    public function getDailyAttendance(){
        $this->attendance=['Present'=>0,'Absent'=>0,'Holiday'=>0];
        $att=Attendance::where('ck_date',(new DateTime()))
                                ->select(DB::raw("case when status='Late' or status='Normal' then 'Present' when status='Absent' or status is Null then 'Absent' else status end as status,count(1) as count"))
                                ->groupby('status')->get();
        foreach($att as $st){
            if(isset($this->attendance[$st->status])){
                $this->attendance[$st->status]+=$st->count;
            }
        }
    }
    public function getMonthlyAttendance($user_id=null){
        $this->attendance=['Present'=>0,'Absent'=>0,'Holiday'=>0];
        $att=Attendance::where('ck_date','>=',$this->from_date)
                                ->where('ck_date','<=',$this->to_date)
                                ->select(DB::raw("case when status='Late' or status='Normal' then 'Present' when status='Absent' or status is Null then 'Absent' else status end as status,count(1) as count"))
                                ->where('user_id',$user_id)
                                ->groupby('status')->get();
        foreach($att as $st){
            if(isset($this->attendance[$st->status])){
                $this->attendance[$st->status]+=$st->count;
            }
        }
    }
    
    public function getDateRange($from,$to){
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod(new DateTime($from), $interval, new DateTime($to));

        foreach($period as $dt) {
            yield $dt;
        }
    }
    public function getAttendanceByPeriod(){
        
        $att=Attendance::whereBetween('ck_date',[$this->from_date,$this->to_date])->select(DB::raw("ck_date,case when status='Late' or status='Normal' then 'Present' when status='Absent' or status is Null then 'Absent' else status end as status,count(1) as count"))->groupby('ck_date','status')->get();
        $this->period=['data'=>["Present"=>[],"Absent"=>[]],'labels'=>[],'min'=>0,'max'=>0];

        foreach($this->getDateRange($this->from_date,$this->to_date) as $dt){
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
