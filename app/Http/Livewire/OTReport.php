<?php

namespace App\Http\Livewire;

use Livewire\Component;

class OTReport extends Component
{
    public $start_date, $end_date;
    public function render()
    {
        return view('livewire.reports.ot-report');
    }

    public function exportRecord(){

    }
}
