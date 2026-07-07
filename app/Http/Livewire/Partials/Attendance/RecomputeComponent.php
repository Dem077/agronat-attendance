<?php

namespace App\Http\Livewire\Partials\Attendance;

use App\Jobs\RecomputeAttendanceJob;
use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Livewire\Component;

class RecomputeComponent extends Component
{
    public $user_id,$from,$to,$users,$in,$out,$late_grace_minutes;
    protected $listeners = ['stopAndClearQueue'];
    public $progressKey;
    public $progress = [
        'completed' => 0,
        'total' => 0,
        'percent' => 0
    ];
    public function render()
    {
        if(!isset($this->in) || !isset($this->out)){
            $attendanceService=new AttendanceService();
            $this->in=$attendanceService->schedule['in'];
            $this->out=$attendanceService->schedule['out'];
        }
        return view('livewire.attendances.recompute');
    }

    public function recompute(){
        set_time_limit(1500);

        if(!auth()->user()->can('timelog-create')){
            abort(403);
        }
        $this->validate([
            'user_id' => 'sometimes',
            'from' => 'required',
            'to' => 'required',
            'in' => 'required',
            'out' => 'required',
            'late_grace_minutes' => 'nullable|integer|min:0|max:480',
        ]);

        $users = $this->user_id ? [$this->user_id] : User::where('active', 1)->pluck('id')->toArray();

        $lateGrace = $this->late_grace_minutes !== '' && $this->late_grace_minutes !== null
            ? (int) $this->late_grace_minutes
            : null;

        // Progress tracking
        $this->progressKey = 'recompute_progress_' . uniqid();
        cache()->put($this->progressKey, 0, 3600); // expires in 1 hour
        cache()->put($this->progressKey . '_total', count($users), 3600);

        foreach (array_chunk($users, 10) as $userChunk) {
            RecomputeAttendanceJob::dispatch(
                $this->from,
                $this->to,
                $userChunk,
                $this->in,
                $this->out,
                $this->progressKey,
                $lateGrace
            );
        }

        $this->emit('.Recomputed');
        session()->flash('message', 'Recompute Added To Queue.');
    }

    public function getProgress()
    {
        if (!$this->progressKey) return;
        $completed = cache()->get($this->progressKey, 0);
        $total = cache()->get($this->progressKey . '_total', 0);
        $percent = $total ? round(($completed / $total) * 100, 2) : 0;
        $this->progress = [
            'completed' => $completed,
            'total' => $total,
            'percent' => $percent
        ];
    }

    public function stopAndClearQueue()
    {
        // Clear progress tracking
        if ($this->progressKey) {
            cache()->forget($this->progressKey);
            cache()->forget($this->progressKey . '_total');
            $this->progressKey = null;
            $this->progress = [
                'completed' => 0,
                'total' => 0,
                'percent' => 0
            ];
        }

        DB::table('jobs')->delete();

        foreach (['default', 'attendance', 'recompute'] as $queue) {
            Queue::connection('redis')->clear($queue);
        }

        session()->flash('message', 'Queue and progress cleared.');
    }
}
