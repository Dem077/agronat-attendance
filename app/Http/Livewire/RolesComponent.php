<?php

namespace App\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;

    public $permission=[],$name,$role_id;
    public function render()
    {
        $roles=Role::paginate(5);
        $permissions=Permission::all();
        return view('livewire.roles.roles-component',compact(['roles','permissions']));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->role_id='';
        $this->permission=[];
    }

    public function store(){
        $validatedData = $this->validate([
            'name' => 'required',
            'permission' => 'required|array'
            // 'password' => 'required',
        ]);
        $permissions=[];
        foreach($validatedData['permission'] as $id=>$has){
            if($has){
                $permissions[]=$id;
            }
        }

        $role=Role::create(Arr::only($validatedData,['name']));
        if($role){
            $role->syncPermissions($permissions);
        }

        $this->emit('.Store'); // Close model to using to jquery

        session()->flash('message', 'Role Created Successfully.');
  
        $this->resetInputFields();
    }

    public function edit($id){
        $role=Role::find($id);
        $this->role_id=$id;
        $this->name=$role->name;
        $this->permission=$role->permissions()->select(DB::raw('id,true as checked'))->pluck('checked','id')->toArray();

    }

            /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function update()
    {
        $role = Role::find($this->role_id);
        $permissions=[];
        foreach($this->permission as $id=>$ck){
            if($ck){
                $permissions[]=$id;
            }
        }

        $role->syncPermissions($permissions);
  
        session()->flash('message', 'Role Permission Updated Successfully.');
        $this->resetInputFields();
        
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        Role::find($id)->delete();
        session()->flash('message', 'Role Deleted Successfully.');
    }
}
