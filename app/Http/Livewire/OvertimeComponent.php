<?php

namespace App\Http\Livewire;

use App\Models\Overtime;
use App\Models\User;
use App\Traits\UserTrait;
use Livewire\Component;
use Livewire\WithPagination;

class OvertimeComponent extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;

    public $ck_date, $in, $out, $ot, $start_date, $end_date;

    public function render()
    {
        $this->setUser();
        $ots=$this->getOvertime()->paginate(5);
        $this->resetPage();
        return view('livewire.overtime.component',['ots'=>$ots]);
    }

    public function getOvertime(){
        $ots=Overtime::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);
        if($this->start_date){
            $ots=$ots->where('ck_date','>=',$this->start_date);
        }
        if($this->end_date){
            $ots=$ots->where('ck_date','<=',$this->end_date);
        }
        if($this->user_id){
            $ots=$ots->where('user_id',$this->user_id);
        }

        return $ots->orderBy('ck_date','desc');
    }


    public function exportRecord() {
        $entries = $this->getOvertime()->get()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'overtime'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Checkin'=>'in',
            'Chckout'=>'out',
            'OT mins'=>'ot'
        );
    
        return export_csv($header, $entries, $filename);
        
    }

    
}
