<?php

namespace App\Http\Livewire\Partials;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSelect extends Component
{
    use UserTrait;
    public $ref;
    public function render()
    {
        $this->setUser();
        return view('livewire.partials.user-select');
    }

    public function mount($ref=null){
        if($ref){
            $this->ref=$ref;
        }else{
            $this->ref="user-select-name";
        }
    }
}
