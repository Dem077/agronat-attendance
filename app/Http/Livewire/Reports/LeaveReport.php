<?php

namespace App\Http\Livewire\Reports;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use DateTime;
use Livewire\Component;

class LeaveReport extends Component
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
        return view('livewire.reports.leave-report');
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

    public function getLeaves(){
        $leaves=Leave::with(['user','type']);
        return $leaves->orderBy('from','desc')->get();
    }

    public function exportRecord(){
        $leaves=Leave::with(['user','type'])
                        ->addSelect(
                            ['holidays' => Holiday::select(DB::raw('count(1)'))
                                    ->whereColumn('leaves.from','<=','holidays.h_date')
                                    ->whereColumn('leaves.to','>=','holidays.h_date')
                                ]);

        $header=['eid','employee','leave type','from','to','days','holidays days','leave days'];

        if($this->start_date){
            $leaves=$leaves->where('from','>=',$this->start_date);
        }
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $leaves=$leaves->where('to','<=',$this->end_date);
        }
        $leaves=$leaves->orderBy('from','asc')->get();

        $leaves=$leaves->map(function($leave){
            $date1=date_create($leave->from);
            $date2=date_create($leave->to);
            $diff=date_diff($date1,$date2);
            $diff=intVal($diff->format("%R%a"))+1;
            return [
                $leave->user->emp_no,
                $leave->user->name,
                $leave->type->title,
                $leave->from,
                $leave->to,
                $diff,
                $leave->holidays,
                $diff-$leave->holidays
            ];
        });


        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'leaves'), date('Ymd'), date('His'));

        return export_csv2($header, $leaves, $filename);
    }

}
