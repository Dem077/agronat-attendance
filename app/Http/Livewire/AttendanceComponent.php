<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $isOpen = false;
    
    public $user_id, $ck_date, $in, $out, $start_date, $end_date;


    public function render()
    {
        $attendances=$this->getAttendances()->paginate(5);
        $employees=User::all();
        $this->resetPage();
        return view('livewire.attendances.component',['employees'=>$employees,'attendances'=>$attendances]);
        // ->extends('layouts.app')
        // ->section('content');
        // $this->timesheets = TimeSheet::all();
        // $this->attendances=$this->getAttendances()->get();
        // $this->employees=User::all();
        // return view('livewire.attendances.component');
    }

    public function getAttendances(){
        $attendances=Attendance::addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)])
        ->addSelect(DB::raw("'08:00' as scin"))
        ->addSelect(DB::raw("'16:00' as scout"));
        if($this->start_date){
            $attendances=$attendances->where('ck_date','>=',$this->start_date);
        }
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $attendances=$attendances->where('ck_date','<=',$this->end_date);
        }
        if($this->user_id){
            $attendances=$attendances->where('user_id',$this->user_id);
        }

        return $attendances->orderBy('ck_date','desc');
    }


    public function exportRecord() {
        $entries = $this->getAttendances()->get()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Duty Start'=>'scin',
            'Duty End'=>'scout',
            'Checkin'=>'in',
            'Checkout'=>'out',
            'Latefine'=>'late_min',
            'Status'=>'status'
        );
    
        return export_csv($header, $entries, $filename);
        
    }


    public function exportRecord2(){
        $attendances=Attendance::select('user_id','ck_date','late_min','status')->addSelect(['employee' => User::select('name')->whereColumn('user_id', 'users.id')->limit(1)]);
        if($this->start_date){
            $attendances=$attendances->where('ck_date','>=',$this->start_date);
        }
        $this->end_date=$this->end_date?$this->end_date:(new \DateTime())->format('Y-m-d');
        if($this->end_date){
            $attendances=$attendances->where('ck_date','<=',$this->end_date);
        }
        if($this->user_id){
            $attendances=$attendances->where('user_id',$this->user_id);
        }

        $attendances=$attendances->orderBy('ck_date','asc')->get();

        $atts=[];
        $dates=[];
        $employees=[];
        foreach($attendances as $att){
            $status=['Present'=>0,'Absent'=>0,'Latemin'=>$att->late_min];
            if(!in_array($att->ck_date,$dates)){
                $dates[]=$att->ck_date;
            }
            if(!isset($employees[$att->employee])){
                $employees[$att->employee]=['Present'=>0,'Absent'=>0,'Latemin'=>0];
            }
            switch($att->status){
                case 'Normal':
                    $status['Present']=1;
                    break;
                case 'Late':
                    $status['Present']=1;
                    
                    break;
                case 'Absent':
                    $status['Absent']=1;
                    break;
                case '':
                    $status['Absent']=1;
                    break;

            }

            foreach($status as $k=>$v){
                $employees[$att->employee][$k]+=$v;
            }

            $atts[$att->employee][$att->ck_date]=$status;
        }
        $report=[];
        $header=['name'];
        foreach($employees as $employee=>$stat){
            $report[$employee]=[$employee];
            foreach($dates as $dt){
                if(!in_array($dt,$header)){
                    $header[]=$dt;
                    $header[]='';
                    $header[]='';
                }
                if(isset($atts[$employee][$dt])){
                    foreach($atts[$employee][$dt] as $k=>$v){
                        $report[$employee][]=$v;
                    }
                }else{
                    for($i=0;$i<3;$i++){
                        $report[$employee][]=0;
                    }
                }
            }
            foreach($stat as $k=>$v){
                $report[$employee][]=$v;
            }
        }
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        return export_csv2($header, array_values($report), $filename);
    }


    
    // private function export_csv1($header, $data, $filename) {
    //     // No point in creating the export file on the file-system. We'll stream
    //     // it straight to the browser. Much nicer.
    
    //     // Open the output stream
    //     $fh = fopen('php://output', 'w');
    
    //     // Start output buffering (to capture stream contents)
    //     ob_start();
    
    //     // CSV Header
    //     if(is_array($header)){
    //         fputcsv($fh, array_keys($header));
    //     }
    
    //     // CSV Data
    //     foreach ($data as $row) {
    //         $frow=[];
    //         foreach($header as $k=>$v){
    //             if(isset($row[$v])){
    //                 $frow[]=$row[$v];
    //             }else{
    //                 $frow[]='';
    //             }
                
    //         }
    //         fputcsv($fh, $row);
    //     }
    
    //     // Get the contents of the output buffer
    //     return ob_get_clean();
    
    //     // // Output CSV-specific headers
    //     // header('Pragma: public');
    //     // header('Expires: 0');
    //     // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    //     // header('Cache-Control: private', false);
    //     // header('Content-Type: application/octet-stream');
    //     // header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
    //     // header('Content-Transfer-Encoding: binary');
    
    //     // Stream the CSV data
    //     //exit($string);
    // }

    // public function export_csv($header,$data,$filename)
    // {
    //     $response_headers = [
    //             'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
    //         ,   'Content-type'        => 'text/csv'
    //         ,   'Content-Disposition' => "attachment; filename={$filename}.csv"
    //         ,   'Expires'             => '0'
    //         ,   'Pragma'              => 'public'
    //     ];

        

    // $callback = function() use ($data,$header) 
    //     {
    //         $FH = fopen('php://output', 'w');

    //         // CSV Header
    //         if(is_array($header)){
    //             fputcsv($FH, array_keys($header));
    //         }
        
    //         // CSV Data
    //         foreach ($data as $row) {
    //             $frow=[];
    //             foreach($header as $k=>$v){
    //                 if(isset($row[$v])){
    //                     $frow[]=$row[$v];
    //                 }else{
    //                     $frow[]='';
    //                 }
    //             }
    //             fputcsv($FH, $frow);
    //         }
    //         fclose($FH);
    //     };

    //     return response()->stream($callback, 200, $response_headers);
    // }

}
