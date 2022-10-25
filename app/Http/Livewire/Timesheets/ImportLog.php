<?php

namespace App\Http\Livewire\Timesheets;

use App\Models\Location;
use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportLog extends Component
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
    }
    public function render()
    {
        return view('livewire.timesheets.import-log');
    }

    public function logImport()
    {
        $filepath="C:\htdocs\attendance\storage\app/timesheets/166564282425 Sept 2022 to 10 Oct 2022.csv";

        // $this->validate();

        // $filename = time().$this->sheet->getClientOriginalName();

        // Storage::disk('local')->putFileAs(
        //     'timesheets',
        //     $this->sheet,
        //     $filename
        // );

        // $filepath=Storage::disk('local')->path('timesheets/'.$filename);

        $logs=$this->loadSheet($filepath);

        $this->attendanceService=app()->make(AttendanceService::class);

        foreach($this->date_range as $range){
            $this->attendanceService->recompute($range['start']->format('Y-m-d'),$range['end']->format('Y-m-d'),$range['user_id']);
        }

        $this->emit('logImported'); // Close model to using to jquery

        session()->flash('message', 'Import Successfully.');

    }


    public function populateDateRange($log)
    {
        if(!$log){
            return;
        }
        $date=$log['punch'];
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
        $punch=$data['punch']->format('Y-m-d H:i:s');
        $date=$data['punch']->format('Y-m-d');
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
                throw ValidationException::withMessages(['column' => "column {$th} missing"]);
            }
        }

    }


    public function loadSheet($sheet)
    {

        $logs=[];
        $id_errors=[];
        if (($open = fopen($sheet, "r")) !== FALSE) {
            $data = fgetcsv($open, 1000, ",");
            $this->headerValidate($data);
            while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                $punch=$this->parsePunchTime($data[2]);
                $user_id=$this->getUserId($data[0]);
                if(!$user_id){
                    if(!in_array($data[0],$id_errors)){
                        $id_errors[]=$data[0];
                    }
                }else{
                    $log=[
                        'punch'=>$punch,
                        'sync'=>0,
                        'logged_by'=>auth()->id(),
                        'user_id'=>$user_id
                    ];
                    $logs[]=$log;
                    $this->populateDateRange($log);
                    $this->addLog($log);

                }

            }

            fclose($open);
        }

        return ['logs'=>$logs,'user_errors'=>$id_errors];
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
        $user=User::select('id')->where('location_id',$this->location)->where('external_id',$external_id)->first();

        if(!$user){
            return null;
        }
        $this->users[$external_id]=$user->id;
        return $user->id;
    }
}
