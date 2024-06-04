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
                    ]
                );
            }

            $leaveYearStart->addYear();
            $isannual_applicable = true;
        }

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
}
