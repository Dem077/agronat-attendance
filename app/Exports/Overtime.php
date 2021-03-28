<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class Overtime implements FromView, WithTitle
{
    use Exportable;
    private $data;
    private $name;

    public function __construct($data,$name)
    {
        $this->data=$data;
        $this->name=$name;
    }
    public function view(): View
    {
        return view('livewire.reports.ot-view',['data'=>$this->data]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->name;
    }

}