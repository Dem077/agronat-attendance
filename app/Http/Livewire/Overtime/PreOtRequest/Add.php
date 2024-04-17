<?php

namespace App\Http\Livewire\Overtime\PreOtRequest;

use App\Models\Attendance;
use App\Models\PreOTRequest;
use App\Models\TimeSheet;
use App\Traits\UserTrait;
use DateTime;
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

    public function otmins($start_time,$end_time)
    {
        $otmins=strtotime($end_time)-strtotime($start_time);
        return $otmins>0?round($otmins/60,2):0;
    }

    public function store()
    {

        $this->validate();
        
        $this->ot->start_time="{$this->ot->ot_date} ".date('H:i',strtotime($this->ot->start_time));
        $this->ot->end_time="{$this->ot->ot_date} ".date('H:i',strtotime($this->ot->end_time));

        if(!auth()->user()->can('reporting-manager')){
            $this->gracePeriodValidate();
        }
        
        $this->afterShiftValidate();

        $this->ot->user_id=$this->user_id;
        $this->ot->requested_user_id=auth()->id();
        $this->ot->mins=$this->otmins($this->ot->start_time,$this->ot->end_time);
        $this->ot->status='pending';
        $duplicates=$this->checkDuplicateEnrtry($this->ot);
        if(!$duplicates->isEmpty()){
            $entries="";
            foreach($duplicates as $d){
                $entries.=date('H:i',strtotime($this->ot->start_time))." - ".date('H:i',strtotime($this->ot->end_time));
            }
            throw ValidationException::withMessages(['duplicate entry' => "Matching entries exist. {$entries}"]);
        }
        $this->ot->save();

        session()->flash('message', 'Requested Successfully');
        return redirect()->route('overtime.pre-ot-request.create');
    }

    public function gracePeriodValidate(){
        $lh=config('hr.late_ot_request_hours');
        $st=explode(" ",$this->ot->start_time);
        $ot_start_datetime=DateTime::createFromFormat('Y-m-d H:i:s',"{$this->ot->ot_date} {$st[1]}");
        $grace_period=now()->add("-{$lh} hours");
        if($ot_start_datetime<$grace_period){
            throw ValidationException::withMessages(['ot.ot_date'=>'Must be less than 48 hours']);
        }
    }

    public function afterShiftValidate(){
        $st=explode(" ",$this->ot->start_time);
        $et=explode(" ",$this->ot->end_time);

        $ot_start=DateTime::createFromFormat('Y-m-d H:i:s',"{$this->ot->ot_date} {$st[1]}");
        $ot_end=DateTime::createFromFormat('Y-m-d H:i:s',"{$this->ot->ot_date} {$et[1]}");

        if($ot_start>$ot_end){
            throw ValidationException::withMessages([
                'ot.start_time'=>'Start time must be less than End Time',
                'ot.end_time'=>'End time must be greater than Start Time'
            ]);
        }
        
        $attendance=Attendance::where('user_id',$this->user_id)->where('ck_date',$this->ot->ot_date)->first();

        if(!in_array($attendance?->status,['Normal','Late','Holiday'])){
            throw ValidationException::withMessages(['ot.ot_date'=>'No valid attendance found']);
        }


        if($attendance?->status!='Holiday'){
            $shift_in=DateTime::createFromFormat('Y-m-d H:i:s',"{$attendance->ck_date} {$attendance->sc_in}");
            $shift_out=DateTime::createFromFormat('Y-m-d H:i:s',"{$attendance->ck_date} {$attendance->sc_out}");
    
            if(($ot_start>$shift_in && $ot_start <$shift_out) || ($ot_end>$shift_in && $ot_end <$shift_out)){
                    throw ValidationException::withMessages([
                        'ot.start_time'=>'Cannot request ot in Shift time',
                        'ot.end_time'=>'Cannot request ot in Shift time'
                    ]);
            }
        }

        $logs=TimeSheet::where('user_id',$this->user_id)
                    ->whereRaw("date_format(punch,'%Y-%m-%d') = '{$this->ot->ot_date}'")
                    ->orderBy('punch','asc')
                    ->get()
                    ->toArray();
        
        if(count($logs)<2){
            throw ValidationException::withMessages([
                'ot.start_time'=>"No valid checkin log. ({$this->ot->start_time})",
                'ot.end_time'=>"No valid checkin log. ({$this->ot->end_time})"
            ]);
        }

        for($i=0;$i<count($logs)-1;$i+=2){
            $in=$logs[$i];
            $out=isset($logs[$i+1])?$logs[$i+1]:null;


            if(!$out){
                continue;
            }

            $in=DateTime::createFromFormat('Y-m-d H:i:s',$in['punch'])->modify("-1 mins");
            $out=DateTime::createFromFormat('Y-m-d H:i:s',$out['punch'])->modify("+1 mins");

            if($in<=$ot_start && $out>=$ot_end){
                return true;
            }
        }
        
        throw ValidationException::withMessages([
            'ot.start_time'=>"No valid checkin log ({$this->ot->start_time})",
            'ot.end_time'=>"No valid checkin log ({$this->ot->end_time})"
        ]);

    }

    public function checkDuplicateEnrtry(PreOTRequest $preOTRequest)
    {
        return PreOTRequest::where('user_id',$preOTRequest->user_id)
                                ->where('ot_date',$preOTRequest->ot_date)
                                ->where('status','<>','rejected')
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
