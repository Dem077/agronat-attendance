<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $isOpen = false;
    
    public $user_id, $ck_date, $in, $out, $start_date, $end_date;


    public function render()
    {
        $attendances=$this->getAttendances()->paginate(5);
        $employees=User::all();
        $this->resetPage();
        return view('livewire.attendances.component',['employees'=>$employees,'attendances'=>$attendances]);
        // ->extends('layouts.app')
        // ->section('content');
        // $this->timesheets = TimeSheet::all();
        // $this->attendances=$this->getAttendances()->get();
        // $this->employees=User::all();
        // return view('livewire.attendances.component');
    }

    public function getAttendances(){
        $attendances=Attendance::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)])
        ->addSelect(DB::raw("'08:00' as scin"))
        ->addSelect(DB::raw("'16:00' as scout"));
        if($this->start_date){
            $attendances=$attendances->where('ck_date','>=',$this->start_date);
        }
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $attendances=$attendances->where('ck_date','<=',$this->end_date);
        }
        if($this->user_id){
            $attendances=$attendances->where('user_id',$this->user_id);
        }

        return $attendances->orderBy('ck_date','desc');
    }


    public function exportRecord() {
        $entries = $this->getAttendances()->get()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Duty Start'=>'scin',
            'Duty End'=>'scout',
            'Checkin'=>'in',
            'Checkout'=>'out',
            'Latefine'=>'late_min',
            'Status'=>'status'
        );
    
        return export_csv($header, $entries, $filename);
        
    }

}
