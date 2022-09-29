<?php

namespace App\Http\Livewire\Overtime\PreOtRequest;

use App\Models\PreOTRequest;
use App\Traits\UserTrait;
use Livewire\Component;

class Add extends Component
{
    use UserTrait;

    protected $rules=[
        'ot.ot_date'=>'required',
        'ot.start_time'=>'required',
        'ot.end_time'=>'required',
        'user_id'=>'required',
        'ot.purpose'=>'required'
    ];

    public PreOTRequest $ot;
    public $readonly=false;

    public function mount()
    {
        $this->setUser();
        $this->ot=PreOTRequest::make();

    }

    public function store()
    {
        $this->validate();
        $otmins=strtotime($this->ot->end_time)-strtotime($this->ot->start_time);
        $otmins=$otmins>0?round($otmins/60,2):0;
        $this->ot->user_id=$this->user_id;
        $this->ot->mins=$otmins;
        dd($this->ot);
        //$this->ot->save();
        $this->mount();
        session()->flash('message', 'Requested Successfully');
    }

    public function render()
    {
        return view('livewire.overtime.pre-ot-request.add');
    }
}
