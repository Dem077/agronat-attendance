<?php

namespace App\Http\Livewire\Partials\Timesheets;

use App\Models\Location;
use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportLogs extends Component
{
    use WithFileUploads;

    private $attendanceService;
    private $date_range=[];
    public $sheet,$location;
    public $locations=[];
    public $users=[];
    protected $rules=[
        'location'=>'required',
        'sheet' => 'required|mimes:csv,txt|max:1024'
    ];
    private $headers=['Number','Name','Punch Time','Work State','Terminal','Punch Type'];

    public function mount()
    {
        $this->locations=Location::all();
        $attendanceService=new AttendanceService();
    }
    public function render()
    {
        return view('livewire.partials.timesheets.import-logs');
    }
 
    public function logImport()
    {
        dd($this->loadSheet(storage_path().'/app/livewire-tmp/kbPru7eH4y8KcrX8521drwLUVakfrH-metaMjVBdWd0bzE4U2VwMjAyMi5jc3Y=-.txt'));

        $this->validate();

        $this->sheet->store('timesheets');

        $this->loadSheet($this->sheet);

        foreach($this->date_range as $range){
            $this->attendanceService->recompute($range['start'],$range['end'],$range['user_id']);
        }

        $this->emit('logImported'); // Close model to using to jquery

        session()->flash('message', 'Sync Successfully.');
 
    }

    public function import($logs)
    {
        foreach($logs as $log){
            $this->populateDateRange($this->addLog($log));
        }
    }

    public function populateDateRange($log)
    {
        if(!$log){
            return;
        }
        $date=date('Y-m-d',strtotime($log['punch']));
        if(!isset($this->date_range[$log['user_id']])){
            $this->date_range[$log['user_id']]=['user_id'=>$log['user_id'],'start'=>$date,'end'=>$date];
        }else{
            $range=$this->date_range[$log['user_id']];
            if($date>$range['end']){
                $this->date_range[$log['user_id']]['end']=$date;
            }

            if($date<$range['start']){
                $this->date_range[$log['user_id']]['start']=$date;
            }
        }

    }

    public function addLog($data)
    {
        $punch=date('Y-m-d H:i:s',strtotime($data['punch']));
        $date=date('Y-m-d',strtotime($data['punch']));
        $day_end=new DateTime($date." 23:59:59");
        $user_id=$data['user_id'];

        $fix=TimeSheet::withTrashed()->where('punch','>=',$data['punch'])->where('punch','<',$day_end)->where('user_id',$user_id)->pluck('punch')->toArray();

        if(in_array($punch,$fix)){
            return null;
        }

        return TimeSheet::create($data);

    }

    public function headerValidate($data)
    {
        
        foreach($this->headers as $th){
            $valid=false;
            foreach($data as $d){
                if($th==preg_replace("/[^a-zA-Z\s]+/", "", $d)){
                    $valid=true;
                    break;
                }
            }
            if(!$valid){
                throw ValidationException ::withMessages(['column' => "column {$th} missing"]);
            }
        }

    }


    public function loadSheet($sheet)
    {

        $logs=[];
        if (($open = fopen($sheet, "r")) !== FALSE) {
            $data = fgetcsv($open, 1000, ",");
            $this->headerValidate($data);
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $logs[]=[
                    'punch'=>$this->parsePunchTime($data[2]),
                    'user_id'=>$this->getUserId($data[0])
                ];
            }

            fclose($open);
        }

        return $logs;
    }

    public function parsePunchTime($date_time)
    {
        return DateTime::createFromFormat('l-d-M-y h:i:s A',$date_time);
    }

    public function getUserId($external_id)
    {
        if(isset($this->users[$external_id])){
            return $this->users[$external_id];
        }
        $user=User::select('id')->where('location_id',$this->location)->whereExternalId($external_id)->first();
        if(!$user){
            throw ValidationException ::withMessages(['user' => "external id {$external_id} not registered"]);
        }
        $this->users[$external_id]=$user->id;
        return $user->id;
    }
}
