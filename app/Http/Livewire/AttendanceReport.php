<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AttendanceReport extends Component
{

    public $start_date, $end_date,$user_id;


    public function render()
    {
        if(!$this->start_date){
            $this->setStartDate();
        }
        if(!$this->end_date){
            $this->setEndDate();
        }
        return view('livewire.reports.attendance-report');
    }

    private function setStartDate(){
        $today=new DateTime();
        $day=intval($today->format('d'));
        if($day<25){
            $today->modify('last month');
        }
        $month=intval($today->format('m'));
        $year=intval($today->format('Y'));
        $day=25;
        $this->start_date=(new DateTime("{$year}-{$month}-{$day}"))->format('Y-m-d');
    }
    private function setEndDate(){
        $today=new DateTime();
        $day=intval($today->format('d'));
        if($day>24){
            $today->modify('next month');
        }
        $month=intval($today->format('m'));
        $year=intval($today->format('Y'));
        $day=24;
        $this->end_date=(new DateTime("{$year}-{$month}-{$day}"))->format('Y-m-d');
    }
    public function exportRecord(){
        $attendances=Attendance::select('user_id','ck_date','late_min','status')->addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);
        if($this->user_id){
            $attendances=$attendances->where('user_id','=',$this->user_id);
        }

        if($this->start_date){
            $attendances=$attendances->where('ck_date','>=',$this->start_date);
        }
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $attendances=$attendances->where('ck_date','<=',$this->end_date);
        }
        $attendances=$attendances->orderBy('ck_date','asc')->get();

        $atts=[];
        $dates=[];
        $employees=[];
        foreach($attendances as $att){
            $status=['Present'=>0,'Absent'=>0,'Latemin'=>$att->late_min];
            if(!in_array($att->ck_date,$dates)){
                $dates[]=$att->ck_date;
            }
            if(!isset($employees[$att->employee])){
                $employees[$att->employee]=['Present'=>0,'Absent'=>0,'Latemin'=>0];
            }
            switch($att->status){
                case 'Normal':
                    $status['Present']=1;
                    break;
                case 'Late':
                    $status['Present']=1;
                    
                    break;
                case 'Absent':
                    $status['Absent']=1;
                    break;
                case '':
                    $status['Absent']=1;
                    break;

            }

            foreach($status as $k=>$v){
                $employees[$att->employee][$k]+=$v;
            }

            $atts[$att->employee][$att->ck_date]=$status;
        }
        $report=[];
        $header=['','Total','',''];
        $report['']=['Name','Present','Absent','Latemin'];
        foreach($dates as $dt){
            $report[''][]='Present';
            $report[''][]='Absent';
            $report[''][]='Latemin';
        }

        foreach($employees as $employee=>$stat){
            $report[$employee]=[$employee];
            foreach($stat as $k=>$v){
                $report[$employee][]=$v;
            }
            foreach($dates as $dt){
                if(!in_array($dt,$header)){
                    $header[]=$dt;
                    $header[]='';
                    $header[]='';
                }
                if(isset($atts[$employee][$dt])){
                    foreach($atts[$employee][$dt] as $k=>$v){
                        $report[$employee][]=$v;
                    }
                }else{
                    for($i=0;$i<3;$i++){
                        $report[$employee][]=0;
                    }
                }
            }

        }

        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        return export_csv2($header, array_values($report), $filename);
    }

}
