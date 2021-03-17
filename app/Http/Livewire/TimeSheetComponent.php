<?php

namespace App\Http\Livewire;

use App\Jobs\ZKTSync;
use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TimeSheetComponent extends Component
{

    use WithPagination,UserTrait;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;
    private $attendanceService;

    public $punchdate,$punchtime,$start_date,$end_date,$sync_data=['user_id'=>'','from'=>'','to'=>''];


    public function __construct()
    {
        $this->attendanceService=new AttendanceService();
        parent::__construct();
    }

    public function render()
    {
        $this->setUser();
        $logs=$this->getTimeSheet()->paginate(10);
        $this->resetPage();
        return view('livewire.timesheets.component',['logs'=>$logs]);
    }

    public function getTimeSheet(){
        $timesheet=TimeSheet::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)])
                        ->addSelect(DB::raw("(CASE when status=0 Then 'IN' ELSE 'OUT' END) AS IN_OUT"));
        if($this->start_date){
            $timesheet=$timesheet->where('punch','>=',$this->start_date);
        }
        if($this->end_date){
            $timesheet=$timesheet->where('punch','<=',$this->end_date);
        }
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }
            $timesheet=$timesheet->whereIn('user_id',$ids);
        }else{
            $timesheet=$timesheet->where('user_id',$this->user_id);
        }

        return $timesheet->orderBy('punch','desc');
    }

    

    public function exportRecord() {
        $entries = $this->getTimeSheet()->get()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'timesheet'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Punch Time'=>'punch',
            'IN/OUT'=>'IN_OUT',
        );
    
        return export_csv($header, $entries, $filename);
        
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->user_id = '';
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
            'punchtime' => 'required'
        ]);

        $validatedDate['punch']="{$validatedDate['punchdate']} {$validatedDate['punchtime']}";
        unset($validatedDate['punchdate']);
        unset($validatedDate['punchtime']);
        // TimeSheet::add($validatedDate);

        $validatedDate['sync']=0;
        $this->attendanceService->addLog($validatedDate);

        $this->emit('.Store'); // Close model to using to jquery

        session()->flash('message', 'Time Added Successfully.');
  
        $this->resetInputFields();


    }

    public function delete($id)
    {
        TimeSheet::destroy($id);
        session()->flash('message', 'Time Deleted Successfully.');
    }
}
