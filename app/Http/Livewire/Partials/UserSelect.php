<?php

namespace App\Http\Livewire\Partials;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSelect extends Component
{
    use UserTrait;
    public function render()
    {
        $this->setUser();
        return view('livewire.partials.user-select');
    }
}
