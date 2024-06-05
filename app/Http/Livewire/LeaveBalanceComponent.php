<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Leave;
use App\Models\Holiday;
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
    public $dateselected; // Add this line

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
                ->sum(function ($leave) use ($leaveYearStart, $leaveYearEnd, $user) {
                    $leaveStart = Carbon::parse($leave->from);
                    $leaveEnd = Carbon::parse($leave->to);

                    if ($leaveStart->greaterThan($leaveYearEnd) || $leaveEnd->lessThan($leaveYearStart)) {
                        return 0;
                    }

                    $leaveStart = $leaveStart->lessThan($leaveYearStart) ? $leaveYearStart : $leaveStart;
                    $leaveEnd = $leaveEnd->greaterThan($leaveYearEnd) ? $leaveYearEnd : $leaveEnd;

                    $totalDays = 0;

                    for ($date = $leaveStart; $date <= $leaveEnd; $date->addDay()) {
                        $is_holiday = Holiday::where('h_date', $date)->exists();
                        $work_saturday = User::where('id', $user->id)
                            ->whereHas('department', function($q) {
                                $q->where('work_on_saturday', 1);
                            })->exists() && $date->isSaturday();

                        if (!($is_holiday && !$work_saturday)) {
                            $totalDays++;
                        }
                    }

                    return $totalDays;
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
        $header = ['staff id', 'National ID', 'employee','gender', 'Department', 'Joined Date', 'daterange', 'Sick Leave (Without Certificate)', 'Family Leave', 'Annual Leave', 'Duty Travel', 'Virtual Day ', 'Paternity Leave', 'Maternity Leave', 'Release', 'Quarantine leave', 'Circumcision Leave', 'Umra Leave', 'Sick Leave w Certificate'];
        $users = User::select(DB::raw("id, nid, name, emp_no, joined_date, department_id"))
                    ->active()
                    ->where('joined_date', '<=', $this->dateselected)
                    ->orderBy('emp_no', 'asc')
                    ->get();

        $leavebalance = [];
        $userBalance = [];
        $leaveTypes = LeaveType::all();
        foreach ($users as $user) {
            $userId = $user->id;

            // Check if leave balance already exists
            if (LeaveBalance::where('user_id', $userId)->exists()) {
                $allbalance = LeaveBalance::where('user_id', $userId)
                    ->get()
                    ->filter(function ($balance) {
                        $yearRange = explode('-', $balance->year);
                        $startDate = Carbon::createFromFormat('Y_m_d', $yearRange[0]);
                        $endDate = Carbon::createFromFormat('Y_m_d', $yearRange[1]);
                        $selectedDate = Carbon::createFromFormat('Y-m-d', $this->dateselected);
                        return $selectedDate->between($startDate, $endDate);
                    })
                    ->map(function ($balance) {
                        $yearRange = explode('-', $balance->year);
                        $startDate = Carbon::createFromFormat('Y_m_d', $yearRange[0]);
                        $endDate = Carbon::createFromFormat('Y_m_d', $yearRange[1]);
                        $selectedDate = Carbon::createFromFormat('Y-m-d', $this->dateselected);
                        return [
                            'leave_type' => $balance->leaveType->title,
                            'leave_type_id' => $balance->leaveType->id,
                            'user_gender' => $balance->user->gender,
                            'allocated_days' => $balance->allocated_days,
                            'year' => $balance->year,
                            'selected_date'=>$selectedDate,
                            'end_date' => $endDate,
                            'start_date' => $startDate,
                        ];
                    })
                    ->keyBy('leave_type_id')
                    ->toArray();
                    
                    foreach ($leaveTypes as $leaveType) {
                        if (!isset($allbalance[$leaveType->id])) {
                            continue;
                        }
                    
                        if (!isset($allbalance[$leaveType->id]['start_date']) || !isset($allbalance[$leaveType->id]['selected_date'])) {
                            continue;
                        }
                    
                        $leaveYearStart = $allbalance[$leaveType->id]['start_date'];
                        $leaveYearEnd = $allbalance[$leaveType->id]['selected_date'];
                    
                        $leave_taken = $user->leaves()
                            ->where('leave_type_id', $leaveType->id)
                            ->get()
                            ->sum(function ($leave) use ($leaveYearStart, $leaveYearEnd, $user) {
                                $leaveStart = Carbon::parse($leave->from);
                                $leaveEnd = Carbon::parse($leave->to);
                    
                                if ($leaveStart->greaterThan($leaveYearEnd) || $leaveEnd->lessThan($leaveYearStart)) {
                                    return 0;
                                }
                    
                                $leaveStart = $leaveStart->lessThan($leaveYearStart) ? $leaveYearStart : $leaveStart;
                                $leaveEnd = $leaveEnd->greaterThan($leaveYearEnd) ? $leaveYearEnd : $leaveEnd;
                    
                                $totalDays = 0;
                    
                                for ($date = $leaveStart; $date <= $leaveEnd; $date->addDay()) {
                                    $is_holiday = Holiday::where('h_date', $date)->exists();
                                    $work_saturday = User::where('id', $user->id)
                                        ->whereHas('department', function($q) {
                                            $q->where('work_on_saturday', 1);
                                        })->exists() && $date->isSaturday();
                    
                                    if (!($is_holiday && !$work_saturday)) {
                                        $totalDays++;
                                    }
                                }
                    
                                return $totalDays;
                            });
                    
                        $allbalance[$leaveType->id]['leave_balance'] = $allbalance[$leaveType->id]['allocated_days'] - $leave_taken;
                    }
                    
            } else {
                $this->syncLeaveBalances($userId);

                $allbalance = LeaveBalance::where('user_id', $userId)
                    ->get()
                    ->filter(function ($balance) {
                        $yearRange = explode('-', $balance->year);
                        $startDate = Carbon::createFromFormat('Y_m_d', $yearRange[0]);
                        $endDate = Carbon::createFromFormat('Y_m_d', $yearRange[1]);
                        $selectedDate = Carbon::createFromFormat('Y_m_d', $this->dateselected);
                        return $selectedDate->between($startDate, $endDate);
                    })
                    ->map(function ($balance) {
                        $yearRange = explode('-', $balance->year);
                        $startDate = Carbon::createFromFormat('Y_m_d', $yearRange[0]);
                        $endDate = Carbon::createFromFormat('Y_m_d', $yearRange[1]);
                        $selectedDate = Carbon::createFromFormat('Y_m_d', $this->dateselected);
                        return [
                            'leave_type' => $balance->leaveType->title,
                            'leave_type_id' => $balance->leaveType->id,
                            'user_gender' => $balance->user->gender,
                            'allocated_days' => $balance->allocated_days,
                            'year' => $balance->year,
                            'end_date' => $endDate,
                            'start_date' => $startDate,
                        ];
                    })
                    ->keyBy('leave_type_id')
                    ->toArray();

                    foreach ($leaveTypes as $leaveType) {
                        if (!isset($allbalance[$leaveType->id])) {
                            continue;
                        }
                    
                        if (!isset($allbalance[$leaveType->id]['start_date']) || !isset($allbalance[$leaveType->id]['selected_date'])) {
                            continue;
                        }
                    
                        $leaveYearStart = $allbalance[$leaveType->id]['start_date'];
                        $leaveYearEnd = $allbalance[$leaveType->id]['selected_date'];
                    
                        $leave_taken = $user->leaves()
                            ->where('leave_type_id', $leaveType->id)
                            ->get()
                            ->sum(function ($leave) use ($leaveYearStart, $leaveYearEnd, $user) {
                                $leaveStart = Carbon::parse($leave->from);
                                $leaveEnd = Carbon::parse($leave->to);
                    
                                if ($leaveStart->greaterThan($leaveYearEnd) || $leaveEnd->lessThan($leaveYearStart)) {
                                    return 0;
                                }
                    
                                $leaveStart = $leaveStart->lessThan($leaveYearStart) ? $leaveYearStart : $leaveStart;
                                $leaveEnd = $leaveEnd->greaterThan($leaveYearEnd) ? $leaveYearEnd : $leaveEnd;
                    
                                $totalDays = 0;
                    
                                for ($date = $leaveStart; $date <= $leaveEnd; $date->addDay()) {
                                    $is_holiday = Holiday::where('h_date', $date)->exists();
                                    $work_saturday = User::where('id', $user->id)
                                        ->whereHas('department', function($q) {
                                            $q->where('work_on_saturday', 1);
                                        })->exists() && $date->isSaturday();
                    
                                    if (!($is_holiday && !$work_saturday)) {
                                        $totalDays++;
                                    }
                                }
                    
                                return $totalDays;
                            });
                    
                        $allbalance[$leaveType->id]['leave_balance'] = $allbalance[$leaveType->id]['allocated_days'] - $leave_taken;
                    }
                    
            }
            // if (empty($allbalance)) {
            //     continue; 
            // }

            $userBalance = [
                'staff id' => $user->emp_no,
                'nid' => $user->nid,
                'employee' => $user->name,
                'gender' => $user->gender,
                'department' => $user->department->name,
                'Joined Date' => $user->joined_date
            ];

            foreach ($allbalance as $bal) {
                $userBalance['daterange'] = $bal['year'];
                if($bal['user_gender'] === 'M' && $bal['leave_type_id'] == 7){
                    $userBalance[$bal['leave_type']]="NA";
                }
                else if($bal['user_gender'] === 'F' && $bal['leave_type_id'] == 6){
                    $userBalance[$bal['leave_type']]="NA";
                }else{
                    $userBalance[$bal['leave_type']] = $bal['leave_balance'];
                }
                
            }

            $leavebalance[] = $userBalance;
        }

        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'leaves_balance'), date('Ymd'), date('His'));

        return export_csv2($header, $leavebalance, $filename);
    }
}
