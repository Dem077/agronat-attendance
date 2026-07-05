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
        $this->periods = $this->getPeriods();

        return view('livewire.partials.attendance-period');
    }

    public function getPeriods()
    {
        $current = payroll_period();
        $period[] = [
            'month' => (new DateTime($current['end']))->format('M, Y').' (Current)',
            'start' => $current['start'],
            'end' => $current['end'],
        ];

        for ($i = 1; $i <= 6; $i++) {
            $previous = payroll_period_before(end($period));
            $period[] = [
                'month' => (new DateTime($previous['end']))->format('M, Y'),
                'start' => $previous['start'],
                'end' => $previous['end'],
            ];
        }

        return $period;
    }
}
