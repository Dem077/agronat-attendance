<?php

namespace App\Http\Livewire\Partials;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSelect extends Component
{
    public $user_id,$users;
    public function render()
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
        return view('livewire.partials.user-select');
    }
}
