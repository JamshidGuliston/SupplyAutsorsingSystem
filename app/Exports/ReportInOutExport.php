<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportInOutExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $prevmods;
    protected $kind;
    protected $days;
    protected $products;
    protected $minusproducts;
    protected $plusproducts;
    protected $takedproducts;
    protected $actualweights;
    protected $isThisMeasureDay;

    public function __construct($prevmods, $kind, $days, $products, $minusproducts, $plusproducts, $takedproducts, $actualweights, $isThisMeasureDay)
    {
        $this->prevmods = $prevmods;
        $this->kind = $kind;
        $this->days = $days;
        $this->products = $products;
        $this->minusproducts = $minusproducts;
        $this->plusproducts = $plusproducts;
        $this->takedproducts = $takedproducts;
        $this->actualweights = $actualweights;
        $this->isThisMeasureDay = $isThisMeasureDay;
    }

    public function array(): array
    {
        $rows = [];

        // Sarlavha
        $rows[] = ['Kirim-Chiqim Hisoboti'];
        $rows[] = ['№ ' . $this->days[0]['month_id'] . '-' . $this->kind->id];
        $rows[] = ['Боғча: ' . $this->kind->kingar_name];
        $rows[] = ['Ой: ' . $this->days[0]['month_name']];
        $rows[] = []; // Bo'sh qator

        // Jadval sarlavhalari - 1-qator
        $headings1 = ['TR', 'Maxsulotlar', "O'lcham", "O'tgan oydan"];
        foreach($this->days as $day){
            $headings1[] = $day->day_number . '-' . $day->month_name . '-' . $day->year_name;
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
            $headings1[] = ''; // colspan uchun
        }
        $headings1[] = 'Jami farqlar';
        $headings1[] = ''; // colspan uchun
        $headings1[] = ''; // colspan uchun
        $rows[] = $headings1;

        // Jadval sarlavhalari - 2-qator
        $headings2 = ['', '', '', ''];
        foreach($this->days as $day){
            $headings2[] = 'kirim';
            $headings2[] = 'chiqim';
            $headings2[] = 'chqiti';
            $headings2[] = 'Jami kirim';
            $headings2[] = 'Jami chiqim';
            $headings2[] = 'Farqi';
            $headings2[] = 'KG';
            $headings2[] = 'Farqi';
            $headings2[] = 'Qoldiq';
        }
        $headings2[] = 'Ortirma';
        $headings2[] = "Yo'qolgan";
        $headings2[] = 'Chqiti';
        $rows[] = $headings2;

        // Ma'lumotlar
        $tr = 1;
        $added = [];
        $losted = [];
        $trashed = [];
        foreach($this->products as $product){
            $added[$product->id] = 0;
            $losted[$product->id] = 0;
            $trashed[$product->id] = 0;
        }
        $plus = [];
        $minus = [];

        foreach($this->products as $product){
            if(!isset($plus[$product->id])){
                $plus[$product->id] = 0;
            }
            if(!isset($minus[$product->id])){
                $minus[$product->id] = 0;
            }

            $rowData = [];
            $rowData[] = $tr++;
            $rowData[] = $product->product_name;
            $rowData[] = 'kg';

            // O'tgan oydan
            if(isset($this->prevmods[$product->id])){
                $rowData[] = $this->prevmods[$product->id];
                $plus[$product->id] += $this->prevmods[$product->id];
            } else {
                $rowData[] = 0;
            }

            // Har bir kun uchun
            foreach($this->days as $day){
                // Kirim
                if(isset($this->plusproducts[$product->id][$day->id])){
                    $rowData[] = $this->plusproducts[$product->id][$day->id];
                    $plus[$product->id] += $this->plusproducts[$product->id][$day->id];
                } else {
                    $this->plusproducts[$product->id][$day->id] = 0;
                    $rowData[] = '';
                }

                // Chiqim
                if(isset($this->minusproducts[$product->id][$day->id])){
                    $rowData[] = $this->minusproducts[$product->id][$day->id];
                    $minus[$product->id] += $this->minusproducts[$product->id][$day->id];
                } else {
                    $this->minusproducts[$product->id][$day->id] = 0;
                    $rowData[] = '';
                }

                // Chqiti
                if(isset($this->takedproducts[$product->id][$day->id])){
                    $rowData[] = $this->takedproducts[$product->id][$day->id];
                    $minus[$product->id] += $this->takedproducts[$product->id][$day->id];
                    $trashed[$product->id] += $this->takedproducts[$product->id][$day->id];
                } else {
                    $rowData[] = '';
                }

                // Jami kirim
                if(isset($plus[$product->id])){
                    $rowData[] = round($plus[$product->id], 3);
                } else {
                    $rowData[] = '';
                }

                // Jami chiqim
                if(isset($minus[$product->id])){
                    $rowData[] = round($minus[$product->id], 3);
                } else {
                    $rowData[] = '';
                }

                // Farqi
                $rowData[] = round($plus[$product->id] - $minus[$product->id], 3);

                // KG (actual weight)
                if(isset($this->actualweights[$product->id][$day->id])){
                    $rowData[] = $this->actualweights[$product->id][$day->id];
                } else {
                    $rowData[] = '';
                }

                // Farqi (actual vs calculated)
                if(isset($this->isThisMeasureDay[$day->id])){
                    if(!isset($this->actualweights[$product->id][$day->id])){
                        $this->actualweights[$product->id][$day->id] = 0;
                    }
                    $difference = round($this->actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]), 3);
                    $rowData[] = $difference;

                    if($difference < 0){
                        $losted[$product->id] = $losted[$product->id] + $difference;
                    } else {
                        $added[$product->id] = $added[$product->id] + $difference;
                        $plus[$product->id] += $difference;
                    }
                } else {
                    $rowData[] = '';
                }

                // Qoldiq
                $minus[$product->id] = ($plus[$product->id] - $minus[$product->id] < 0) ? ($plus[$product->id] - $minus[$product->id]) + $minus[$product->id] : $minus[$product->id];

                if(isset($this->isThisMeasureDay[$day->id]) && $plus[$product->id] - $minus[$product->id] < $this->actualweights[$product->id][$day->id]){
                    $rowData[] = round($this->actualweights[$product->id][$day->id], 3);
                } else {
                    $rowData[] = round($plus[$product->id] - $minus[$product->id], 3);
                }
            }

            // Jami farqlar
            $rowData[] = round($added[$product->id], 3);
            $rowData[] = round($losted[$product->id], 3);
            $rowData[] = round($trashed[$product->id], 3);

            $rows[] = $rowData;
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,  // TR
            'B' => 30, // Maxsulotlar
            'C' => 10, // O'lcham
            'D' => 12, // O'tgan oydan
        ];

        // Har bir kun uchun 9 ta ustun
        $colIndex = 5; // E ustunidan boshlash
        foreach($this->days as $day){
            for($i = 0; $i < 9; $i++){
                $widths[$this->getColumnLetter($colIndex)] = 10;
                $colIndex++;
            }
        }

        // Jami farqlar uchun 3 ta ustun
        $widths[$this->getColumnLetter($colIndex)] = 12;
        $colIndex++;
        $widths[$this->getColumnLetter($colIndex)] = 12;
        $colIndex++;
        $widths[$this->getColumnLetter($colIndex)] = 12;

        return $widths;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Sarlavha
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->getStyle('A2:A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11]
        ]);

        // Jadval sarlavhalari
        $lastColumn = $this->getLastColumn();
        $sheet->getStyle('A6:' . $lastColumn . '7')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FAFFB3']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Ma'lumotlar qatorlari
        $lastRow = count($this->products) + 7;
        $sheet->getStyle('A8:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Mahsulotlar ustunini chapga tekislash
        $sheet->getStyle('B8:B' . $lastRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastColumn = $this->getLastColumn();

                // Sarlavhalarni birlashtiramiz
                if ($lastColumn && $lastColumn != 'A') {
                    $event->sheet->mergeCells('A1:' . $lastColumn . '1');
                    $event->sheet->mergeCells('A2:' . $lastColumn . '2');
                    $event->sheet->mergeCells('A3:' . $lastColumn . '3');
                    $event->sheet->mergeCells('A4:' . $lastColumn . '4');
                }

                // Birinchi qator sarlavhalari uchun merge
                $event->sheet->mergeCells('A6:A7'); // TR
                $event->sheet->mergeCells('B6:B7'); // Maxsulotlar
                $event->sheet->mergeCells('C6:C7'); // O'lcham
                $event->sheet->mergeCells('D6:D7'); // O'tgan oydan

                // Har bir kun uchun 9 ta ustunni merge qilish (1-qator sarlavhalar)
                $colIndex = 5; // E ustunidan boshlash (1-based: A=1, B=2, ..., E=5)
                foreach($this->days as $day){
                    $startCol = $this->getColumnLetter($colIndex);
                    $endCol = $this->getColumnLetter($colIndex + 8); // 9 ta ustun (0 dan 8 gacha)
                    $event->sheet->mergeCells($startCol . '6:' . $endCol . '6');
                    $colIndex += 9; // keyingi kun uchun
                }

                // Jami farqlar uchun merge (3 ta ustun)
                $startCol = $this->getColumnLetter($colIndex);
                $endCol = $this->getColumnLetter($colIndex + 2);
                $event->sheet->mergeCells($startCol . '6:' . $endCol . '6');

                // Text wrapping
                $lastRow = count($this->products) + 7;
                $event->sheet->getDelegate()->getStyle('A6:' . $lastColumn . $lastRow)
                    ->getAlignment()->setWrapText(true);
            }
        ];
    }

    private function getLastColumn()
    {
        // TR, Maxsulotlar, O'lcham, O'tgan oydan = 4
        // Har bir kun uchun 9 ta ustun
        // Jami farqlar uchun 3 ta ustun
        $columnCount = 4 + (count($this->days) * 9) + 3;
        return $this->getColumnLetter($columnCount);
    }

    private function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $columnNumber = intval(($columnNumber - $modulo) / 26);
        }
        return $letter;
    }
}
