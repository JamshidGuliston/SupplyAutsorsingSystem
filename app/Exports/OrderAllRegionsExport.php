<?php

namespace App\Exports;

use App\Models\order_product;
use App\Models\order_product_structure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OrderAllRegionsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $orderTitle;
    protected $items = [];
    protected $regions = [];
    protected $regionColumns = [];
    
    public function __construct($orderTitle)
    {
        $this->orderTitle = $orderTitle;
        $this->prepareData();
    }
    
    protected function prepareData()
    {
        $document = order_product::where('order_products.order_title', $this->orderTitle)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('regions', 'regions.id', '=', 'kindgardens.region_id')
            ->get(['order_products.id', 'order_products.day_id', 'regions.id as region_id', 'regions.region_name', 'regions.short_name']);
            
        foreach($document as $row){
            $this->regions[$row->region_id] = $row->short_name;
            
            $item = order_product_structure::where('order_product_name_id', $row->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get();
                
            foreach($item as $in){
                if(!isset($this->items[$in->product_name_id])){
                    $this->items[$in->product_name_id] = [
                        'product_name' => $in->product_name,
                        'size_name' => $in->size_name,
                        'p_sort' => $in->sort,
                        'qoldiq' => 0,
                        'farq' => 0
                    ];
                }
                
                if(!isset($this->items[$in->product_name_id][$row->region_id])){
                    $this->items[$in->product_name_id][$row->region_id] = 0;
                }
                
                $this->items[$in->product_name_id][$row->region_id] += $in->product_weight;
            }  
        }
        
        // Qoldiqlarni hisoblash
        if($document->count() > 0){
            $day = \App\Models\Day::find($document->first()->day_id);
            if($day){
                $month_days = \App\Models\Day::where('month_id', $day->month_id)
                    ->where('year_id', $day->year_id)
                    ->orderBy('id')
                    ->get(['id']);
                
                $remainders = [];
                $addlarch = \App\Models\Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                            ->where('add_groups.day_id', '<=', $month_days->last()->id)
                            ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                            ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                            ->get();
                
                foreach($addlarch as $row){
                    if(!isset($remainders[$row->product_id])){
                        $remainders[$row->product_id]['kirim'] = 0;
                        $remainders[$row->product_id]['chiqim'] = 0;
                    }
                    $remainders[$row->product_id]['kirim'] += $row->weight;
                }
                
                // Chiqimlarni olish
                $chiqimlar = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                            ->where('order_products.day_id', '<=', $month_days->last()->id)
                            ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                            ->where('order_products.document_processes_id', 4)
                            ->select('order_product_structures.product_name_id', 'order_product_structures.product_weight')
                            ->get();
                
                foreach($chiqimlar as $row){
                    if(!isset($remainders[$row->product_name_id])){
                        $remainders[$row->product_name_id]['kirim'] = 0;
                        $remainders[$row->product_name_id]['chiqim'] = 0;
                    }
                    $remainders[$row->product_name_id]['chiqim'] += $row->product_weight;
                }
                
                // Har bir mahsulot uchun qoldiq va farqni hisoblash
                foreach($this->items as $product_id => &$item){
                    $kirim = isset($remainders[$product_id]) ? $remainders[$product_id]['kirim'] : 0;
                    $chiqim = isset($remainders[$product_id]) ? $remainders[$product_id]['chiqim'] : 0;
                    $item['qoldiq'] = $kirim - $chiqim;
                    
                    // Jami miqdorni hisoblash
                    $total_weight = 0;
                    foreach($this->regions as $region_id => $region){
                        if(isset($item[$region_id])){
                            $total_weight += $item[$region_id];
                        }
                    }
                    $item['total_weight'] = $total_weight;
                    $item['farq'] = $total_weight - $item['qoldiq'];
                }
                unset($item);
            }
        }
        
        // Saralash
        usort($this->items, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
    }
    
    public function collection()
    {
        $data = collect();
        $counter = 1;
        
        foreach($this->items as $item) {
            $row = [
                $counter++,
                $item['product_name'],
                $item['size_name'],
            ];
            
            // Har bir region uchun qiymat qo'shish
            $total = 0;
            foreach($this->regions as $regionId => $regionName) {
                $weight = isset($item[$regionId]) ? $item[$regionId] : 0;
                $row[] = $weight > 0 ? number_format($weight, 2, '.', '') : '';
                $total += $weight;
            }
            
            // Jami, Qoldiq, Kerak bo'ladi ustunlari
            $row[] = number_format($item['total_weight'] ?? $total, 2, '.', '');
            $row[] = number_format($item['qoldiq'] ?? 0, 2, '.', '');
            $row[] = number_format($item['farq'] ?? 0, 2, '.', '');
            
            $data->push($row);
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        $headings = ['№', 'Махсулот номи', 'Ўлчов бирлиги'];
        
        foreach($this->regions as $regionName) {
            $headings[] = $regionName;
        }
        
        $headings[] = 'Миқдори';
        $headings[] = 'Қолдиқ';
        $headings[] = 'Керак бўлади';
        
        return $headings;
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->items) + 1;
        // 3 = A,B,C | regions + 3 yangi ustun (Miqdori, Qoldiq, Kerak bo'ladi)
        $lastColumnIndex = 3 + count($this->regions) + 3;
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);
        $miqdoriColumnIndex = 3 + count($this->regions) + 1;
        $qoldiqColumnIndex = 3 + count($this->regions) + 2;
        $farqColumnIndex = 3 + count($this->regions) + 3;
        $miqdoriColumn = Coordinate::stringFromColumnIndex($miqdoriColumnIndex);
        $qoldiqColumn = Coordinate::stringFromColumnIndex($qoldiqColumnIndex);
        $farqColumn = Coordinate::stringFromColumnIndex($farqColumnIndex);
        
        // Header style
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Data style
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Миқдори ustuni (Jami) - sariq rang
        $sheet->getStyle($miqdoriColumn . '2:' . $miqdoriColumn . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF9C4']
            ]
        ]);
        
        // Қолдиқ ustuni - yashil rang
        $sheet->getStyle($qoldiqColumn . '2:' . $qoldiqColumn . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C8E6C9']
            ]
        ]);
        
        // Керak бўлади ustuni - qizil rang (agar musbat bo'lsa)
        $sheet->getStyle($farqColumn . '2:' . $farqColumn . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFCDD2']
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => 'C62828']]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(10);
        
        // Region ustunlari (D dan boshlab)
        for($i = 4; $i <= 3 + count($this->regions); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(10);
        }
        
        // Oxirgi 3 ta ustun (Miqdori, Qoldiq, Kerak bo'ladi)
        $sheet->getColumnDimension($miqdoriColumn)->setWidth(12);
        $sheet->getColumnDimension($qoldiqColumn)->setWidth(12);
        $sheet->getColumnDimension($farqColumn)->setWidth(12);
        
        return [];
    }
    
    public function title(): string
    {
        return 'Барча ҳудудлар';
    }
}

