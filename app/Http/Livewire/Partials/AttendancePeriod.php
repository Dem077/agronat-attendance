<?php

namespace App\Http\Livewire\Partials;

use DateTime;
use Livewire\Component;

class AttendancePeriod extends Component
{
    public $periods;
    public $period_id=0;
    public function render()
    {
        $this->periods=$this->getPeriods();

        return view('livewire.partials.attendance-period');
    }

    public function getPeriods(){

        //current period
        $current=new DateTime();
        if((int)$current->format('d')>24){
            $current->modify('first day of next month');
        }
        $end=new DateTime($current->format('Y-m-24'));
        $start=(clone $end)->modify("last month");
        $period[]=[
            "month"=>$end->format('M, Y')." (Current)",
            "start"=>$start->format("Y-m-25"),
            "end"=>$end->format('Y-m-d')
        ];

        for ($i=1; $i<=6; $i++){
            $period[]=$this->getPeriodByDate(end($period));
        }

        return  $period;
    }

    private function getPeriodByDate($pd){
        $dt=new DateTime($pd['end']);
        $end=$dt->modify('first day of last month');
        $start=(clone $end)->modify("last month");
        return [
            "month"=>$end->format('M, Y'),
            "start"=>$start->format("Y-m-25"),
            "end"=>$end->format('Y-m-24')
        ];
    }
}
