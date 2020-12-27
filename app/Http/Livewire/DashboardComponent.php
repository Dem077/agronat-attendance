<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardComponent extends Component
{
    public $attendance,$period,$start_date,$end_date,$user_id;

    public function render()
    {
        
        if(!$this->end_date){
            $this->end_date=(new DateTime())->modify('+1 day')->format('Y-m-d');
        }

        if(!$this->start_date){
            $this->start_date=(new DateTime($this->end_date))->modify('last month')->format('Y-m-d');
        }
        
        $employees=User::all();
        $this->getAttendanceStats();
        $this->getAttendanceByPeriod();
        return view('livewire.dashboard',['employees'=>$employees]);
    }

    public function getAttendanceStats(){
        $dt=new DateTime();
        $this->attendance=Attendance::where('ck_date',$dt->format('Y-m-d'))
                                ->select(DB::raw("case when status='Late' or status='Normal' then 'Present' else status end as status,count(1) as count"))
                                ->groupby('status')
                                ->pluck('count','status');
    }
    
    public function getDateRange($from,$to){
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod(new DateTime($from), $interval, new DateTime($to));

        foreach($period as $dt) {
            yield $dt;
        }
    }
    public function getAttendanceByPeriod(){
        
        $att=Attendance::whereBetween('ck_date',[$this->start_date,$this->end_date])->select(DB::raw("ck_date,case when status='Late' or status='Normal' then 'Present' when status='Absent' or status is Null then 'Absent' else status end as status,count(1) as count"))->groupby('ck_date','status')->get();
        $this->period=['data'=>["Present"=>[],"Absent"=>[]],'labels'=>[],'min'=>0,'max'=>0];

        foreach($this->getDateRange($this->start_date,$this->end_date) as $dt){
            $dt=$dt->format('Y-m-d');
            $this->period['labels'][]=$dt;
            $this->period['data']["Present"][$dt]=0;
            $this->period['data']["Absent"][$dt]=0;
        }
        foreach($att as $st){
            if(isset($this->period['data'][$st->status][$st->ck_date])){
                $this->period['data'][$st->status][$st->ck_date]=$st->count;
                if($this->period['max']<$st->count){
                    $this->period['max']=$st->count;
                }
            }
        }

    }
}
