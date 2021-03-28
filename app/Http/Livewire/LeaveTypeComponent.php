<?php

namespace App\Http\Livewire;

use App\Models\LeaveType;
use Livewire\Component;
use Livewire\WithPagination;

class LeaveTypeComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['deleteLeaveType' => 'delete','leaveTypeCreated' => '$refresh'];


    public function render()
    {
        $leave_types=LeaveType::paginate(10);
        $this->resetPage();

        return view('livewire.leave-type.component',['leave_types'=>$leave_types]);
    }
    

    public function delete($id){
        if(!auth()->user()->can('leave-list')){
            abort(403);
        }
        LeaveType::destroy($id);
    }
}
