<?php

namespace App\Http\Livewire;

use App\Models\AppliedOt;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use stdClass;

class OvertimeComponent extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;

    public $status,$date,$ot_in, $ot_out, $in, $out,$reason,$hash,$employee_id, $ot, $start_date, $end_date, $readonly=true;

    public function render()
    {
        $this->setUser();
        if(auth()->user()->can('overtime-create')){
            $this->readonly=false;
        }
        $ots=$this->getOvertime()->paginate(10);
        $this->resetPage();
        return view('livewire.overtime.component',['ots'=>$ots]);
    }

    public function getOvertime(){
        $ots=Overtime::select('id','user_id','ck_date','in','out','ot',DB::raw("DATE_FORMAT(ck_date,'%a') as day"))
                        ->addSelect(['employee' => User::select('name')->whereColumn('users.id', 'overtimes.user_id')->limit(1)]);
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

        return $ots->orderBy('ck_date','asc');
    }

  
    public function create($ot){
        $this->date=$ot['ck_date'];
        $this->in=$ot['in'];
        $this->out=$ot['out'];
        $this->ot_in=$ot['in'];
        $this->ot_out=$ot['out'];
        $this->employee_id=$ot['user_id'];
    }

    public function show($ot){
        $this->date=$ot['ck_date'];
        $this->in=$ot['in'];
        $this->out=$ot['out'];
        $this->employee_id=$ot['user_id'];
        $this->reason=$ot['reason']??'';
    }

    public function edit($hash){
        $applied=AppliedOt::where('hash',$hash)->firstOrFail();
        $this->applied_id=$applied->id;
        $this->date=$applied->ck_date;
        $this->in=$applied->in;
        $this->out=$applied->out;
        $this->employee_id=$applied->user_id;
        $this->reason=$applied->reason;
    }



    public function store(){
        $validated=$this->validate([
            'date'=>'required',
            'in'=>'required',
            'out'=>'required',
            'employee_id'=>'required',
            'reason'=>'required',
            'hash'=>'required'
        ]);

        AppliedOt::create($validated);
        $this->emit('.Saved');
    }

    public function update(){
        $validated=$this->validate([
            'date'=>'required',
            'in'=>'required',
            'out'=>'required',
            'employee_id'=>'required',
            'reason'=>'required',
            'hash'=>'required'
        ]);
        $applied=AppliedOt::where('hash',$validated['hash'])->firstOrFail();
        $applied->update($validated);
        $this->emit('.Saved');

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
