<?php

namespace App\Http\Livewire\Holidays;

use App\Models\Holiday;
use App\Models\User;
use App\Services\AttendanceService;
use Livewire\Component;

class AddHoliday extends Component
{
    public $from_date,$to_date,$description;

    protected $rules=[
        'from_date'=>"required|date",
        'to_date'=>"required|date",
        'description'=>"sometimes",
    ];
    public function render()
    {
        return view('livewire.holidays.add-holiday');
    }

    public function resetInput()
    {
       $this->from_date='';
       $this->to_date='';
       $this->description='';
    }

    public function add()
    {
        $this->validate();

        $attendanceService=new AttendanceService();

        $users=User::pluck('id')->toArray();

        foreach(date_range($this->from_date,$this->to_date) as $date){
            if(Holiday::where('h_date',$date)->exists()){
                continue;
            }
            Holiday::create(['h_date'=>$date,'description'=>$this->description]);
            
        }

        foreach($users as $user_id){
            $attendanceService->recompute($this->from_date,$this->to_date,$user_id);
        }

        $this->resetInput();
        session()->flash('message', 'Holiday Added Successfully.');
        $this->emit('$refresh');
    }
}
