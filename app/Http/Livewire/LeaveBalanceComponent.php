<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Leave;
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
    public $leaveBalances = [];

    public function mount()
    {
        $this->setUser();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.leave-balance.leave-balance', [
            'users' => $this->users,
        ]);
    }

    public function updatedSelectedUser($userId)
    {
        $this->syncLeaveBalances($userId);
        $this->getLeaveBalance($userId);
    }

    public function syncLeaveBalances($userId)
    {
        $user = User::with('leaves')->find($userId);
        if (!$user) {
            return;
        }

        $joinDate = Carbon::parse($user->joined_date);
        
        $currentDate = Carbon::now();
        $endDate = $joinDate->copy()->addYear();
        $leaveTypes = LeaveType::all();
        $isannual_applicable = $currentDate->greaterThan($endDate) ? true : false;
        // dd($isannual_applicable);
        if ($currentDate->greaterThan($endDate)) {
            // Reset the leave balance if the current date exceeds 12 months from the joining date
            foreach ($leaveTypes as $leaveType) {
                LeaveBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                    ],
                    [
                        'allocated_days' => $leaveType->allocated_days ?? 0,
                        'leave_taken' => 0,
                        'leave_balance' => $leaveType->allocated_days ?? 0,
                        'isannual_applicable' => $isannual_applicable,
                    ]
                );
            }
        } else {
            for ($year = $joinDate->year; $year <= $currentDate->year; $year++) {
                $leaveYearStart = $joinDate->copy()->year($year);
                $leaveYearEnd = $leaveYearStart->copy()->addYear()->subDay();

                if ($leaveYearStart->greaterThan($currentDate)) {
                    break;
                }

                $isannual_applicable = $currentDate->greaterThan($endDate) ? true : false;

                foreach ($leaveTypes as $leaveType) {
                    $allocated_days_per_year = $leaveType->allocated_days ?? 0; // Ensure it's not null
                  
                    $leave_taken = $user->leaves()
                        ->where('leave_type_id', $leaveType->id)
                        ->whereBetween('from', [$leaveYearStart->toDateString(), $leaveYearEnd->toDateString()])
                        ->get()
                        ->sum(function ($leave) {
                            return $leave->from->diffInDays($leave->to) + 1;
                        });
                        
                    $leave_balance = $allocated_days_per_year - $leave_taken;
                    
                    LeaveBalance::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $year,
                            'isannual_applicable' => $isannual_applicable,
                        ],
                        [
                            'allocated_days' => $allocated_days_per_year,
                            'leave_taken' => $leave_taken,
                            'leave_balance' => $leave_balance,
                        ]
                    );
                }
            }
        }
    }

    public function getLeaveBalance($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }
        
        $joinDate = Carbon::parse($user->join_date);
        $currentLeaveYearStart = $joinDate->copy()->year(Carbon::now()->year);
        if ($currentLeaveYearStart->greaterThan(Carbon::now())) {
            $currentLeaveYearStart->subYear();
        }
        
        $this->leaveBalances = LeaveBalance::where('user_id', $userId)
            ->with('leaveType')
            ->get()
            ->map(function ($balance) {
               
                return [
                    'leave_type' => $balance->leaveType->title,
                    'leave_type_id' => $balance->leaveType->id,
                    'allocated_days' => $balance->allocated_days,
                    'leave_taken' => $balance->leave_taken,
                    'leave_balance' => $balance->leave_balance,
                    'is_annual_applicable' => $balance->isannual_applicable,
                ];
            })
            ->toArray();
    }
}
