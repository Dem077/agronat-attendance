<?php

namespace App\Http\Livewire;

use App\Models\LeaveType;
use Livewire\Component;
use Livewire\WithPagination;

class LeaveTypeComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $leaveTypeId;
    public $title;
    public $allocated_days;
    public $showEditModal = false;

    protected $listeners = ['deleteLeaveType' => 'delete', 'leaveTypeCreated' => '$refresh'];

    protected $rules = [
        'title' => 'required|string|max:255',
        'allocated_days' => 'required|integer|min:0',
    ];

    public function render()
    {
        $leave_types = LeaveType::paginate(10);
        $this->resetPage();

        return view('livewire.leave-type.component', ['leave_types' => $leave_types]);
    }

    public function saveLeaveType()
    {
        $this->validate();

        if ($this->leaveTypeId) {
            $leaveType = LeaveType::find($this->leaveTypeId);
            $leaveType->update([
                'title' => $this->title,
                'allocated_days' => $this->allocated_days,
            ]);
        } else {
            LeaveType::create([
                'title' => $this->title,
                'allocated_days' => $this->allocated_days,
            ]);
        }

        $this->resetForm();
        $this->emit('leaveTypeCreated');
        $this->emit('hideEditModal');
    }

    public function create()
    {
        $this->resetForm();
        $this->showEditModal = true;
        $this->emit('showEditModal');
    }

    public function edit($id)
    {
        $leaveType = LeaveType::find($id);
        $this->leaveTypeId = $leaveType->id;
        $this->title = $leaveType->title;
        $this->allocated_days = $leaveType->allocated_days;
        $this->showEditModal = true;
        $this->emit('showEditModal');
    }

    public function delete($id)
    {
        if (!auth()->user()->can('leave-list')) {
            abort(403);
        }
        LeaveType::destroy($id);
    }

    public function resetForm()
    {
        $this->leaveTypeId = null;
        $this->title = '';
        $this->allocated_days = '';
        $this->showEditModal = false;
    }
}
