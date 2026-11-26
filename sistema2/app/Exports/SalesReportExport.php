<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesReportExport implements FromView, ShouldAutoSize
{
    public function __construct(
        private Collection $sales,
        private array $summary,
        private string $from,
        private string $to
    ) {}

    public function view(): View
    {
        return view('principal.reports.excel', [
            'sales' => $this->sales,
            'summary' => $this->summary,
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }
}