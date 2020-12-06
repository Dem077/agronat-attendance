<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $user_id, $ck_date, $in, $out;
    public $updateMode = false;

    public function render()
    {
        $attendances=Attendance::with('user')->orderBy('ck_date','asc')->paginate(5);
        return view('livewire.attendances.component',['attendances'=>$attendances])
        ->extends('layouts.app')
        ->section('content');
    }

    public function getAttendances($user_id=0,$start_date='',$end_date=''){
        return Attendance::addSelect(
            [
                'employee' => User::select('fullname')
                    ->whereColumn('user_id', 'users.id')
                    ->limit(1)
            ]
    )
    ->addSelect(\DB::raw("'08:00' as duty_start"))
    ->addSelect(\DB::raw("'16:00' as duty_end"))
    ->addSelect(\DB::raw("'0' as late_fine"))
    ->orderBy('ck_date','asc')->get();
    }


    public function exportRecord() {
        $entries = $this->getAttendances()->toArray();
    
        $filename = sprintf('%1$s-%2$s-%3$s', str_replace(' ', '', 'attendances'), date('Ymd'), date('His'));

        $header = array(
            'Employee'=>'employee',
            'Date'=>'ck_date',
            'Duty Start'=>'duty_start',
            'Duty End'=>'duty_end',
            'Checkin'=>'in',
            'Chckout'=>'out',
            'Latefine'=>'late_fine'
        );
    
        return $this->export_csv($header, $entries, $filename);
        
    }
    
    private function export_csv1($header, $data, $filename) {
        // No point in creating the export file on the file-system. We'll stream
        // it straight to the browser. Much nicer.
    
        // Open the output stream
        $fh = fopen('php://output', 'w');
    
        // Start output buffering (to capture stream contents)
        ob_start();
    
        // CSV Header
        if(is_array($header)){
            fputcsv($fh, array_keys($header));
        }
    
        // CSV Data
        foreach ($data as $row) {
            $frow=[];
            foreach($header as $k=>$v){
                if(isset($row[$v])){
                    $frow[]=$row[$v];
                }else{
                    $frow[]='';
                }
                
            }
            fputcsv($fh, $row);
        }
    
        // Get the contents of the output buffer
        return ob_get_clean();
    
        // // Output CSV-specific headers
        // header('Pragma: public');
        // header('Expires: 0');
        // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        // header('Cache-Control: private', false);
        // header('Content-Type: application/octet-stream');
        // header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
        // header('Content-Transfer-Encoding: binary');
    
        // Stream the CSV data
        //exit($string);
    }

    public function export_csv($header,$data,$filename)
    {
        $response_headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => "attachment; filename={$filename}.csv"
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        

    $callback = function() use ($data,$header) 
        {
            $FH = fopen('php://output', 'w');

            // CSV Header
            if(is_array($header)){
                fputcsv($FH, array_keys($header));
            }
        
            // CSV Data
            foreach ($data as $row) {
                $frow=[];
                foreach($header as $k=>$v){
                    if(isset($row[$v])){
                        $frow[]=$row[$v];
                    }else{
                        $frow[]='';
                    }
                }
                fputcsv($FH, $frow);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $response_headers);
    }

}
