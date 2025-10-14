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
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Year;
use App\Models\Day;
use App\Models\minus_multi_storage;

class MinusmultistorageExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $kid;
    protected $monthid;
    protected $data;
    protected $kingar;
    protected $days;
    protected $month;
    protected $year;

    public function __construct($kid, $monthid)
    {
        $this->kid = $kid;
        $this->monthid = $monthid;
        $this->loadData();
    }

    protected function loadData()
    {
        $this->kingar = Kindgarden::where('id', $this->kid)->first();
        $this->year = Year::where('year_active', 1)->first();
        
        if($this->monthid == 0){
            $this->monthid = Month::where('month_active', 1)->first()->id;
        }
        
        $this->month = Month::where('id', $this->monthid)->first();
        $this->days = Day::where('year_id', $this->year->id)
            ->where('month_id', $this->month->id)
            ->get();
        
        // Sarflangan mahsulotlar (minus) - har bir kun uchun
        $minusproducts = [];
        foreach($this->days as $day){
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $this->kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'minus_multi_storages.product_name_id',
                    'minus_multi_storages.kingar_menu_id',
                    'minus_multi_storages.product_weight',
                    'products.product_name',
                ]);
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id][$day->id])){
                    $minusproducts[$row->product_name_id][$day->id."+"] = 0;
                    $minusproducts[$row->product_name_id][$day->id."-"] = 0;
                }
                if($row->kingar_menu_id == -1){
                    $minusproducts[$row->product_name_id][$day->id."-"] += $row->product_weight;
                }
                else{
                    $minusproducts[$row->product_name_id][$day->id."+"] += $row->product_weight;
                }
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        
        $this->data = [
            'minusproducts' => $minusproducts,
        ];
    }

    public function array(): array
    {
        $rows = [];
        
        // Sarlavha
        $rows[] = ['Ombor chiqim hisoboti'];
        $rows[] = ['Боғча: ' . $this->kingar->kingar_name];
        $rows[] = ['Ой: ' . $this->month->month_name . ' ' . $this->year->year_name];
        $rows[] = []; // Bo'sh qator
        
        // Jadval sarlavhalari - kunlar
        $headings1 = ['Махсулотлар'];
        foreach($this->days as $day){
            $headings1[] = $day->day_number;
        }
        $headings1[] = 'Жами:';
        $rows[] = $headings1;
        
        // Ma'lumotlar
        $minusproducts = $this->data['minusproducts'];
        
        foreach($minusproducts as $key => $row){
            // Faqat string kalit (productname) bo'lsa o'tkazib yuborish
            if(!is_numeric($key)) continue;
            
            // Mahsulot nomi bo'lmasa o'tkazib yuborish
            if(!isset($row['productname']) || empty($row['productname'])) continue;
            
            $total = 0;
            $rowData = [$row['productname']];
            
            foreach($this->days as $day){
                $plusValue = isset($row[$day->id."+"]) ? $row[$day->id."+"] : 0;
                $minusValue = isset($row[$day->id."-"]) ? $row[$day->id."-"] : 0;
                
                $dayTotal = $plusValue + $minusValue;
                $total += $dayTotal;
                
                if($dayTotal > 0){
                    // Agar ikkalasi ham bo'lsa
                    if($plusValue > 0 && $minusValue > 0){
                        $rowData[] = round($plusValue, 2) . "\n---\n" . round($minusValue, 2);
                    } elseif($plusValue > 0){
                        $rowData[] = round($plusValue, 2);
                    } else {
                        $rowData[] = round($minusValue, 2);
                    }
                } else {
                    $rowData[] = '';
                }
            }
            
            $rowData[] = round($total, 2);
            $rows[] = $rowData;
        }
        
        return $rows;
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 30]; // Mahsulotlar
        
        // Har bir kun uchun
        $column = 'B';
        foreach($this->days as $day){
            $widths[$column] = 12;
            $column++;
        }
        
        // Jami ustuni
        $widths[$column] = 15;
        
        return $widths;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Sarlavha
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        // Bog'cha va oy ma'lumotlari
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11]
        ]);
        
        // Jadval sarlavhalari
        $lastColumn = chr(65 + 1 + count($this->days)); // A + 1 (mahsulotlar) + kunlar soni
        $sheet->getStyle('A5:' . $lastColumn . '5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8E8E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Ma'lumotlar qatorlari
        $lastRow = count($this->data['minusproducts']) + 5;
        $sheet->getStyle('A6:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        
        // Mahsulotlar ustunini chapga tekislash
        $sheet->getStyle('A6:A' . $lastRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Sarlavhani birlashtiramiz
                $lastColumn = chr(65 + 1 + count($this->days));
                $event->sheet->mergeCells('A1:' . $lastColumn . '1');
                $event->sheet->mergeCells('A2:' . $lastColumn . '2');
                $event->sheet->mergeCells('A3:' . $lastColumn . '3');
                
                // Text wrapping
                $lastRow = count($this->data['minusproducts']) + 5;
                $event->sheet->getDelegate()->getStyle('A6:' . $lastColumn . $lastRow)
                    ->getAlignment()->setWrapText(true);
            }
        ];
    }
}

