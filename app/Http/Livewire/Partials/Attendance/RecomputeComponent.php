<?php

namespace App\Http\Livewire\Partials\Attendance;

use App\Jobs\RecomputeAttendanceJob;
use App\Models\User;
use App\Services\AttendanceService;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RecomputeComponent extends Component
{
    public $user_id,$from,$to,$users,$in,$out;
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
        $validatedDate = $this->validate([
            'user_id' => 'sometimes',
            'from' => 'required',
            'to' => 'required',
            'in' => 'required',
            'out' => 'required'
        ]);

        $users = $this->user_id ? [$this->user_id] : User::where('active', 1)->pluck('id')->toArray();

        // Progress tracking
        $this->progressKey = 'recompute_progress_' . uniqid();
        cache()->put($this->progressKey, 0, 3600); // expires in 1 hour
        cache()->put($this->progressKey . '_total', count($users), 3600);

        foreach($users as $user_id){
            RecomputeAttendanceJob::dispatch($this->from, $this->to, $user_id, $this->in, $this->out, $this->progressKey);
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

        // Clear all queued jobs (for database queue)
        DB::table('jobs')->delete();

        session()->flash('message', 'Queue and progress cleared.');
    }
}
