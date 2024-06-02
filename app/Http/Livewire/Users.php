<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Location;
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
    public $department;
    public $employee;
    public $name,$nid, $user_id, $email, $designation,$password,$password_confirmation,$emp_no,$gender,$department_id,$location_id,$mobile,$phone, $active,$external_id,$joined_date;
    public $updateMode = false;
    public $locations=[];


    public function mount()
    {
        $this->department=request()->department??'';
        $this->employee=request()->employee??'';
        $this->locations=Location::all();
    }

    public function render()
    {
        $employees = User::orderBy('name', 'asc')->get()->mapWithKeys(function ($user) {
            return [$user->id => $user->name.' ( ' . $user->emp_no . ' ) ' ];
        })->toArray();
        $departments=Department::orderBy('name','asc')->get();
        $user_list=User::when($this->department,function($q){
                                $q->where('department_id',$this->department);
                            })
                        ->when($this->employee,function($q){
                            $q->where('id',$this->employee);
                        })->orderBy('id','asc')->paginate(10)->withQueryString();
        $this->resetPage();
        return view('livewire.users.component',['employees'=>$employees,'departments'=>$departments,'user_list'=>$user_list]);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->name = '';
        $this->nid = '';
        $this->email = '';
        $this->emp_no = '';
        $this->gender = '';
        $this->designation = '';
        $this->department_id = '';
        $this->external_id = '';
        $this->location_id = '';
        $this->joined_date = '';
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
            'nid' => 'required|unique:users,nid',
            'email' => 'required',
            'emp_no' => 'required',
            'gender' => 'required',
            'external_id' => 'sometimes',
            'designation' => 'required',
            'joined_date' => 'required',
            'mobile' => 'sometimes',
            'phone' => 'sometimes',
            'password' => 'required|confirmed'
        ]);

        $validatedData['department_id']=$this->department_id;
        $validatedData['location_id']=$this->location_id;
        $validatedData['joined_date']=$this->joined_date;
        $validatedData['active']=true;
        $validatedData['password']=Hash::make($validatedData['password']);
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
        $this->nid = $user->nid;
        $this->email = $user->email;
        $this->designation = $user->designation;
        $this->emp_no = $user->emp_no;
        $this->gender = $user->gender;
        $this->external_id = $user->external_id;
        $this->department_id = $user->department_id;
        $this->location_id = $user->location_id;
        $this->joined_date = $user->joined_date;
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
        $this->nid = $user->nid;
        $this->email = $user->email;
        $this->designation = $user->designation;
        $this->emp_no = $user->emp_no;
        $this->gender = $user->gender;
        $this->external_id = $user->external_id;
        $this->department_id = $user->department_id;
        $this->location_id = $user->location_id;
        $this->joined_date = $user->joined_date;
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
            'nid' => 'required|unique:users,nid,'.$this->user_id,
            'email' => 'required',
            'designation' => 'required',
            'emp_no' => 'required',
            'gender' => 'required',
            'external_id' => 'sometimes',
            'department_id' => 'required',
            'location_id' => 'required',
            'joined_date' => 'required',
            'mobile' => 'sometimes',
            'phone' => 'sometimes',
            'password' => 'sometimes|confirmed',
            'active'=>'sometimes'
        ]);

        $update=[
            "name"=>$this->name,
            "nid"=>$this->nid,
            "email"=>$this->email,
            "designation"=>$this->designation,
            "emp_no"=>$this->emp_no,
            "gender"=>$this->gender,
            "external_id"=>$this->external_id??null,
            "department_id"=>$this->department_id,
            "location_id"=>$this->location_id,
            'joined_date' =>$this->joined_date,
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
