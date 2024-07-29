<?php

namespace App\Http\Livewire;

use App\Jobs\UpdateAttendanceStatus;
use Livewire\Component;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\Holiday;
use App\Traits\UserTrait;
use Carbon\Carbon;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class LeavesComponent extends Component
{
    use WithPagination, UserTrait, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date, $remark, $attachment, $days_count;
    public $form = ['from' => '', 'to' => '', 'leave_type_id'];
    protected $listeners = ['deleteLeave' => 'delete', 'leaveCreated' => '$refresh'];

    public function render()
    {
        $this->setUser();
        $leaves = $this->getLeaves();
        $leave_types = LeaveType::all();
        $this->resetPage();

        return view('livewire.leaves.component', ['leaves' => $leaves, 'leave_types' => $leave_types]);
    }

    public function getLeaves()
    {
        $leaves = Leave::with(['user', 'type']);
        if (!$this->user_id) {
            $ids = [];
            foreach ($this->users as $user) {
                $ids[] = $user['id'];
            }
            $leaves = $leaves->whereIn('user_id', $ids);
        } else {
            $leaves = $leaves->where('user_id', $this->user_id);
        }

        return $leaves->orderBy('from', 'desc')->orderBy('user_id', 'asc')->paginate(10);
    }

    public function resetInput()
    {
        $this->form['from'] = '';
        $this->form['to'] = '';
        $this->form['leave_type_id'] = '';
        $this->remark = '';
        $this->attachment = null;
    }

    public function store()
    {
        if (!auth()->user()->can('leave-list')) {
            abort(403);
        }
    
        $validated = $this->validate([
            'user_id' => 'required',
            'form.from' => 'required|date',
            'form.to' => 'required|date',
            'form.leave_type_id' => 'required',
            'remark' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048',
        ]);
    
        $days_count = 0;
        $fromDate = Carbon::parse($validated['form']['from']);
        $toDate = Carbon::parse($validated['form']['to']);
    
        $currentDate = $fromDate->copy();
        while ($currentDate <= $toDate) {
            $is_holiday = Holiday::where('h_date', $currentDate->format('Y-m-d'))->exists();
            $work_saturday = User::where('id', $validated['user_id'])
                ->whereHas('department', function ($q) {
                    $q->where('work_on_saturday', 1);
                })->exists() && $currentDate->isSaturday();
    
            if (!$is_holiday) {
                $days_count++;
            }
            if ($work_saturday && $currentDate->isSaturday()) {
                $days_count++;
            }
    
            $currentDate->addDay();
        }
        $this->days_count = $days_count;
    
        $data = [
            'user_id' => $validated['user_id'],
            'from' => $validated['form']['from'],
            'to' => $validated['form']['to'],
            'leave_type_id' => $validated['form']['leave_type_id'],
            'remark' => $this->remark,
            'day_count' => $this->days_count
        ];
    
        if ($this->attachment) {
            $filePath = $this->attachment->store('public');
            if (!$filePath) {
                session()->flash('error', 'Failed to upload file.');
                return;
            }
            $data['attachment'] = $filePath;
        }
    
        $leave = Leave::create($data);
        $this->updateAttendance($leave);
        $this->resetInput();
        $this->emit('leaveCreated');
    }

    public function delete($id)
    {
        if (!auth()->user()->can('leave-list')) {
            abort(403);
        }
        $leave = Leave::findOrFail($id);
        $leave->where('id', $id)->update(['deleted_by_id' => auth()->id()]);
        Leave::where('id', $id)->delete();
        $this->updateAttendance($leave);
    }

    public function updateAttendance(Leave $leave)
    {
        UpdateAttendanceStatus::dispatchNow($leave->toArray());
    }
}

