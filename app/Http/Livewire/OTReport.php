<?php

namespace App\Http\Livewire;

use App\Exports\MultiOvertime;
use App\Exports\Overtime as ExportsOvertime;
use App\Models\Attendance;
use App\Models\Overtime;
use DateTime;
use Illuminate\Support\Arr;
use Livewire\Component;

class OTReport extends Component
{
    public $start_date, $end_date,$user_id,$export=[];
    public function render()
    {
        return view('livewire.reports.ot-report');
    }

    public function exportRecord(){
        return (new MultiOvertime($this->start_date,$this->end_date,$this->user_id))->download('ot.xlsx');
    }


}
