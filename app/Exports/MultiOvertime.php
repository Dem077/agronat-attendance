<?php

namespace App\Exports;

use DateTime;
use App\Exports\Overtime as ExportsOvertime;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\User;
use App\Exports\OtSummary;
use App\Models\AppliedOt;
use App\Models\Department;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiOvertime implements WithMultipleSheets
{
    use Exportable;

    protected $start_date;
    protected $end_date;
    protected $user_id;

    
    public function __construct($start_date,$end_date,$user_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_id=$user_id;
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $summary=[];
        if($this->user_id){
            $users=[User::find($this->user_id)];
        }else{
            $exclude_departments=[17];
            $users=User::select('id','name')->whereNotIn('department_id',$exclude_departments)->get();
        }

        foreach ($users as $user) {
            $ots=$this->exportRecord($user->id);
            $summary[$user->name]=['employee'=>$user->name,'weekday'=>0,'holiday'=>0,'weekly_hours'=>0];
            foreach($ots as $i=>$ot){
                $weekly_hours=0;
                $elligible_ot=0;
                $days=$ot['end']->diff($ot['start'])->days+1;
                $ramadan=new DateTime('2021-04-13');
                if($ot['start']<$ramadan && $ot['end']>=$ramadan){
                    $rdays=$ot['end']->diff($ramadan)->days-1;
                    $days=$days-$ot['hdays'];
                    $weekly_hours=($days-$rdays)*8+$rdays*4;
                }elseif($ot['start']>=$ramadan){
                    $weekly_hours=($days-$ot['hdays'])*4;
                }else{
                    $weekly_hours=($days-$ot['hdays'])*8;
                }
                
                $elligible_ot=$ot['weekday']-$weekly_hours;
                $elligible_ot=$elligible_ot>0?$elligible_ot:0;
                $summary[$user->name]['weekly_hours']=$weekly_hours;
                $summary[$user->name]['weekday']+=$elligible_ot;
                $summary[$user->name]['holiday']+=$ot['holiday'];

                $ots[$i]['weekly_hours']=$weekly_hours;
                $ots[$i]['weekday']=$elligible_ot;
            }
            $sheets[] = new ExportsOvertime($ots,$user->name);
        }
        array_unshift($sheets,new OtSummary(array_values($summary)));

        return $sheets;
    }

    public function exportRecord($user_id){
        $attendances=Attendance::where('user_id',$user_id)
                        ->where('ck_date','>=',$this->start_date)
                        ->where('ck_date','<=',$this->end_date)->get();
        $overtimes=AppliedOt::where('user_id',$user_id)
                        ->where('ck_date','>=',$this->start_date)
                        ->where('ck_date','<=',$this->end_date)->get();
        $report=[];

        foreach ($attendances as $att) {
            $ck_date=new DateTime($att->ck_date);
            if($att->status=='Holiday' && $ck_date->format('D')!='Sat'){
                $att->status='holiday';
            }else{
                $att->status='weekday';
            }
            $report[$att->ck_date]=['status'=>$att->status,'data'=>[]];
            if($att->in){
                if($att->in>$att->sc_out){
                    continue;
                }
                if($att->in<$att->sc_in){
                    $att->in=$att->sc_in;
                }
                if($att->out>$att->sc_out || !$att->out || $att->out<=$att->sc_in){
                    $att->out=$att->sc_out;
                }
                $att->out=$att->sc_out;
                $att->weekday=round((strtotime($att->out)-strtotime($att->in))/3600,2);
                $att->holiday=0;
                $att->date=$att->ck_date;
                $data=Arr::only($att->toArray(),['date','in','out','weekday','holiday']);
                $report[$att->ck_date]['data'][]=$data;
            }
        }   
        
        foreach ($overtimes as $ot) {
            if($ot->ot>30){
                $data=['date'=>$ot->ck_date,'in'=>$ot->in,'out'=>$ot->out,'weekday'=>0,'holiday'=>0];
                $ot->ot=round($ot->ot/60,2);
                if($report[$ot->ck_date]['status']=='holiday'){
                    $data['holiday']=$ot->ot;
                }else{
                    $data['weekday']=$ot->ot;
                }

                $report[$ot->ck_date]['data'][]=$data;
            }
        }


        return $this->group($this->start_date,$this->end_date,$report);
        

    }

    public function group($start,$end,$data){
        
        $groups=[['start'=>new DateTime($start),'holiday'=>0,'weekday'=>0,'days'=>7,'hdays'=>0,'data'=>[]]];

        foreach (date_range($start,$end) as $date) {
            $ck_date=$date->format('Y-m-d');

            if($date->format('D')=='Sun'){
                $et=(clone $date)->modify('-1 day');
                $groups[count($groups)-1]['end']=$et;
                //$groups[count($groups)-1]['days']+=$groups[count($groups)-1]['start']->diff($et)->days+1;

                $groups[]=['start'=>$date,'holiday'=>0,'weekday'=>0,'days'=>7,'hdays'=>0,'data'=>[]];
            }

            if(isset($data[$ck_date])){
                if($data[$ck_date]['status']=='holiday'){
                    $groups[count($groups)-1]['days']-=1;
                    $groups[count($groups)-1]['hdays']+=1;
                }
                foreach ($data[$ck_date]['data'] as $log) {
                    $groups[count($groups)-1]['data'][]=$log;
                    $groups[count($groups)-1]['weekday']+=$log['weekday'];
                    $groups[count($groups)-1]['holiday']+=$log['holiday'];
                }                
            }
        }

        if(!isset($groups[count($groups)-1]['end'])){
            $groups[count($groups)-1]['end']=new DateTime($end);
            $groups[count($groups)-1]['days']=$groups[count($groups)-1]['start']->diff($et)->days+1;
        }

        return $groups;

    }
}