<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $username, $fullname, $user_id, $email, $position,$password;
    public $updateMode = false;

    public function render()
    {
        //$this->users=User::all();
        return view('livewire.users.component',['users'=>User::paginate(4)])
            ->extends('layouts.app')
            ->section('content');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->username = '';
        $this->fullname = '';
        $this->email = '';
        $this->position = '';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $validatedDate = $this->validate([
            'username' => 'required',
            'fullname' => 'required',
            'email' => 'required',
            'position' => 'required',
            'password' => 'required',
        ]);
  
        User::create($validatedDate);
  
        $this->emit('userStore'); // Close model to using to jquery

        session()->flash('message', 'user Created Successfully.');
  
        $this->resetInputFields();


    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->username = $user->username;
        $this->fullname = $user->fullname;
        $this->email = $user->email;
        $this->position = $user->position;
        $this->updateMode = true;
    }
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInputFields();
    }
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function update()
    {
        $validatedDate = $this->validate([
            'username' => 'required',
            'fullname' => 'required',
            'email' => 'required',
            'position' => 'required',
            'password' => 'sometimes',
        ]);
  
        $user = User::find($this->user_id);

        $user->update([
            "username"=>$this->username,
            "fullname"=>$this->fullname,
            "email"=>$this->email,
            "position"=>$this->position
        ]);
  
        $this->updateMode = false;
  
        session()->flash('message', 'User Updated Successfully.');
        $this->resetInputFields();
        
    }
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User Deleted Successfully.');
    }
}
