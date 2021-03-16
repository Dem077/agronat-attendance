<?php

namespace App\Http\Livewire\Partials\Timesheets;

use App\Jobs\ZKTSync;
use Livewire\Component;

class SyncComponent extends Component
{
    public $user_id,$from,$to,$users;
    public function render()
    {
        return view('livewire.timesheets.sync');
    }

    public function sync(){

        if(!auth()->user()->can('timelog-create')){
            abort(403);
        }
        $validatedDate = $this->validate([
            'user_id' => 'sometimes',
            'from' => 'required',
            'to' => 'required'
        ]);

        ZKTSync::dispatchNow($validatedDate);
        $this->emit('.Synced'); // Close model to using to jquery

        session()->flash('message', 'Sync Successfully.');

    }

}
