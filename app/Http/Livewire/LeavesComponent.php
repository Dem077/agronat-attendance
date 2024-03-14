<?php

namespace App\Http\Livewire;

use App\Jobs\UpdateAttendanceStatus;
use Livewire\Component;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Traits\UserTrait;
use Livewire\WithPagination;
use PDO;

class LeavesComponent extends Component
{
    use WithPagination,UserTrait;
    protected $paginationTheme = 'bootstrap';

    public $start_date,$end_date,$form=['from'=>'','to'=>'','leave_type_id'];
    protected $listeners = ['deleteLeave' => 'delete','leaveCreated'=>'$refresh'];

    public function render()
    {
        $this->setUser();
        $leaves=$this->getLeaves();
        $leave_types=LeaveType::all();
        $this->resetPage();

        return view('livewire.leaves.component',['leaves'=>$leaves,'leave_types'=>$leave_types]);
    }

    public function getLeaves(){
        $leaves=Leave::with(['user','type']);
        if(!$this->user_id){
            $ids=[];
            foreach($this->users as $user){
                $ids[]=$user['id'];
            }

            $leaves=$leaves->whereIn('user_id',$ids);
        }else{
            $leaves=$leaves->where('user_id',$this->user_id);
        }

        return $leaves->orderBy('from','desc')->orderBy('user_id','asc')->paginate(10);
    }

    public function resetInput(){
        $this->form['from']='';
        $this->form['to']='';
        $this->form['leave_type_id']='';
        
    }
    public function store(){
        if(!auth()->user()->can('leave-list')){
            abort(403);
        }
        $validated=$this->validate([
            'user_id'=>'required',
            'form.from'=>'required',
            'form.to'=>'required',
            'form.leave_type_id'=>'required',
        ]);

        $data=[
            'user_id'=>$validated['user_id'],
            'from'=>$validated['form']['from'],
            'to'=>$validated['form']['to'],
            'leave_type_id'=>$validated['form']['leave_type_id'],
        ];
        $leave=Leave::create($data);
        $this->updateAttendance($leave);
        $this->resetInput();
        $this->emit('leaveCreated');

    }

    public function delete($id){
        if(!auth()->user()->can('leave-list')){
            abort(403);
        }
        $leave=Leave::findOrFail($id);
        $leave->where('id',$id)->update(['deleted_by_id'=>auth()->id()]);
        Leave::where('id',$id)->delete();
        $this->updateAttendance($leave);

    }

    public function updateAttendance(Leave $leave){
        UpdateAttendanceStatus::dispatchNow($leave->toArray());
    }
    
}
