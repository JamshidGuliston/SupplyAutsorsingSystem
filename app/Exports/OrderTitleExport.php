<?php

namespace App\Exports;

use App\Models\order_product;
use App\Models\order_product_structure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderTitleExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $orderTitle;
    protected $allProducts = [];
    protected $kindergartens = [];
    protected $productData = [];
    
    public function __construct($orderTitle)
    {
        $this->orderTitle = $orderTitle;
        $this->prepareData();
    }
    
    protected function prepareData()
    {
        $orders = order_product::where('order_title', $this->orderTitle)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('regions', 'regions.id', '=', 'kindgardens.region_id')
            ->get(['order_products.id', 'regions.id as region_id', 'regions.region_name', 'regions.short_name', 'kindgardens.kingar_name', 'kindgardens.number_of_org', 'kindgardens.id as kingar_name_id']);
        
        foreach($orders as $order) {
            $this->kindergartens[$order->kingar_name_id] = [
                'id' => $order->kingar_name_id,
                'name' => $order->kingar_name,
                'number_of_org' => $order->number_of_org,
                'region_id' => $order->region_id
            ];
            
            $orderProductStructures = order_product_structure::where('order_product_name_id', $order->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get(['order_product_structures.id', 'products.size_name_id', 'order_product_structures.product_name_id', 'order_product_structures.product_weight', 'products.product_name', 'sizes.size_name', 'products.div', 'order_product_structures.actual_weight', 'products.sort']);
                
            foreach($orderProductStructures as $structure) {
                $productId = $structure->product_name_id;
                
                if(!isset($this->allProducts[$productId])) {
                    $this->allProducts[$productId] = [
                        'id' => $productId,
                        'name' => $structure->product_name,
                        'unit' => $structure->size_name,
                        'unit_id' => $structure->size_name_id,
                        'sort' => $structure->sort ?? 0
                    ];
                }
            }
        }
        
        // Maxsulotlarni sort bo'yicha saralash
        usort($this->allProducts, function($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        
        // Bog'chalarni region bo'yicha saralash
        usort($this->kindergartens, function($a, $b) {
            if($a['number_of_org'] != $b['number_of_org']) {
                return $a['number_of_org'] - $b['number_of_org'];
            }
            return strcmp($a['number_of_org'], $b['number_of_org']);
        });
        
        // Har bir maxsulot uchun har bir bog'cha bo'yicha miqdorni olish
        foreach($this->allProducts as $product) {
            $this->productData[$product['id']] = [
                'name' => $product['name'],
                'unit' => $product['unit'],
                'unit_id' => $product['unit_id'],
                'kindergartens' => [],
                'total' => 0
            ];
            
            foreach($this->kindergartens as $kindergarten) {
                $order = $orders->where('kingar_name_id', $kindergarten['id'])->first();
                $structure = null;
                if($order) {
                    $structure = order_product_structure::where('order_product_name_id', $order->id)
                        ->where('product_name_id', $product['id'])
                        ->first();
                }
                
                $weight = $structure ? $structure->product_weight : 0;
                $this->productData[$product['id']]['kindergartens'][$kindergarten['id']] = $weight;
                $this->productData[$product['id']]['total'] += $weight;
            }
        }
    }
    
    public function collection()
    {
        $data = collect();
        $counter = 1;
        
        foreach($this->productData as $productId => $product) {
            $row = [
                $counter++,
                $product['name'],
                $product['unit'],
            ];
            
            // Har bir bog'cha uchun qiymat
            foreach($this->kindergartens as $kindergarten) {
                $weight = $product['kindergartens'][$kindergarten['id']] ?? 0;
                $row[] = $weight > 0 ? number_format($weight, 2, '.', '') : '';
            }
            
            // Jami
            $row[] = number_format($product['total'], 2, '.', '');
            
            $data->push($row);
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        $headings = ['№', 'Махсулот номи', 'Ўлчов'];
        
        foreach($this->kindergartens as $kindergarten) {
            $headings[] = $kindergarten['number_of_org'];
        }
        
        $headings[] = 'Жами';
        
        return $headings;
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->productData) + 1;
        $lastColumn = chr(67 + count($this->kindergartens)); // C + kindergartens count
        
        // Header style
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
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
        
        // Jami ustuni
        $sheet->getStyle($lastColumn . '2:' . $lastColumn . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C8E6C9']
            ]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(10);
        
        // Bog'cha ustunlari
        foreach(range('D', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setWidth(8);
        }
        
        return [];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            },
        ];
    }
    
    public function title(): string
    {
        return 'Буюртма';
    }
}

