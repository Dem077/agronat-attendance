<?php

namespace App\Http\Livewire\Partials\Timesheets;

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
        $attendanceService=new AttendanceService();
    }
    public function render()
    {
        return view('livewire.partials.timesheets.import-logs');
    }
 
    public function logImport()
    {
        $this->validate();
        $this->sheet->store('timesheets');

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


    public function loadSheet($sheet)
    {

        $logs=[];
        if (($open = fopen($sheet, "r")) !== FALSE) {
            $data = fgetcsv($open, 1000, ",");
            foreach($this->headers as $title){
                if(!in_array($title,$data)){
                    throw ValidationException::withMessages(['sheet' => "{$title} missing in header"]);
                }
            }
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $logs[]=[
                    'punch'=>$this->parsePunchTime($data['Punch Time']),
                    'user_id'=>$this->getUserId($data['Number'])
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
        $user=User::select('id')->whereExternalId($external_id)->first();
        if(!$user){
            throw ValidationException ::withMessages(['user' => "external id {$external_id} not registered"]);
        }
        $this->users[$external_id]=$user->id;
        return $user->id;
    }
}
