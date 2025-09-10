<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransportationExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $request;
    protected $id;
    protected $start;
    protected $end;
    protected $costid;
    protected $days;
    protected $costs;
    protected $number_childrens;
    protected $kindgar;
    protected $ages;

    public function __construct($request, $id, $start, $end, $costid, $days, $costs, $number_childrens, $kindgar, $ages)
    {
        $this->request = $request;
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
        $this->days = $days;
        $this->costs = $costs;
        $this->number_childrens = $number_childrens;
        $this->kindgar = $kindgar;
        $this->ages = $ages;
    }

    public function array(): array
    {
        $data = [];
        $row_number = 1;
        
        foreach($this->days as $day) {
            // Bolalar sonini hisoblash
            $children_9_10 = 0;
            $children_4 = 0;
            
            foreach($this->number_childrens[$day->id] as $child) {
                if($child->king_age_name_id == 3) { // 9-10.5 soatlik guruh
                    $children_9_10 += $child->kingar_children_number;
                } elseif($child->king_age_name_id == 4) { // 4 soatlik guruh
                    $children_4 += $child->kingar_children_number;
                }
            }
            
            $children_all = $children_9_10 + $children_4;
            
            // Narxlarni olish
            $cost_9_10 = 17866.00; // 9-10.5 soatlik guruh uchun narx
            $cost_4 = 4355.00; // 4 soatlik guruh uchun narx
            
            // Yetkazib berish xarajatlari
            $delivery_9_10 = $children_9_10 * $cost_9_10;
            $delivery_4 = $children_4 * $cost_4;
            $delivery_all = $delivery_9_10 + $delivery_4;
            
            // Xarajatlar tahlili
            $amount_without_nds = $delivery_all / 1.12; // QQSsiz summa
            $markup = $amount_without_nds * 0.285; // 28.5% ustama
            $nds = $amount_without_nds * 0.12; // 12% QQS
            $final_amount = $amount_without_nds + $markup + $nds;
            
            $data[] = [
                $row_number++,
                ($row_number-1) . '-T',
                $day->day_number . '/' . $day->month_name . '/' . $day->year_name,
                $children_9_10,
                $children_4,
                $children_all,
                $cost_9_10,
                $cost_4,
                $delivery_9_10,
                $delivery_4,
                $delivery_all,
                $amount_without_nds,
                $markup,
                $nds,
                $final_amount
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            '',
            '???? ????',
            '????',
            '9-10,5 ??????? ????? (???? ????)',
            '4 ??????? ????? (???? ????)',
            '???? (???? ????)',
            '9-10,5 ??????? ????? (????)',
            '4 ??????? ????? (????)',
            '9-10,5 ??????? ????? (??????? ?????)',
            '4 ??????? ????? (??????? ?????)',
            '???? (??????? ?????)',
            '????? (??????)',
            '?????? ??? 28,5%',
            '??? (???) 12%',
            '???? ??????? ????? ??????? (??? ?????)'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 10,
            'C' => 15,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 25,
            'J' => 25,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0F0F0']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Sarlavha qatorini markazga tekislash
                $sheet->getStyle('A1:O1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Barcha qatorlarga chegaralar qo'shish
                $sheet->getStyle('A1:O' . (count($this->days) + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // Jami qatorini qo'shish
                $lastRow = count($this->days) + 2;
                $sheet->setCellValue('A' . $lastRow, '????');
                $sheet->mergeCells('A' . $lastRow . ':C' . $lastRow);
                
                // Jami qatorini qalin qilish
                $sheet->getStyle('A' . $lastRow . ':O' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow . ':O' . $lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D0D0D0');
            },
        ];
    }
}
