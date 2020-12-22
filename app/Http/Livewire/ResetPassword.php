<?php

namespace App\Http\Livewire;

use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ResetPassword extends Component
{
    public $user_id, $current_password, $password_confirmation, $password;
    public function render()
    {
        return view('livewire.reset-password');
    }

    public function update(){
        $validatedDate = $this->validate([
            'current_password' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        ]);
        $updater=new UpdateUserPassword();
        $updater->update(Auth::user(),$validatedDate);

        session()->flash('message', 'Password Changed Successfully.');

        $this->resetInputFields();

    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }
}
