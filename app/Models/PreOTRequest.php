<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOTRequest extends Model
{
    use HasFactory;
    protected $table='pre_ot_requests';
    protected $guarded=[];
}
