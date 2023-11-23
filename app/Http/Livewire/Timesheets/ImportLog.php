<?php

namespace App\Http\Livewire\Timesheets;

use App\Models\Location;
use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Illuminate\Support\Facades\Log;
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
    const PUNCH_TIME='Punch Time';
    const NUMBER='Number';
    const NAME='Name';

    private $headers=[self::NUMBER=>0,self::NAME=>0,self::PUNCH_TIME=>0];

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

        $this->validate();
        set_time_limit(1000);
        $filename = time().$this->sheet->getClientOriginalName();

        Storage::disk('local')->putFileAs(
            'timesheets',
            $this->sheet,
            $filename
        );

        $filepath=Storage::disk('local')->path('timesheets/'.$filename);

        $logs=$this->loadSheet($filepath);

        if($logs['user_errors']){
            throw ValidationException::withMessages(['user_errors' => "invalid user ids: ".implode(',',$logs['user_errors'])]);
        }

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
        foreach($this->headers as $th=>$index){
            $valid=false;
            foreach($data as $index=>$d){
                if($th==preg_replace("/[^a-zA-Z\s]+/", "", $d)){
                    $valid=true;
                    $this->headers[$th]=$index;
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
                $punch=$this->parsePunchTime($data[$this->headers[self::PUNCH_TIME]]);
                if($punch===false){
                    Log::error(["message"=>"invalid punch","data"=>$data,'punch'=>$data[$this->headers[self::PUNCH_TIME]]]);
                    throw ValidationException::withMessages(['message'=>'Invalid punch time '.$data[$this->headers[self::PUNCH_TIME]]]);
                }
                $user_id=$this->getUserId($data[$this->headers[self::NUMBER]]);
                if(!$user_id){
                    if(!in_array($data[$this->headers[self::NUMBER]],$id_errors)){
                        $id_errors[]=$data[$this->headers[self::NUMBER]];
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
        $d=DateTime::createFromFormat('d-M-Y h:i:s A',$date_time);
        if($d===false){
            $d=DateTime::createFromFormat('l-d-M-y h:i:s A',$date_time);
        }
        if($d===false){
            $d=DateTime::createFromFormat('Y-m-d H:i:s',$date_time);
        }

        if($d===false){
            $d=DateTime::createFromFormat('Y-m-d H:i',$date_time);
        }
        return $d;
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
