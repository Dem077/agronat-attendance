<?php

namespace App\Http\Livewire\Charts;

use Livewire\Component;

class LineChart extends Component
{
    public $period;

    public function mount($period)
    {
        $this->period=$period;
    }
    public function render()
    {
        return view('livewire.charts.line-chart');
    }
}
