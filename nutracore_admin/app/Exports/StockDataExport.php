<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockDataExport implements FromArray, WithHeadings
{

    use Exportable;

    public function __construct($dataArr, $headings)
    {
        $this->dataArr = $dataArr;
        $this->headings = $headings;
    }

    public function array(): array
    {

        $dataArr = $this->dataArr;
        return $dataArr;
    }

    public function headings(): array
    {
        $headings = $this->headings;

        return $headings;
    }

    public function map($row): array
    {

    }
}

