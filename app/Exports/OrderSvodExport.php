<?php

namespace App\Exports;

use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\Day;
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
                number_format($item['product_weight'], 2, '.', ''),
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
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->items) + 1;
        
        // Header style
        $sheet->getStyle('A1:D1')->applyFromArray([
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
        $sheet->getStyle('A2:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        
        return [];
    }
    
    public function title(): string
    {
        return 'Свод отчёт';
    }
}

