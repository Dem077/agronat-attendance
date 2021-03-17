<?php

namespace App\Http\Livewire\Partials\Attendance;

use App\Services\AttendanceService;
use DateTime;
use Livewire\Component;

class RecomputeComponent extends Component
{
    public $user_id,$from,$to,$users;
    public function render()
    {
        return view('livewire.attendances.recompute');
    }

    public function recompute(){

        if(!auth()->user()->can('timelog-create')){
            abort(403);
        }
        $validatedDate = $this->validate([
            'user_id' => 'sometimes',
            'from' => 'required',
            'to' => 'required'
        ]);

        $attendanceService=new AttendanceService();
        $attendanceService->recompute(new DateTime($validatedDate['from']),new DateTime($validatedDate['to']),new DateTime($validatedDate['user_id']));
        $this->emit('.Recomputed');

        session()->flash('message', 'Recompute Successfully.');

    }
}
