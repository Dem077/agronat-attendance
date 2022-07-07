<?php

namespace App\Models;

use App\Jobs\ProcessAttendance;
use App\Jobs\ProcessOvertime;
use App\Models\Overtime;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheet extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'punch',
        'status',
        'sync',
        'logged_by'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function add($data){
        $date1=date('Y-m-d',strtotime($data['punch']));
        $date2=date('Y-m-d',strtotime($data['punch']." +1 day"));
        $user_id=$data['user_id'];
        $log=null;

        $fix=TimeSheet::where('punch','>',$data['punch'])->where('punch','<',$date2)->where('user_id',$user_id)->count();
        if($fix){
            $log=TimeSheet::create($data);

            $today=Attendance::where('ck_date',$date1)->where('user_id',$user_id)->first();
            if($today){
                $today->in=NULL;
                $today->out=NULL;
                $today->save();
            }

            Overtime::where('ck_date',$date1)->where('user_id',$user_id)->delete();

            $records=TimeSheet::whereBetween('punch',[$date1,$date2])->where('user_id',$user_id)->orderBy('punch','asc')->get();

            $status=1;
            foreach($records as $record){
                $status=($status+1)%2;
                $record->status=$status;
                $record->save();
    
                //ProcessOvertime::dispatch($record);
                //ProcessAttendance::dispatch($record);
            }
        }else{
            $count=TimeSheet::where('punch','>=',$date1)->where('punch','<=',$data['punch'])->where('user_id',$user_id)->count();
            $data['status']=$count%2;
            $log=TimeSheet::create($data);

            ProcessOvertime::dispatch($log);
            ProcessAttendance::dispatch($log);
        }

        return $log;
    }
}
