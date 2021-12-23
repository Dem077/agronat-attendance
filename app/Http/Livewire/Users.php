<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use phpDocumentor\Reflection\Types\Null_;

class Users extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $name, $user_id, $email, $designation,$password,$password_confirmation,$emp_no,$department_id,$mobile,$phone, $active;
    public $updateMode = false;


    public function getUsersProperty(){
        $users=User::select('*');
        if($this->user_id){
            $users=$users->where('id',$this->user_id);
        }

        return $users->orderBy('name','asc')->paginate(10);
    }
    public function render()
    {
        $employees=User::orderBy('name','asc')->pluck('name','id')->toArray();
        $departments=Department::all();
        $this->resetPage();
        return view('livewire.users.component',['employees'=>$employees,'departments'=>$departments]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->email = '';
        $this->emp_no = '';
        $this->designation = '';
        $this->department_id = '';
        $this->mobile = '';
        $this->phone = '';
        $this->password='';
        $this->password_confirmation='';
        $this->active='';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required',
            'email' => 'required',
            'emp_no' => 'required',
            'designation' => 'required',
            'department_id' => 'sometimes',
            'mobile' => 'sometimes',
            'phone' => 'sometimes',
            'active'=>'sometimes'
            //'password' => 'sometimes|password',
        ]);

        $validatedData['active']=true;
        $validatedData['password']=Hash::make('agro2020');
        $user=User::create($validatedData);
        $user->assignRole('Staff');
        
  
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
        $this->emp_no = $user->emp_no;
        $this->department_id = $user->department_id;
        $this->mobile = $user->mobile;
        $this->phone = $user->phone;
        $this->active = $user->active;
        $this->updateMode = true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->designation = $user->designation;
        $this->emp_no = $user->emp_no;
        $this->department_id = $user->department_id;
        $this->mobile = $user->mobile;
        $this->phone = $user->phone;
        $this->active = $user->active;
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
        $validatedData = $this->validate([
            'name' => 'required',
            'email' => 'required',
            'designation' => 'required',
            'emp_no' => 'required',
            'department_id' => 'required',
            'mobile' => 'sometimes',
            'phone' => 'sometimes',
            'password' => 'sometimes|confirmed',
            'active'=>'sometimes'
        ]);

        $update=[
            "name"=>$this->name,
            "email"=>$this->email,
            "designation"=>$this->designation,
            "emp_no"=>$this->emp_no,
            "department_id"=>$this->department_id,
            "mobile"=>$this->mobile,
            "phone"=>$this->phone,
            "active"=>$this->active
        ];
  
        if($this->password){
            $update['password']=Hash::make($validatedData['password']);
        }

        $user = User::findOrFail($this->user_id);

        if($user->update($update)){
            $this->emit('.userUpdated'); // Close model to using to jquery  
            session()->flash('message', 'User Updated Successfully.');
        }else{
            session()->flash('error', 'User Update Failed.');
        }

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
