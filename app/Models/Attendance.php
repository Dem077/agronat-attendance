<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ck_date',
        'sc_in',
        'sc_out',
        'in',
        'out',
        'late_min',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function attend_status(){
        return $this->hasOne(AttendStatus::class,'user_id','user_id')->where('ck_date','=',$this->ck_date);
    }
}
