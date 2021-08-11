<?php

namespace App\Jobs;

use App\Models\TimeSheet;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ZKTSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $from,$to,$user_id,$db;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data=[])
    {
        if(isset($data['from'])){
            $this->from=$data['from'];
        }else{
            $this->from=TimeSheet::max('punch');
            if(!$this->from){
                $this->from='2020-01-01';
            }
        }
        $this->to=isset($data['to'])?$data['to']." 23:59:59":null;
        $this->user_id=isset($data['user_id'])?$data['user_id']:null;
        $this->db=DB::connection('sqlsrv');
        $this->attendanceService=new AttendanceService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $users=[];
        $external_id=null;
        if($this->user_id){
            $user=User::find($this->user_id);
            if($user){
                $external_id=$user->emp_no;
                $users[$external_id]=$this->user_id;
            }
        }
        foreach($this->getAttendance($this->from,$this->to,$external_id) as $d){
            if(!isset($users[$d['USERID']])){
                
                $u=User::where('emp_no',$d['USERID'])->first();
                if(!$u){
                    Log::info('user not found',$d);
                    continue;
                }
                $users[$d['USERID']]=$u->id;
            }
            $data=["user_id"=>$users[$d['USERID']],"punch"=>$d['CHECKTIME']];
            $this->attendanceService->addLog($data);
        }
    }

    public function getAttendance($from=null,$to=null,$external_id=null){
        $logs=$this->db->table('CHECKINOUT')->where('CHECKTIME','>',$from)
                    ->whereIn('SENSORID',config('sync.machines'))->addSelect(['USERID'=>$this->db->table('USERINFO')->select('SSN')->whereColumn('CHECKINOUT.USERID', 'USERINFO.USERID')->limit(1)]);
        if($to){
            $logs=$logs->where('CHECKTIME','<=',$to);
        }
        if($external_id){
            $logs=$logs->whereExists(function ($query) use($external_id){
                $query->select(DB::raw(1))
                      ->from('USERINFO')
                      ->whereColumn('USERINFO.USERID', 'CHECKINOUT.USERID')
                      ->where('USERINFO.SSN',$external_id);
            });
        }
        $logs=$logs->orderBy('CHECKTIME','asc')->get();
        foreach($logs as $row){
            yield ["USERID"=>$row->USERID,"CHECKTIME"=>$row->CHECKTIME];
        }
    }
}
