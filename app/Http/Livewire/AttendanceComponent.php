<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use App\Traits\UserTrait;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination, UserTrait;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['userSelected' => '$refresh'];



    public $isOpen = false;
    
    public $ck_date, $in, $out, $start_date, $end_date;
    public $attendance_statuses=[
        'Normal','Late','Absent','Duty Travel','Sick Leave','Family Leave','Annual Leave'
    ];


    public function render()
    {
        $this->setUser();

        $attendances=$this->getAttendances()->paginate(31);

        /**
         * manage all
         * manage departments
         * view personal
         */
        
        $this->resetPage();
        return view('livewire.attendances.component',['attendances'=>$attendances]);
    }


    public function userSelected($id)
    {
        $this->user_id=$id;
    }



    public function getAttendances(){
        $attendances=Attendance::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);

        if($this->start_date){
            $attendances=$attendances->where('ck_date','>=',$this->start_date);
        }
        
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $attendances=$attendances->where('ck_date','<=',$this->end_date);
        }
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }

            $attendances=$attendances->whereIn('user_id',$ids);
        }else{
            $attendances=$attendances->where('user_id',$this->user_id);
        }

        return $attendances->orderBy('ck_date','asc');
    }


    public function exportRecord() {
        $entries = $this->getAttendances()->get()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Duty Start'=>'sc_in',
            'Duty End'=>'sc_out',
            'Checkin'=>'in',
            'Checkout'=>'out',
            'Latefine'=>'late_min',
            'Status'=>'status'
        );
    
        return export_csv($header, $entries, $filename);
        
    }

}
