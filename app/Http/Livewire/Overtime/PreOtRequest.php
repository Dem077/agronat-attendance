<?php

namespace App\Http\Livewire\Overtime;

use App\Models\PreOTRequest as ModelsPreOTRequest;
use App\Models\User;
use App\Traits\UserTrait;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PreOtRequest extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date, $readonly=false,$can_approve=false;
    protected $listeners=['updateStatus','userSelected','periodSelected'];

    public function mount()
    {
        $this->setUser();
        if(auth()->user()->can('overtime.pre-ot-approve') || count($this->users)>1){
            $this->can_approve=true;
        }

    }

    public function otmins($start_time,$end_time)
    {
        $otmins=strtotime($end_time)-strtotime($start_time);
        return $otmins>0?round($otmins/60,2):0;
    }

    public function updateStatus($id,$status)
    {
        if($this->can_approve){
            $otrequest=ModelsPreOTRequest::find($id);
            $otrequest->status=$status;
            if($status=='approved'){
                $otrequest->approved_user_id=auth()->id();
                $otrequest->approved_date=now();
                $otrequest->approved_start_time=$otrequest->start_time;
                $otrequest->approved_end_time=$otrequest->end_time;
                $otrequest->approved_mins=$this->otmins($otrequest->start_time,$otrequest->end_time);
            }
            $otrequest->save();
        }
    }

    function getOts() {
        $ot_requests=ModelsPreOTRequest::addSelect([
            'employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1),
            'nid' => User::select('nid')->whereColumn('user_id', 'users.id')->limit(1),
            'requested_by' => User::select('name')->whereColumn('requested_user_id', 'users.id')->limit(1),
            'approved_by' => User::select('name')->whereColumn('approved_user_id', 'users.id')->limit(1),
        ]);

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
        return $ot_requests;
    }
    public function getOtRequestsProperty(){
        return $this->getOts()->orderBy('ot_date','desc')->paginate(10);
    }

    public function exportRecord(){
        $report=$this->getOts()->get()->map(function($ot){
            $ot['starttime']=$ot->start_time->format('H:i');
            $ot['endtime']=$ot->end_time->format('H:i');
            return $ot;
        })->toArray();
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'pre ots'), date('Ymd'), date('His'));
        $header=[
            'employee'=>'employee',
            'nid'=>'nid',
            'ot_date'=>'ot_date',
            'starttime'=>'starttime',
            'endtime'=>'endtime',
            'mins'=>'mins',
            'purpose'=>'purpose',
            'status'=>'status',
            'requested_by'=>'requested_by',
            'approved_by'=>'approved_by'
        ];
        return export_csv($header, $report, $filename);
    }

    public function userSelected($user_id)
    {
        $this->user_id=$user_id;
        $this->resetPage();
    }

    public function periodSelected($period)
    {
        if(empty($period)){
            $period=['start'=>'','end'=>''];
        }
        $this->start_date=$period['start'];
        $this->end_date=$period['end'];
        $this->resetPage();
    }


    public function render()
    {

        return view('livewire.overtime.pre-ot-request');
    }
}
