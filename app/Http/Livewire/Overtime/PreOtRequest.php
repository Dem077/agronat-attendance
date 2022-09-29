<?php

namespace App\Http\Livewire\Overtime;

use App\Models\PreOTRequest as ModelsPreOTRequest;
use App\Traits\UserTrait;
use Livewire\Component;
use Livewire\WithPagination;

class PreOtRequest extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date, $readonly=false;

    public function mount()
    {
        $this->setUser();

    }


    public function render()
    {

        return view('livewire.overtime.pre-ot-request');
    }
}
