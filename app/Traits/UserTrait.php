<?php
  
namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait UserTrait {
    public $user_id;
    public Array $users;
    public function setUser()
    {
        $user=Auth::user();
        if(auth()->user()->can('reporting-manager')){
            $this->users=User::select('name','id')->orderBy('name','asc')->get()->toArray();
        }elseif(auth()->user()->can('reporting-supervisor')){
            $this->users=User::select('name','id')->where('department_id',$user->department_id)->orderBy('name','asc')->get()->toArray();
        }else{
            $this->users=[['id'=>$user->id,'name'=>$user->name]];
            $this->user_id=$user->id;
        }
    }
}