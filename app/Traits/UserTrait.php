<?php
  
namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait UserTrait {
    public $user_id,$users;
    public function setUser()
    {
        $user=Auth::user();
        if(auth()->user()->can('reporting-manager')){
            $this->users=User::pluck('name','id');
        }elseif(auth()->user()->can('reporting-supervisor')){
            $this->users=User::pluck('name','id');
        }else{
            $this->users=[$user->id=>$user->name];
            $this->user_id=$user->id;
        }
    }
}