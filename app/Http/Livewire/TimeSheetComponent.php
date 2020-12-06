<?php

namespace App\Http\Livewire;

use App\Models\TimeSheet;
use Livewire\Component;

class TimeSheetComponent extends Component
{
    public function render()
    {
        $logs=TimeSheet::with('user')->orderBy('punch','asc')->paginate(5);
        return view('livewire.timesheets.component',['logs'=>$logs])
        ->extends('layouts.app')
        ->section('content');
    }
}
