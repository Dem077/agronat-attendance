<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class AssignRole extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;

    public $user_id, $role = [], $user_name;
    public $searchTerm = '';  

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';
        $users = User::with('roles')
                    ->where('name', 'like', $searchTerm)
                    ->paginate(15);
        $employees = User::select('id', 'name')->get();
        $roles = Role::all();
        return view('livewire.assign-role.component', compact('users', 'employees', 'roles'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $this->user_id = $id;
        $this->user_name = $user->name;
        $this->role = $user->roles()->select(DB::raw('id,true as checked'))->pluck('checked', 'id')->toArray();
    }

    public function resetInputFields()
    {
        $this->user_id = '';
        $this->user_name = '';
        $this->role = [];
    }

    public function cancel()
    {
        $this->resetInputFields();
    }

    public function update()
    {
        $user = User::find($this->user_id);
        $roles = [];
        foreach ($this->role as $id => $ck) {
            if ($ck) {
                $roles[] = $id;
            }
        }

        $user->syncRoles($roles);

        session()->flash('message', 'User Roles Updated Successfully.');
        $this->resetInputFields();
    }
}
