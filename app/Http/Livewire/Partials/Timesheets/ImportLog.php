<?php

namespace App\Http\Livewire\Partials\Timesheets;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\TimeChangeLog;
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

        $this->validate();

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

        $userName = auth()->user()->name;
        foreach($logs['imported_records'] as $importedRecord){
            $attendance = Attendance::where('user_id', $importedRecord['user_id'])
                                    ->where('ck_date', $importedRecord['date'])
                                    ->first();
            if($attendance){
                (new TimeChangeLog)->logaudit(
                    $attendance->id,
                    $importedRecord['time_sheet_id'],
                    $userName,
                    'Imported via CSV',
                    'INSERT'
                );
            }
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

        $alreadyLogged = TimeSheet::withTrashed()
            ->where('user_id', $user_id)
            ->where('punch', $punch)
            ->exists();

        if ($alreadyLogged) {
            return null;
        }

        $data['punch'] = $punch;

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
        $imported_records=[];
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
                    $record = $this->addLog($log);
                    if($record){
                        $imported_records[]=[
                            'time_sheet_id'=>$record->id,
                            'user_id'=>$user_id,
                            'date'=>$punch->format('Y-m-d'),
                        ];
                    }

                }

            }

            fclose($open);
        }

        return ['logs'=>$logs,'user_errors'=>$id_errors,'imported_records'=>$imported_records];
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
