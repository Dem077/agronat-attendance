<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\DB;
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
        $ots=$this->getOvertime()->paginate(10);
        $this->resetPage();
        return view('livewire.overtime.component',['ots'=>$ots]);
    }

    public function getOvertime(){
        $ots=Overtime::select('id','user_id','ck_date','in','out','ot',DB::raw("DATE_FORMAT(ck_date,'%a') as day"))
                        ->addSelect(['status' => Attendance::select('status')->whereColumn('ck_date','overtimes.ck_date')->whereColumn('user_id', 'overtimes.user_id')->limit(1)])
                        ->addSelect(['employee' => User::select('name')->whereColumn('user_id', 'overtimes.user_id')->limit(1)]);
        if($this->start_date){
            $ots=$ots->where('ck_date','>=',$this->start_date);
        }
        if($this->end_date){
            $ots=$ots->where('ck_date','<=',$this->end_date);
        }
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }
            $ots=$ots->whereIn('user_id',$ids);
        }else{
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
            'Day'=>'day',
            'Checkin'=>'in',
            'Chckout'=>'out',
            'OT min'=>'ot',
        );
    
        return export_csv($header, $entries, $filename);
        
    }

    
}
