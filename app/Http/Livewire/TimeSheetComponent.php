<?php

namespace App\Http\Livewire;

use App\Jobs\ZKTSync;
use App\Models\Attendance;
use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\TimeChangeLog;
use Livewire\WithPagination;

class TimeSheetComponent extends Component
{

    use WithPagination,UserTrait;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;
    public $changeData;
    private $attendanceService;

    public $punchdate,$punchtime,$start_date,$end_date,$sync_data=['user_id'=>'','from'=>'','to'=>''],$reason;

    protected $listeners = ['deleteLog' => 'delete','setChangesData'];
    public function __construct()
    {
        $this->attendanceService=new AttendanceService();
        parent::__construct();
    }

    public function render()
    {
        $this->setUser();
        $logs = $this->getTimeSheet(10);
        $this->resetPage();
    // dd($logs);
        // dd($logs);
        return view('livewire.timesheets.component', [
            'logs' => $logs,
        ]);
    }
    
    public function setChangesData($attendances_id)
    {
        $all=[];
        $logdata = TimeChangeLog::where('attendances_id', $attendances_id)->get()->all();
        foreach($logdata as $row){
            $editedTime = DB::select('SELECT punch FROM time_sheets WHERE id = ?', [$row->time_sheet_id]);
            $punch=$editedTime[0]->punch;
            list($date, $time) = explode(' ', $punch);
            $all[]=['list'=>$row,'time'=>$time,'date'=>$date];
            
        }
        // dd($all);
        $this->changeData = $all;
    }

    public function getTimeSheet($pagination=null){
        set_time_limit(1000);

        $attendance=Attendance::select('*',DB::raw("date_format(ck_Date,'%a') as day"))
                        ->addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);
                    // ->whereExists(function($q){
                    //     $q->select(DB::raw(1))
                    //     ->from('time_sheets')
                    //     ->whereRaw("date_format(time_sheets.punch,'%Y-%m-%d') = attendances.ck_date")
                    //     ->whereRaw("time_sheets.user_id = attendances.user_id");
                    // });
                    
        $timesheet=TimeSheet::select('*');
        if($this->start_date){
            $attendance=$attendance->where('ck_date','>=',$this->start_date);
            $timesheet=$timesheet->where('punch','>=',$this->start_date);
        }  
        if($this->end_date){
            $attendance=$attendance->where('ck_date','<=',$this->end_date);
            $timesheet=$timesheet->where('punch','<=',$this->end_date.' 23:59:59');
        }
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }
            $attendance=$attendance->whereIn('user_id',$ids);
            $timesheet=$timesheet->whereIn('user_id',$ids);

        }else{
            $attendance=$attendance->where('user_id',$this->user_id);
            $timesheet=$timesheet->where('user_id',$this->user_id);
        }

        $attendance=$attendance->orderBy('ck_date','asc');
        $timesheet=$timesheet->orderBy('punch','asc');
        $links='';
        if($pagination){
            $attendance=$attendance->paginate($pagination);
            $min_date=$attendance->min("ck_date");
            $max_date=$attendance->max("ck_date");

            $timesheet=$timesheet->when($min_date,fn($q,$min_date)=>$q->where("punch",">=",$min_date))
                                ->when($max_date,fn($q,$max_date)=>$q->where("punch","<=",$max_date." 23:59:59"))
                                ->get();

            $links=$attendance->links();

        }else{
            $attendance=$attendance->get();
            $timesheet=$timesheet->get();
        }

        $data=[];
        $isedited = [];
        foreach($attendance as $att){
            
            $exitsintable = TimeChangeLog::where('attendances_id', $att->id)->get();
            if ($exitsintable) {
                $isedited = [
                    'changes_made' => $exitsintable->first(),
                ];
            }

            $p=[];
            $punches=$timesheet->where('user_id',$att->user_id)->where("punch",">=",$att->ck_date)->where("punch","<=",$att->ck_date." 23:59:59");
            $timesheet=$timesheet->whereNotIn('id',$punches->pluck('id'));
            foreach($punches as $punch){
                $p[]=['time'=>date('G:i',strtotime($punch->punch)),'id'=>$punch->id];
            }
            $att->changes=$isedited;
            $att->punch=$p;
            $data[]=$att;
        }

        return ['data'=>$data,'links'=>$links];

    }

    

    public function exportRecord() {
        $entries = [];
        foreach($this->getTimeSheet()['data'] as $att){
            $dt=["employee"=>$att->employee,'day'=>$att->day,'ck_date'=>$att->ck_date,'status'=>$att->status,'late_min'=>$att->late_min];
            for($i=0;$i<6;$i++){
                $key="check{$i}";
                $dt[$key]="";
                if($i<count($att->punch)){
                    $dt[$key]=$att->punch[$i]['time'];
                }
            }
            $entries[]=$dt;
        }
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'timesheet'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Day'=>'day',
            'Status'=>'status',
            'Late Min'=>'late_min'
        );
        for($i=0;$i<6;$i++){
            $v="check{$i}";
            $c=floor($i/2+1);
            $key="Check".($i%2?"Out {$c}":"In {$c}");
            $header[$key]=$v;
        }
        
        return export_csv($header, $entries, $filename);
        
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->punchdate = '';
        $this->punchtime = '';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        if(!auth()->user()->can('timelog-create')){
            abort(403);
        }
        $validatedDate = $this->validate([
            'user_id' => 'required',
            'punchdate' => 'required',
            'punchtime' => 'required',
            'reason' => 'required' // Validate reason
        ]);

        $validatedDate['punch']="{$validatedDate['punchdate']} {$validatedDate['punchtime']}";
        $date=$validatedDate['punchdate'];
        unset($validatedDate['punchdate']);
        unset($validatedDate['punchtime']);
        // TimeSheet::add($validatedDate);

        $userName = auth()->user()->name;
        

        $validatedDate['logged_by']=Auth::id();
        $validatedDate['sync']=0;

        $record = $this->attendanceService->addLog($validatedDate);

        $attendanceid = Attendance::where('user_id',  $validatedDate['user_id'])->where('ck_date', $date)->first();
       
        (new TimeChangeLog)->logaudit($attendanceid->id, $record->id, $userName, $validatedDate['reason'] , "INSERT");

        $this->resetInputFields();

        session()->flash('message', 'Time Added Successfully.');
  

    }

    public function delete($id, $attendanceid , $reason)
    {
        if (!auth()->user()->can('timelog-create')) {
            abort(403);
        }

        $userName = auth()->user()->name;
        
       
       (new TimeChangeLog)->logaudit($attendanceid, $id, $userName, $reason , "DELETE");

        // Delete the time log
        TimeSheet::destroy($id);

        session()->flash('message', 'Time Deleted Successfully.');
    }
    
}
