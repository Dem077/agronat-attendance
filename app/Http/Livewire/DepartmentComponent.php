<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public Department $department;
    public $update=false;

    protected $rules=[
        'department.name'=>'required',
        'department.supervisor_id'=>'sometimes',
        'department.work_on_saturday'=>'required'
    ];

    protected $listeners = ['deleteLog' => 'delete'];

    public function mount(){
        $this->department=new Department();
    }

    public function resetInput(){
        $this->department=new Department();
        $this->update=false;
    }

    public function getDepartmentsProperty(){
        return Department::with('supervisor')->paginate(10);
    }

    public function getUsersProperty(){
        return User::select('name','id')->orderBy('name','asc')->get();
    }
    public function render()
    {
        return view('livewire.departments.component');
    }

    public function store(){
        $this->validate();

        $this->department->save();

        session()->flash('message','Department created');
    }

    public function edit($id){
        $this->update=true;
        $this->department=Department::findOrFail($id);
    }

    public function update(){
        $this->validate();

        $this->department->save();
        $this->resetInput();

        session()->flash('message','Department Updated');
    }

    public function delete($id){
        Department::destroy($id);
    }
}
