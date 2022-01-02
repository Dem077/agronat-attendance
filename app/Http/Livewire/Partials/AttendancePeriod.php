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
            $current->modify('next month');
        }
        $end=new DateTime($current->format('Y-m-24'));
        $start=$start=(clone $end)->modify("last month");
        $period[]=[
            "month"=>"Current (".$end->format('M, y').")",
            "start"=>$start->format("Y-m-25"),
            "end"=>$end->format('Y-m-d')
        ];

        //last period
        $end=new DateTime($start->format('Y-m-24'));
        $start=(clone $end)->modify("last month");
        $period[]=[
            "month"=>"Last (".$end->format('M, y').")",
            "start"=>$start->format("Y-m-25"),
            "end"=>$end->format('Y-m-d')
        ];

        return  $period;
    }
}
