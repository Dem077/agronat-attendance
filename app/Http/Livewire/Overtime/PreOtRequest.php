<?php

namespace App\Http\Livewire\Overtime;

use App\Models\PreOTRequest as ModelsPreOTRequest;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PreOtRequest extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date, $readonly=false;
    protected $listeners=['updateStatus'];

    public function mount()
    {
        $this->setUser();

    }

    public function updateStatus($id,$status)
    {
        if(auth()->user()->can('overtime.pre-ot-approve')){
            ModelsPreOTRequest::whereId($id)->update(['status'=>$status]);
        }
    }

    public function getOtRequestsProperty(){
        $ot_requests=ModelsPreOTRequest::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);
        return $ot_requests->orderBy('ot_date','asc')->get();
        if($this->start_date){
            $ot_requests=$ot_requests->where('ot_date','>=',$this->start_date);
        }

        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $ot_requests=$ot_requests->where('ot_date','<=',$this->end_date);
        }
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }

            $ot_requests=$ot_requests->whereIn('user_id',$ids);
        }else{
            $ot_requests=$ot_requests->where('user_id',$this->user_id);
        }

        return $ot_requests->orderBy('ot_date','asc')->get();
    }


    public function render()
    {

        return view('livewire.overtime.pre-ot-request');
    }
}
