<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nid',
        'email',
        'designation',
        'department_id',
        'mobile',
        'phone',
        'emp_no',
        'gender',
        'password',
        'active',
        'external_id',
        'location_id',
        'joined_date',
        'supervisor_id',
        'is_annual_applicable'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function department(){
        return $this->belongsTo(Department::class);
    }


    public function supervisor(){
        return $this->belongsTo(User::class,'supervisor_id');
    }


    public function leaves(){
        return $this->hasMany(Leave::class);
    }

    public function scopeActive($query)
    {
        $query->where('active', 1);
    }

    public function workOnSaturday() {
        return Department::where('id',$this->department_id)
                            ->where('work_on_saturday',1)->exists();
    }

    public function leaveTypes()
    {
        return $this->hasManyThrough(LeaveType::class, Leave::class, 'user_id', 'id', 'id', 'leave_type_id');
    }
}
