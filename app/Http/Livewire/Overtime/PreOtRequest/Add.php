<?php

namespace App\Http\Livewire\Overtime\PreOtRequest;

use App\Models\PreOTRequest;
use App\Traits\UserTrait;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Add extends Component
{
    use UserTrait;

    protected $rules=[
        'ot.ot_date'=>'required',
        'ot.start_time'=>'required',
        'ot.end_time'=>'required',
        'user_id'=>'required',
        'ot.purpose'=>'required'
    ];

    public PreOTRequest $ot;
    public $readonly=false;

    public function mount()
    {
        $this->setUser();
        $this->ot=PreOTRequest::make();

    }

    public function store()
    {

        $this->validate();
        $otmins=strtotime($this->ot->end_time)-strtotime($this->ot->start_time);
        $otmins=$otmins>0?round($otmins/60,2):0;
        $this->ot->user_id=$this->user_id;
        $this->ot->mins=$otmins;
        $this->ot->status='pending';
        $duplicates=$this->checkDuplicateEnrtry($this->ot);
        if(!$duplicates->isEmpty()){
            $entries="";
            foreach($duplicates as $d){
                $entries.="{$d->start_time} - {$d->end_time}; ";
            }
            throw ValidationException::withMessages(['duplicate entry' => "Matching entries exist. {$entries}"]);
        }
        $this->ot->save();
        session()->flash('message', 'Requested Successfully');
    }

    public function checkDuplicateEnrtry(PreOTRequest $preOTRequest)
    {
        return PreOTRequest::where('user_id',$preOTRequest->user_id)
                                ->where('ot_date',$preOTRequest->ot_date)
                                ->where(function($q)use($preOTRequest){
                                    $q->whereBetween('start_time',[$preOTRequest->start_time,$preOTRequest->end_time])
                                    ->orWhereBetween('end_time',[$preOTRequest->start_time,$preOTRequest->end_time]);
                                })->get();

    }

    public function render()
    {
        return view('livewire.overtime.pre-ot-request.add');
    }
}
