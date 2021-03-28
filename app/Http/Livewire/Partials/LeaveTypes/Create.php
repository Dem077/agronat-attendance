<?php

namespace App\Http\Livewire\Partials\LeaveTypes;

use App\Models\LeaveType;
use Livewire\Component;
use PDO;

class Create extends Component
{
    public LeaveType $leaveType;

    protected $rules=[
        'leaveType.title'=>'required',
    ];
    public function mount(){
        $this->leaveType=new LeaveType();
    }

    public function render()
    {
        return view('livewire.partials.leave-types.create');
    }

    public function resetInput(){
        $this->mount();
    }

    public function store(){
        if(!auth()->user()->can('leave-list')){
            abort(403);
        }
        $this->validate();
        
        $this->leaveType->save();
        $this->resetInput();
        $this->emitUp('leaveTypeCreated');
        session()->flash('message', 'Leave type created successfully.');

    }
}
