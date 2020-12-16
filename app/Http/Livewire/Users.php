<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $name, $user_id, $email, $designation,$password,$external_id;
    public $updateMode = false;

    public function render()
    {
        $employees=User::select(['id','name'])->get();
        $users=$this->getUsers()->paginate(5);

        return view('livewire.users.component',['employees'=>$employees,'users'=>$users]);
    }

    public function getUsers(){
        $users=User::select('*');
        if($this->user_id){
            $users=$users->where('id',$this->user_id);
        }

        return $users->orderBy('name','asc');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->email = '';
        $this->external_id = '';
        $this->designation = '';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $validatedDate = $this->validate([
            'name' => 'required',
            'email' => 'required',
            'external_id' => 'required',
            'designation' => 'required',
            // 'password' => 'required',
        ]);
  
        $validatedDate['password']=Hash::make('agro2020');
        User::create($validatedDate);
  
        $this->emit('.Store'); // Close model to using to jquery

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
        $this->name = $user->name;
        $this->email = $user->email;
        $this->designation = $user->designation;
        $this->external_id = $user->external_id;
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
            'name' => 'required',
            'email' => 'required',
            'designation' => 'required',
            'external_id' => 'required',
            // 'password' => 'sometimes',
        ]);
  
        $user = User::find($this->user_id);

        $user->update([
            "name"=>$this->name,
            "email"=>$this->email,
            "designation"=>$this->designation,
            "external_id"=>$this->external_id
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
