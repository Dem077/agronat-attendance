<?php

namespace App\Http\Livewire\Partials\Attendance;

use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Livewire\Component;

class RecomputeComponent extends Component
{
    public $user_id,$from,$to,$users,$in,$out;
    public function render()
    {
        if(!$this->in || !$this->out){
            $attendanceService=new AttendanceService();
            $this->in=$attendanceService->schedule['in'];
            $this->out=$attendanceService->schedule['out'];
        }
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
        // $validatedDate['from']=new DateTime($validatedDate['from']);
        // $validatedDate['to']=new DateTime($validatedDate['to']);
        $users=[];
        $attendanceService=new AttendanceService();

        if($this->user_id){
            $users=[$this->user_id];
        }else{
            $users=User::pluck('id')->toArray();
        }
        foreach($users as $user_id){
            $attendanceService->recompute($this->from,$this->to,$user_id,['in'=>$this->in,'out'=>$this->out]);
        }

        $this->emit('.Recomputed');

        session()->flash('message', 'Recompute Successfully.');

    }
}
