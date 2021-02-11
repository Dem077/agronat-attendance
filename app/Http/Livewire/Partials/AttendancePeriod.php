<?php

namespace App\Http\Livewire\Partials;

use DateTime;
use Livewire\Component;

class AttendancePeriod extends Component
{
    public $periods,$period_id;
    public function render()
    {
        $this->periods=$this->getPeriods();
        return view('livewire.partials.attendance-period');
    }

    public function getPeriods(){
        $today=new DateTime();
        $year=$today->format("Y");
        for($i=1;$i<=12;$i++){
            $end=new DateTime("{$year}-{$i}-24");
            $month=$end->format("F");
            $start=(clone $end)->modify("last month");
            $period[]=[
                "month"=>$month,
                "start"=>$start->format("Y-m-25"),
                "end"=>$end->format('Y-m-d')
            ];
        }

        return  $period;
    }
}
