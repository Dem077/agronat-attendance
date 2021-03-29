<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'supervisor_id'
    ];
    public function employees(){
        return $this->hasMany(User::class,'department_id');
    }

    public function supervisor(){
        return $this->belongsTo(User::class,'supervisor_id');
    }

}
