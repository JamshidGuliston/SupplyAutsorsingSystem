<?php

namespace App\Exports;

use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Year;
use App\Models\Day;
use App\Models\plus_multi_storage;
use App\Models\minus_multi_storage;
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

class PlusmultistorageExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $kid;
    protected $monthid;
    protected $data;
    protected $days;
    protected $kingar;
    protected $month;
    protected $year;

    public function __construct($kid, $monthid)
    {
        $this->kid = $kid;
        $this->monthid = $monthid;
        $this->loadData();
    }

    private function loadData()
    {
        $this->kingar = Kindgarden::where('id', $this->kid)->first();
        $this->year = Year::where('year_active', 1)->first();
        
        if($this->monthid == 0){
            $this->monthid = Month::where('month_active', 1)->first()->id;
        }
        
        $this->month = Month::where('id', $this->monthid)->first();
        $this->days = Day::where('year_id', $this->year->id)->where('month_id', $this->month->id)->get();
        
        // Sarflangan mahsulotlar (minus)
        $minusproducts = [];
        foreach($this->days as $day){
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $this->kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'minus_multi_storages.product_name_id',
                    'minus_multi_storages.product_weight',
                    'products.product_name',
                    'products.div',
                ]);
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id][$day->id])){
                    $minusproducts[$row->product_name_id][$day->id] = 0;
                }
                $minusproducts[$row->product_name_id][$day->id] += $row->product_weight;
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        
        // O'tgan oydan qoldiq
        $residualProducts = [];
        $residualData = plus_multi_storage::where('kingarden_name_d', $this->kid)
            ->where('residual', 1)
            ->where('day_id', '>=', $this->days->first()->id)
            ->where('day_id', '<=', $this->days->last()->id)
            ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
            ->get([
                'plus_multi_storages.product_name_id',
                'plus_multi_storages.product_weight',
                'products.product_name',
            ]);
        
        foreach($residualData as $row){
            if(!isset($residualProducts[$row->product_name_id])){
                $residualProducts[$row->product_name_id] = [
                    'weight' => 0,
                    'productname' => $row->product_name
                ];
            }
            $residualProducts[$row->product_name_id]['weight'] += $row->product_weight;
        }
        
        // Kiritilgan mahsulotlar (plus)
        $plusproducts = [];
        foreach($this->days as $day){
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $this->kid)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'plus_multi_storages.product_name_id',
                    'plus_multi_storages.shop_id',
                    'plus_multi_storages.product_weight',
                    'plus_multi_storages.residual',
                    'products.product_name',
                ]);
            foreach($plus as $row){
                if($row->residual == 1){
                    continue;
                }
                
                if(!isset($plusproducts[$row->product_name_id][$day->id])){
                    $plusproducts[$row->product_name_id][$day->id."+"] = 0;
                }
                if($row->shop_id != -1){
                    $plusproducts[$row->product_name_id][$day->id."+"] += $row->product_weight;
                }
                $plusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        
        // Faqat qoldiq bo'lgan mahsulotlarni qo'shish
        foreach($residualProducts as $productId => $residualData){
            if(!isset($plusproducts[$productId])){
                $plusproducts[$productId] = ['productname' => $residualData['productname']];
            }
        }
        
        // Faqat minus bo'lgan mahsulotlarni qo'shish
        foreach($minusproducts as $productId => $minusData){
            if(is_numeric($productId) && !isset($plusproducts[$productId])){
                $plusproducts[$productId] = ['productname' => $minusData['productname']];
            }
        }
        
        $this->data = [
            'plusproducts' => $plusproducts,
            'minusproducts' => $minusproducts,
            'residualProducts' => $residualProducts,
        ];
    }

    public function array(): array
    {
        $rows = [];
        
        // Sarlavha
        $rows[] = ['Ombor kirim-chiqim hisoboti'];
        $rows[] = ['Боғча: ' . $this->kingar->kingar_name];
        $rows[] = ['Ой: ' . $this->month->month_name . ' ' . $this->year->year_name];
        $rows[] = []; // Bo'sh qator
        
        // Jadval sarlavhalari - 1-qator (kunlar)
        $headings1 = ['Махсулотлар', "O'tgan oydan Qoldiq"];
        foreach($this->days as $day){
            $headings1[] = $day->day_number;
            $headings1[] = '';
        }
        $headings1[] = 'Jami kiritilgan';
        $headings1[] = 'Jami sarflangan';
        $headings1[] = 'Farqi';
        $rows[] = $headings1;
        
        // Jadval sarlavhalari - 2-qator (- va +)
        $headings2 = ['', ''];
        foreach($this->days as $day){
            $headings2[] = 'Сарф';
            $headings2[] = 'Кирим';
        }
        $headings2[] = '';
        $headings2[] = '';
        $headings2[] = '';
        $rows[] = $headings2;
        
        // Ma'lumotlar
        $plusproducts = $this->data['plusproducts'];
        $minusproducts = $this->data['minusproducts'];
        $residualProducts = $this->data['residualProducts'];
        
        foreach($plusproducts as $key => $row){
            // Faqat string kalit (productname) bo'lsa o'tkazib yuborish
            if(!is_numeric($key)) continue;
            
            // Mahsulot nomi bo'lmasa o'tkazib yuborish
            if(!isset($row['productname']) || empty($row['productname'])) continue;
            
            $totalMinus = 0;
            $totalPlus = 0;
            $residualWeight = isset($residualProducts[$key]) ? $residualProducts[$key]['weight'] : 0;
            $totalPlus += $residualWeight;
            
            $rowData = [
                $row['productname'],
                $residualWeight > 0 ? $residualWeight : '',
            ];
            
            foreach($this->days as $day){
                $minusValue = isset($minusproducts[$key][$day->id]) ? $minusproducts[$key][$day->id] : 0;
                $plusValue = isset($row[$day->id."+"]) ? $row[$day->id."+"] : 0;
                $totalMinus += $minusValue;
                $totalPlus += $plusValue;
                
                $rowData[] = $minusValue > 0 ? round($minusValue, 2) : '';
                $rowData[] = $plusValue > 0 ? round($plusValue, 2) : '';
            }
            
            $rowData[] = round($totalPlus, 2);
            $rowData[] = round($totalMinus, 2);
            $rowData[] = round($totalPlus - $totalMinus, 2);
            
            $rows[] = $rowData;
        }
        
        return $rows;
    }

    public function headings(): array
    {
        // Headings array() da kiritiladi
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']]],
            6 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge cells for title
                $lastColumn = $sheet->getHighestColumn();
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->mergeCells('A3:' . $lastColumn . '3');
                
                // Merge cells for day headers (5-qator)
                $col = 'C';
                foreach($this->days as $day){
                    $nextCol = chr(ord($col) + 1);
                    $sheet->mergeCells($col . '5:' . $nextCol . '5');
                    $col = chr(ord($nextCol) + 1);
                }
                
                // Merge last 3 columns
                $lastRow = $sheet->getHighestRow();
                $lastColNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);
                $jami1Col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum - 2);
                $jami2Col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum - 1);
                $jami3Col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColNum);
                
                // Merge vertical cells (A va B ustunlari)
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells($jami1Col . '5:' . $jami1Col . '6');
                $sheet->mergeCells($jami2Col . '5:' . $jami2Col . '6');
                $sheet->mergeCells($jami3Col . '5:' . $jami3Col . '6');
                
                // Title styling
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);
                
                // Header styling
                $sheet->getStyle('A5:' . $lastColumn . '6')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Borders
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];
                
                $sheet->getStyle('A5:' . $lastColumn . $lastRow)->applyFromArray($styleArray);
                
                // A ustunini chapdan hizalash
                $sheet->getStyle('A7:A' . $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
}

