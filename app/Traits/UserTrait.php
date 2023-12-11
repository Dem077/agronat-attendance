<?php
  
namespace App\Traits;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait UserTrait {
    public $user_id;
    public Array $users;
    public function setUser()
    {
        $user=Auth::user();
        $departments=Department::where('supervisor_id',$user->id)->pluck('id')->toArray();
        if(auth()->user()->can('reporting-manager')){
            $this->users=User::select('name','id','emp_no')->orderBy('name','asc')->get()->toArray();
        }elseif($departments){
            $this->users=User::select('name','id','emp_no')->whereIn('department_id',$departments)->orderBy('name','asc')->get()->toArray();
            if(!in_array($user->id,$this->users)){
                array_unshift($this->users,['name'=>$user->name,'id'=>$user->id,'emp_no'=>$user->emp_no]);
            }
        }else{
            $this->users=User::select('name','id','emp_no')
                                ->where('supervisor_id',$user->id)
                                ->where('id','<>',$user->id)
                                ->orderBy('name','asc')->get()
                                ->toArray();
            $this->users=array_merge([['id'=>$user->id,'name'=>$user->name,'emp_no'=>$user->emp_no]],$this->users);
            $this->user_id=$user->id;
        }
    }
    
}