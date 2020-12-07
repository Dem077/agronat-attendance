<?php

namespace App\Models;

use App\Jobs\ProcessAttendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'punch',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function add($data){
        $date1=date('Y-m-d',strtotime($data['punch']));
        $date2=date('Y-m-d',strtotime($data['punch']." +1 day"));
        $user_id=$data['user_id'];
        $count=TimeSheet::where('punch','>=',$date1)->where('punch','<=',$data['punch'])->where('user_id',$user_id)->count();

        $status=$count%2;
        $fix=TimeSheet::where('punch','>',$data['punch'])->where('punch','<',$date2)->where('user_id',$user_id)->get();
        if($fix){
            //clear Attendance>$data['punch']
            //clear OT>$data['punch']
        }

        $data['status']=$count%2;
        $log=TimeSheet::create($data);

        foreach($fix as $record){
            $status=($status+1)%2;
            $record->status=$status;
            $record->save();
        }
        ProcessAttendance::dispatch($log);
        return $log;
    }
}
