<?php

function export_csv($header,$data,$filename)
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

function export_csv2($header,$data,$filename)
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
                fputcsv($FH, $header);
            }
        
            // CSV Data
            foreach ($data as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

    return response()->stream($callback, 200, $response_headers);
}

function date_range($from,$to,$gap="1 day"){
    $interval = DateInterval::createFromDateString($gap);
    if(gettype($from)=='string'){
        $from=new DateTime($from);
    }
    if(gettype($to)=='string'){
        $to=new DateTime($to);
    }
    return new DatePeriod($from, $interval,$to->modify('+1 day'));
}