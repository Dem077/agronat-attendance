<?php

namespace App\Http\Livewire\Holidays;

use App\Models\Holiday;
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
        foreach(date_range($this->from_date,$this->to_date) as $date){
            if(Holiday::where('h_date',$date)->exists()){
                continue;
            }
            Holiday::create(['h_date'=>$date,'description'=>$this->description]);
        }
        $this->resetInput();
        session()->flash('message', 'Holiday Added Successfully.');
        $this->emit('$refresh');
    }
}
