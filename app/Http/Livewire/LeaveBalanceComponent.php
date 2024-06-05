<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Leave;
use Illuminate\Support\Facades\DB;
use App\Traits\UserTrait;
use Livewire\WithPagination;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class LeaveBalanceComponent extends Component
{
    use WithPagination, UserTrait;

    public $selectedUser;
    public $leaveBalances;
    public $dateRanges = [];
    public $selectedDateRange;

    public function mount()
    {
        $this->setUser();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.leave-balance.leave-balance', [
            'users' => $this->users,
            'dateRanges' => $this->dateRanges,
        ]);
    }

    public function updatedSelectedUser($userId)
    {
        $this->syncLeaveBalances($userId);
        $this->getLeaveBalance($userId);
    }

    public function updatedSelectedDateRange($dateRangeKey)
    {
        $this->getLeaveBalance($this->selectedUser);
    }

    public function syncLeaveBalances($userId)
    {
        $user = User::with('leaves')->find($userId);
        if (!$user) {
            return;
        }

        $leaveData = [];
        $joinDate = Carbon::parse($user->joined_date);
        $currentDate = Carbon::now();
        $leaveTypes = LeaveType::all();

        $leaveYearStart = $joinDate->copy();
        $isannual_applicable = false;
        $this->dateRanges = [];
        while ($leaveYearStart->lessThanOrEqualTo($currentDate)) {
            $leaveYearEnd = $leaveYearStart->copy()->addYear()->subDay();
            
            $dateRangeKey = $leaveYearStart->format('Y_m_d') . '-' . $leaveYearEnd->format('Y_m_d');
            $this->dateRanges[] = $dateRangeKey;

            foreach ($leaveTypes as $leaveType) {
                $allocated_days_per_year = $leaveType->allocated_days ?? 0;

                $leave_taken = $user->leaves()
                    ->where('leave_type_id', $leaveType->id)
                    ->get()
                    ->sum(function ($leave) use ($leaveYearStart, $leaveYearEnd) {
                        $leaveStart = Carbon::parse($leave->from);
                        $leaveEnd = Carbon::parse($leave->to);

                        if ($leaveStart->greaterThan($leaveYearEnd) || $leaveEnd->lessThan($leaveYearStart)) {
                            return 0;
                        }

                        $leaveStart = $leaveStart->lessThan($leaveYearStart) ? $leaveYearStart : $leaveStart;
                        $leaveEnd = $leaveEnd->greaterThan($leaveYearEnd) ? $leaveYearEnd : $leaveEnd;

                        return $leaveStart->diffInDays($leaveEnd) + 1;
                    });

                $leave_balance = $allocated_days_per_year - $leave_taken;

                $leaveData[$dateRangeKey][] = [
                    'leave_type_id' => $leaveType->id,
                    'allocated_days' => $allocated_days_per_year,
                    'leave_taken' => $leave_taken,
                    'leave_balance' => $leave_balance,
                    'isannual_applicable' => $isannual_applicable,
                ];
                
                $isdaterangecurr = ($leaveYearStart->copy())->addYear()->lessThanOrEqualTo($currentDate) ? False  : true;
                $isdataexist = LeaveBalance::where('user_id', $user->id)
                ->where('year', $dateRangeKey)
                ->where('leave_type_id', $leaveType->id)
                ->exists();
            
                if (!$isdataexist) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $dateRangeKey,
                        'allocated_days' => $allocated_days_per_year,
                        'leave_taken' => $leave_taken,
                        'leave_balance' => $leave_balance,
                        'isannual_applicable' => $isannual_applicable,
                    ]);
                }
                if($isdaterangecurr == true){
                    LeaveBalance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $dateRangeKey,
                        ],
                        [
                            'allocated_days' => $allocated_days_per_year,
                            'leave_taken' => $leave_taken,
                            'leave_balance' => $leave_balance,
                            'isannual_applicable' => $isannual_applicable,
                            'currunt_year' => true
                        ]
                    );
                }
                
                if ($isdaterangecurr == false) {
                    LeaveBalance::where([
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $dateRangeKey,
                    ])->update([
                        'currunt_year' => false
                    ]);
                }
            }

            $leaveYearStart->addYear();
            $isannual_applicable = true;
        }
        // dd($leaveData);
        return $leaveData;
    }

    public function getLeaveBalance($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

       
        $this->leaveBalances = LeaveBalance::where('user_id', $userId)
            ->where('year', $this->selectedDateRange)
            ->with('leaveType')
            ->get()
            ->map(function ($balance) {
                return [
                    'leave_type' => $balance->leaveType->title,
                    'leave_type_id' => $balance->leaveType->id,
                    'user_gender' => $balance->user->gender,
                    'allocated_days' => $balance->allocated_days,
                    'leave_taken' => $balance->leave_taken,
                    'leave_balance' => $balance->leave_balance,
                    'joined_date_added' => $balance->user->joined_date,
                    'is_annual_applicable' => $balance->isannual_applicable,
                ];
            })
            ->toArray();
            
    }


    public function exportleave()
    {
        $header = ['staff id','National ID', 'employee','Department','Joined Date','daterange' ,'Sick Leave (Without Certificate)', 'Family Leave', 'Annual Leave', 'Duty Travel', 'Virtual Day ', 'Paternity Leave', 'Maternity Leave', 'Release', 'Quarantine leave', 'Circumcision Leave', 'Umra Leave','Sick Leave w Certificate'];
        $users = User::select(DB::raw("id, nid, name, emp_no, joined_date, department_id"))->active()->orderBy('emp_no', 'asc')->get();
    
        $leavebalance = [];
        $userBalance =[];
        foreach ($users as $user) {
            $userId = $user->id;
    
            // Check if leave balance already exists
            if (LeaveBalance::where('user_id', $userId)->exists()) {
       
                $allbalance = LeaveBalance::where('user_id', $userId)
                    ->where('currunt_year',True)  
                    ->with('leaveType')
                    ->get()
                    ->map(function ($balance) {
                        return [
                            'leave_type' => $balance->leaveType->title,
                            'leave_type_id' => $balance->leaveType->id,
                            'user_gender' => $balance->user->gender,
                            'allocated_days' => $balance->allocated_days,
                            'leave_taken' => $balance->leave_taken,
                            'leave_balance' => $balance->leave_balance,
                            'joined_date_added' => $balance->user->joined_date,
                            'year'=>$balance->year,
                        ];
                    })
                    ->toArray();
            } else {
                $this->syncLeaveBalances($userId);
          
                $allbalance = LeaveBalance::where('user_id', $userId)
                    ->where('currunt_year',True)   
                    ->with('leaveType')
                    ->get()
                    ->map(function ($balance) {
                        return [
                            'leave_type' => $balance->leaveType->title,
                            'leave_type_id' => $balance->leaveType->id,
                            'user_gender' => $balance->user->gender,
                            'allocated_days' => $balance->allocated_days,
                            'year'=>$balance->year,
                            'leave_taken' => $balance->leave_taken,
                            'leave_balance' => $balance->leave_balance,
                        ];
                    })
                    ->toArray();
                    
            }
    
            $userBalance = [
                'staff id' => $user->emp_no,
                'employee' => $user->nid,
                'employee' => $user->name,
                'department' => $user->department->name,
                'Joined Date' => $user->joined_date
                
            ];
            foreach ($allbalance as $bal) {
                $userBalance['daterange'] = $bal['year'];
                $userBalance[$bal['leave_type']] = $bal['leave_balance'];
                
            }
    
            $leavebalance[] = $userBalance;
        }
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'leaves_balance'), date('Ymd'), date('His'));
    
        return export_csv2($header, $leavebalance, $filename);
    }
    
}
