<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppliedOt extends Model
{
    use HasFactory;

    protected $fillable = [
        'hash',
        'user_id',
        'ck_date',
        'in',
        'out',
        'ot',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
