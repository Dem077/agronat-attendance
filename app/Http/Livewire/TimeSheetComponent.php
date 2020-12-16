<?php

namespace App\Http\Livewire;

use App\Models\TimeSheet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TimeSheetComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $updateMode = false;

    public $user_id,$punchdate,$punchtime,$start_date,$end_date;


    public function render()
    {
        $employees=User::all();
        $logs=$this->getTimeSheet()->paginate(5);
        return view('livewire.timesheets.component',['logs'=>$logs,'employees'=>$employees]);
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
        if($this->user_id){
            $timesheet=$timesheet->where('user_id','>=',$this->user_id);
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
        $validatedDate = $this->validate([
            'user_id' => 'required',
            'punchdate' => 'required',
            'punchtime' => 'required'
        ]);

        $validatedDate['punch']="{$validatedDate['punchdate']} {$validatedDate['punchtime']}";
        unset($validatedDate['punchdate']);
        unset($validatedDate['punchtime']);
        TimeSheet::add($validatedDate);
  
        $this->emit('.Store'); // Close model to using to jquery

        session()->flash('message', 'Time Added Successfully.');
  
        $this->resetInputFields();


    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        TimeSheet::find($id)->delete();
        session()->flash('message', 'Time Deleted Successfully.');
    }
}
