<?php

namespace App\Jobs;

use App\Models\TimeSheet;
use App\Models\User;
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
        }
        $this->to=isset($data['to'])?$data['to']:null;
        $this->user_id=isset($data['user_id'])?$data['user_id']:null;
        $this->db=DB::connection('sqlsrv');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::info('sync');
        $users=[];
        foreach($this->getAttendance($this->from,$this->to,$this->user_id) as $d){
            Log::info('sync1');
            if(!isset($users[$d['USERID']])){
                
                $u=User::where('external_id',$d['USERID'])->first();
                if(!$u){
                    Log::info('user not found',$d);
                    continue;
                }
                $users[$d['USERID']]=$u->id;
            }
            $data=["user_id"=>$users[$d['USERID']],"punch"=>$d['CHECKTIME']];
            TimeSheet::add($data);
        }
    }

    public function getAttendance($from=null,$to=null,$user_id=null){
        $logs=$this->db->table('CHECKINOUT')->where('CHECKTIME','>',$from)->addSelect(['USERID'=>$this->db->table('USERINFO')->select('BadgeNumber')->whereColumn('CHECKINOUT.USERID', 'USERINFO.USERID')->limit(1)]);
        if($to){
            $logs=$logs->where('ChHECKTIME','<=',$to);
        }
        if($user_id){
            $logs=$logs->whereExists(function ($query) use($user_id){
                $query->select(DB::raw(1))
                      ->from('USERINFO')
                      ->whereColumn('USERINFO.USERID', 'CHECKINOUT.USERID')
                      ->where('USERINFO.BadgeNumber',$user_id);
            });
        }
        $logs=$logs->get();
        foreach($logs as $row){
            yield ["USERID"=>$row->USERID,"CHECKTIME"=>$row->CHECKTIME];
        }
    }

    public function getAttendance1($from=null,$to=null){
        $connStr = 
        'odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};' .
        'Dbq=C:\\Workplace\\att.mdb;';

        $dbh = new \PDO($connStr);
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = 
                "SELECT * FROM CHECKINOUT WHERE CHECKTIME > ?";
        $sth = $dbh->prepare($sql);

        // query parameter value(s)
        $params = array($from);

        $sth->execute($params);

        while ($row = $sth->fetch()) {
            yield ["USERID"=>$row['USERID'],"CHECKTIME"=>$row["CHECKTIME"]];
        }
    }
}
