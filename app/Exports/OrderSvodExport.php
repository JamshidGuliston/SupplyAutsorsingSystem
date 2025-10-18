<?php

namespace App\Exports;

use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\Day;
use App\Models\Add_large_werehouse;
use App\Models\Add_group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class OrderSvodExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $orderTitle;
    protected $items = [];
    
    public function __construct($orderTitle)
    {
        $this->orderTitle = $orderTitle;
        $this->prepareData();
    }
    
    protected function prepareData()
    {
        $document = order_product::where('order_products.order_title', $this->orderTitle)->get();
        
        if($document->isEmpty()) {
            return;
        }
        
        foreach($document as $row){
            $item = order_product_structure::where('order_product_name_id', $row->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get();
                
            foreach($item as $in){
                if(!isset($this->items[$in->product_name_id])){
                    $this->items[$in->product_name_id] = [
                        'product_weight' => 0,
                        'product_name' => $in->product_name,
                        'size_name' => $in->size_name,
                        'p_sort' => $in->sort
                    ];
                }
                $this->items[$in->product_name_id]['product_weight'] += $in->product_weight;
            }  
        }
        
        // Oyning kunlarini olish
        $firstOrder = $document->first();
        $month_days = Day::where('month_id', Day::where('id', $firstOrder->day_id)->first()->month_id)
            ->where('year_id', Day::where('id', $firstOrder->day_id)->first()->year_id)
            ->orderBy('id')
            ->get();
        
        // Qoldiqlarni hisoblash
        $remainders = [];
        
        // Kirim bo'lgan maxsulotlar
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
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
        
        // Chiqimlarni olish (document_processes_id = 4)
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
            $item['farq'] = $item['product_weight'] - $item['qoldiq'];
        }
        unset($item);
        
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
            $data->push([
                $counter++,
                $item['product_name'],
                $item['size_name'],
                number_format($item['product_weight'], 1, '.', ''),
                number_format($item['qoldiq'] ?? 0, 1, '.', ''),
                number_format($item['farq'] ?? 0, 1, '.', ''),
            ]);
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return [
            '№',
            'Махсулот номи',
            'Ўлчов бирлиги',
            'Миқдори',
            'Қолдиқ',
            'Керак бўлади',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->items) + 1;
        
        // Header style
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
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
        $sheet->getStyle('A2:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Qoldiq ustuni - yashil rang
        $sheet->getStyle('E2:E' . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C8E6C9']
            ]
        ]);
        
        // Kerak bo'ladi ustuni - qizil rang (agar musbat bo'lsa)
        $sheet->getStyle('F2:F' . $lastRow)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFCDD2']
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => 'C62828']]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        
        return [];
    }
    
    public function title(): string
    {
        return 'Свод отчёт';
    }
}

