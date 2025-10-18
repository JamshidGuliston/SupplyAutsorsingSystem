<?php

namespace App\Exports;

use App\Models\order_product;
use App\Models\order_product_structure;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrderTitleExport implements WithMultipleSheets
{
    protected $orderTitle;
    protected $allOrders;
    protected $allProductStructures;
    
    public function __construct($orderTitle)
    {
        $this->orderTitle = $orderTitle;
        $this->loadAllData();
    }
    
    protected function loadAllData()
    {
        // Barcha orderlarni bir marta olish
        $this->allOrders = order_product::where('order_title', $this->orderTitle)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('regions', 'regions.id', '=', 'kindgardens.region_id')
            ->orderBy('regions.id')
            ->orderBy('kindgardens.number_of_org')
            ->get(['order_products.id', 'regions.id as region_id', 'regions.region_name', 'regions.short_name', 'kindgardens.kingar_name', 'kindgardens.number_of_org', 'kindgardens.id as kingar_name_id']);
        
        // Barcha maxsulotlarni bir marta olish
        $orderIds = $this->allOrders->pluck('id')->toArray();
        $this->allProductStructures = order_product_structure::whereIn('order_product_name_id', $orderIds)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->get(['order_product_structures.id', 'order_product_structures.order_product_name_id', 'products.size_name_id', 'order_product_structures.product_name_id', 'order_product_structures.product_weight', 'products.product_name', 'sizes.size_name', 'products.div', 'order_product_structures.actual_weight', 'products.sort']);
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        // Tumanlarni guruplash
        $regions = [];
        foreach($this->allOrders as $order) {
            if(!isset($regions[$order->region_id])) {
                $regions[$order->region_id] = $order->region_name;
            }
        }
        
        // Tumanlarni ID bo'yicha saralash
        ksort($regions);
        
        // Har bir tuman uchun alohida sheet yaratish
        foreach($regions as $regionId => $regionName) {
            $sheets[] = new OrderTitleRegionExport($this->orderTitle, $regionId, $regionName, $this->allOrders, $this->allProductStructures);
        }
        
        return $sheets;
    }
}

// Har bir tuman uchun sheet class
class OrderTitleRegionExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithEvents
{
    protected $orderTitle;
    protected $regionId;
    protected $regionName;
    protected $allProducts = [];
    protected $kindergartens = [];
    protected $productData = [];
    protected $allOrders;
    protected $allProductStructures;
    
    public function __construct($orderTitle, $regionId, $regionName, $allOrders, $allProductStructures)
    {
        $this->orderTitle = $orderTitle;
        $this->regionId = $regionId;
        $this->regionName = $regionName;
        $this->allOrders = $allOrders;
        $this->allProductStructures = $allProductStructures;
        $this->prepareData();
    }
    
    protected function prepareData()
    {
        // Faqat joriy tuman uchun orderlarni filter qilish
        $orders = $this->allOrders->where('region_id', $this->regionId);
        
        // Orderlarni kindergarten ID bo'yicha index qilish
        $ordersByKindergarten = [];
        foreach($orders as $order) {
            $ordersByKindergarten[$order->kingar_name_id] = $order;
            $this->kindergartens[$order->kingar_name_id] = [
                'id' => $order->kingar_name_id,
                'name' => $order->kingar_name,
                'number_of_org' => $order->number_of_org,
                'region_id' => $order->region_id
            ];
        }
        
        // Bog'chalarni raqam bo'yicha saralash
        usort($this->kindergartens, function($a, $b) {
            return $a['number_of_org'] - $b['number_of_org'];
        });
        
        // Joriy tuman bog'chalari uchun maxsulotlarni olish
        $orderIds = $orders->pluck('id')->toArray();
        $structures = $this->allProductStructures->whereIn('order_product_name_id', $orderIds);
        
        // Maxsulotlarni order ID va product ID bo'yicha index qilish
        $structuresByOrderAndProduct = [];
        foreach($structures as $structure) {
            $structuresByOrderAndProduct[$structure->order_product_name_id][$structure->product_name_id] = $structure;
            
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
        
        // Maxsulotlarni sort bo'yicha saralash
        usort($this->allProducts, function($a, $b) {
            return $a['sort'] - $b['sort'];
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
                $weight = 0;
                
                if(isset($ordersByKindergarten[$kindergarten['id']])) {
                    $orderId = $ordersByKindergarten[$kindergarten['id']]->id;
                    
                    if(isset($structuresByOrderAndProduct[$orderId][$product['id']])) {
                        $weight = $structuresByOrderAndProduct[$orderId][$product['id']]->product_weight;
                    }
                }
                
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
    
    public function title(): string
    {
        // Sheet nomini tuman nomi bilan
        return mb_substr($this->regionName, 0, 31); // Excel sheet nomi 31 belgidan oshmasligi kerak
    }
    
    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            },
        ];
    }
    
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $lastRow = count($this->productData) + 1;
        // 3 = A,B,C | kindergartens + 1 = Jami ustuni
        $lastColumnIndex = 3 + count($this->kindergartens) + 1;
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex);
        
        // Header style
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);
        
        // Data style
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Jami ustuni
        $sheet->getStyle($lastColumn . '2:' . $lastColumn . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C8E6C9']
            ]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(10);
        
        // Bog'cha ustunlari (D dan boshlab oxirigacha)
        for($i = 4; $i < $lastColumnIndex; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(8);
        }
        
        return [];
    }
}

