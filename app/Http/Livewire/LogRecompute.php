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
        $today=new DateTime();
        $day=intval($today->format('d'));
        if($day<25){
            $today->modify('last month');
        }
        $month=intval($today->format('m'));
        $year=intval($today->format('Y'));
        $day=25;
        $this->start_date=(new DateTime("{$year}-{$month}-{$day}"))->format('Y-m-d');
    }
    private function setEndDate(){
        $today=new DateTime();
        $day=intval($today->format('d'));
        if($day>24){
            $today->modify('next month');
        }
        $month=intval($today->format('m'));
        $year=intval($today->format('Y'));
        $day=24;
        $this->end_date=(new DateTime("{$year}-{$month}-{$day}"))->format('Y-m-d');
    }

    public function recompute(){
        
    }
}
