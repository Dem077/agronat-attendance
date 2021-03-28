<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class OtSummary implements FromView, WithTitle
{
    use Exportable;
    private $data;

    public function __construct($data)
    {
        $this->data=$data;
    }
    public function view(): View
    {
        return view('livewire.reports.ot-summary-view',['data'=>$this->data]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Summary';
    }

}