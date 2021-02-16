<?php

namespace App\Http\Livewire\Charts;

use App\Models\Attendance;
use Livewire\Component;

class MonthlyAttendance extends Component
{
    public $user_id,$period,$from,$to;
    public function render()
    {
        return view('livewire.charts.monthly-attendance');
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
