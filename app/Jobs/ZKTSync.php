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
use Illuminate\Support\Facades\Http;

class ZKTSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $from,$to,$user_id,$url,$token,$attendanceService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data=[])
    {
        $this->token=config('sync.token');
        $this->url='http://attendance-sync.agronational.mv/api/attendance-logs';

        if(isset($data['from'])){
            $this->from=$data['from'];
        }else{
            $this->from=today()->format('Y-m-d');
            if(!$this->from){
                $this->from='2020-01-01';
            }
        }
        $this->to=isset($data['to'])?$data['to']." 23:59:59":null;
        $this->user_id=isset($data['user_id'])?$data['user_id']:null;
        $this->attendanceService=new AttendanceService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employee_ids=[];
        if($this->user_id){
            $emp_no=User::find($this->user_id)?->emp_no;
            if($emp_no){
                $employee_ids[]=$emp_no;
            }
        }
        foreach($this->getAttendance($this->from,$this->to,$employee_ids) as $d){
            if(!isset($users[$d['emp_id']])){
                
                $u=User::where('emp_no',$d['emp_id'])->first();
                if(!$u){
                    continue;
                }
                $users[$d['emp_id']]=$u->id;
            }
            $data=["user_id"=>$users[$d['emp_id']],"punch"=>$d['checkin']];
            $this->attendanceService->addLog($data);
        }
    }

    public function getAttendance($from=null,$to=null,$employee_ids=[]){
        $response=Http::withToken($this->token)
                        ->acceptJson()
                            ->post($this->url,[
                                'from_date'=>$from,
                                'to_date'=>$to,
                                'employee_ids'=>$employee_ids
                            ]);
        if($response->successful()){
            return $response['data'];
        }
        return [];
    }
}
