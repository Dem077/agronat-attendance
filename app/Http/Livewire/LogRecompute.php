<?php

namespace App\Http\Livewire;

use DateTime;
use Livewire\Component;

class LogRecompute extends Component
{
    public $start_date, $end_date,$user_id;


    public function render()
    {
        if(!$this->start_date){
            $this->setStartDate();
        }
        if(!$this->end_date){
            $this->setEndDate();
        }
        return view('livewire.recompute.log-recompute');
    }

    private function setStartDate(){
        $this->start_date = payroll_period()['start'];
    }

    private function setEndDate(){
        $this->end_date = payroll_period()['end'];
    }

    public function recompute(){
        
    }
}
