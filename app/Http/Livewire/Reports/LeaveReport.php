<?php

namespace App\Http\Livewire\Reports;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use DateTime;
use Carbon\Carbon;
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

    public function exportleave()
    {
        $header = [
            'staff id', 'National ID', 'employee', 'Department', 'Joined Date', 'daterange',
            'Sick Leave (Without Certificate)', 'Family Leave', 'Annual Leave', 'Duty Travel', 
            'Virtual Day', 'Paternity Leave', 'Maternity Leave', 'Release', 'Quarantine leave', 
            'Circumcision Leave', 'Umra Leave', 'Sick Leave w Certificate'
        ];
        
        $users = User::select(DB::raw("id, nid, name, emp_no, joined_date, department_id"))
            ->active()
            ->orderBy('emp_no', 'asc')
            ->get();
        
        $leaveTypes = LeaveType::all();
        $allleaves = [];
        $leavebalance = [];
        
        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                $startDate = Carbon::parse($this->start_date);
                $endDate = Carbon::parse($this->end_date);
                $leaveYearStart = $startDate;
                $leaveYearEnd = $endDate;
                
                $leave_taken = $user->leaves()
                    ->where('leave_type_id', $leaveType->id)
                    ->get()
                    ->sum(function ($leave) use ($leaveYearStart, $leaveYearEnd, $user) {
                        $leaveStart = Carbon::parse($leave->from);
                        $leaveEnd = Carbon::parse($leave->to);
    
                        if ($leaveStart->greaterThan($leaveYearEnd) || $leaveEnd->lessThan($leaveYearStart)) {
                            return 0;
                        }
    
                        $leaveStart = $leaveStart->lessThan($leaveYearStart) ? $leaveYearStart : $leaveStart;
                        $leaveEnd = $leaveEnd->greaterThan($leaveYearEnd) ? $leaveYearEnd : $leaveEnd;
    
                        $totalDays = 0;
    
                        for ($date = $leaveStart; $date <= $leaveEnd; $date->addDay()) {
                            $is_holiday = Holiday::where('h_date', $date)->exists();
                            $work_saturday = User::where('id', $user->id)
                                ->whereHas('department', function($q) {
                                    $q->where('work_on_saturday', 1);
                                })->exists() && $date->isSaturday();
    
                            if (!($is_holiday && !$work_saturday)) {
                                $totalDays++;
                            }
                        }
    
                        return $totalDays;
                    });
    
                $allleaves[$user->emp_no][$leaveType->id] = $leave_taken;
            }
    
            $userBalance = [
                'staff id' => $user->emp_no,
                'nid' => $user->nid,
                'employee' => $user->name,
                'department' => $user->department->name,
                'Joined Date' => $user->joined_date,
                'daterange' => $this->start_date . '_' . $this->end_date
            ];
    
            foreach ($leaveTypes as $leaveType) {
                $userBalance[$leaveType->id] = $allleaves[$user->emp_no][$leaveType->id] ?? 0;
            }
    
            $leavebalance[] = $userBalance;
        }
        
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'leaves_balance'), date('Ymd'), date('His'));
    
        return export_csv2($header, $leavebalance, $filename);
    }
    
    

}
