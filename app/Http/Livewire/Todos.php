<?php

namespace App\Http\Livewire;

use App\Models\TimeSheet;
use Livewire\Component;
use Livewire\WithPagination;

class Todos extends Component
{
    use WithPagination;
    public $user_id, $punch, $punch_id;
    public $isOpen = 0;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function render()
    {
        $timesheets = TimeSheet::paginate(3);
        return view('livewire.timesheets.component',compact('timesheets'));
    }
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function openModal()
    {
        $this->isOpen = true;
    }
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function closeModal()
    {
        $this->isOpen = false;
    }
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->user_id = '';
        $this->punch = '';
        $this->punch_id = '';
    }
      
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function store()
    {
        $this->validate([
            'user_id' => 'required',
            'punch' => 'required',
        ]);
    
        TimeSheet::updateOrCreate(['id' => $this->punch_id], [
            'user_id' => $this->user_id,
            'punch' => $this->punch
        ]);
   
        session()->flash('message', 
            $this->todo_id ? 'TimeSheet Updated Successfully.' : 'Todo Created Successfully.');
   
        $this->closeModal();
        $this->resetInputFields();
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function edit($id)
    {
        $TimeSheet = TimeSheet::findOrFail($id);
        $this->punch_id = $id;
        $this->user_id = $TimeSheet->user_id;
        $this->punch = $TimeSheet->punch;
     
        $this->openModal();
    }
      
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function delete($id)
    {
        TimeSheet::find($id)->delete();
        session()->flash('message', 'Todo Deleted Successfully.');
    }
}
