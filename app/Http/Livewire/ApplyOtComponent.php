<?php

namespace App\Http\Livewire;

use App\Models\AppliedOt;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ApplyOtComponent extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';

    public AppliedOt $ot;
    public $start_date, $end_date, $readonly=true;

    protected $listeners = ['deleteLog' => 'delete'];


    protected $rules=[
        'ot.ck_date'=>'required',
        'ot.in'=>'required',
        'ot.out'=>'required',
        'ot.user_id'=>'required',
        'ot.hash'=>'sometimes'
    ];

    public function mount(){
        $this->ot=new AppliedOt();
    }

    public function render()
    {
        $this->setUser();
        if(auth()->user()->can('overtime-create')){
            $this->readonly=false;
        }
        $ots=$this->getOvertime()->paginate(10);
        $this->resetPage();
        return view('livewire.overtime.apply-ot-component',['ots'=>$ots]);
    }

    public function getOvertime(){
        $ots=AppliedOt::select('id','user_id','ck_date','in','out','ot',DB::raw("DATE_FORMAT(ck_date,'%a') as day"))
                        ->addSelect(['employee' => User::select('name')->whereColumn('users.id', 'applied_ots.user_id')->limit(1)]);
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

    public function edit($id){
        $this->ot=AppliedOt::findOrFail($id);
    }

    public function store(){
        $this->validate();
        $ot=strtotime($this->ot->out)-strtotime($this->ot->in);
        $ot=$ot>0?round($ot/60,2):0;
        $this->ot->ot=$ot;
        $this->ot->save();
        $this->mount();
        session()->flash('message', 'OT Applied');
    }

    public function update($id){
        
    }

    public function delete($id){
        AppliedOt::destroy($id);
    }
}

