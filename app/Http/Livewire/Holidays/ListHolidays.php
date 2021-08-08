<?php

namespace App\Http\Livewire\Holidays;

use App\Models\Holiday;
use Livewire\Component;
use Livewire\WithPagination;

class ListHolidays extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners=['$refresh'];
    public $start_date,$end_date;

    public function render()
    {
        return view('livewire.holidays.list-holidays');
    }

    public function getHolidaysProperty()
    {
        return Holiday::when($this->start_date,function($q){
            $q->where('h_date','>=',$this->start_date);
        })
        ->when($this->end_date,function($q){
            $q->where('h_date','<=',$this->end_date);
        })
        ->orderBy('h_date','asc')
        ->paginate(10);
    }


    public function delete($id){
        Holiday::destroy($id);
        $this->emitSelf('$refresh');

    }
}
