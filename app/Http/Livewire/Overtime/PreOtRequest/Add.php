<?php

namespace App\Http\Livewire\Overtime\PreOtRequest;

use App\Models\PreOTRequest;
use App\Traits\UserTrait;
use Livewire\Component;

class Add extends Component
{
    use UserTrait;

    protected $rules=[
        'ot.ck_date'=>'required',
        'ot.in'=>'required',
        'ot.out'=>'required',
        'user_id'=>'required',
        'ot.purpose'=>'required',
        'ot.hash'=>'sometimes'
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
        $othours=strtotime($this->ot->out)-strtotime($this->ot->in);
        $othours=$othours>0?round($othours/60,2):0;
        $this->ot->user_id=$this->user_id;
        $this->ot->othours=$othours;
        //dd($this->ot);
        //$this->ot->save();
        $this->mount();
        session()->flash('message', 'Requested Successfully');
    }

    public function render()
    {
        return view('livewire.overtime.pre-ot-request.add');
    }
}
