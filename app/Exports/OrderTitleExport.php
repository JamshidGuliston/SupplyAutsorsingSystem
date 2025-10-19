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
            ->get(['order_product_structures.id', 'order_product_structures.order_product_name_id', 'products.size_name_id', 'order_product_structures.product_name_id', 'order_product_structures.product_weight', 'products.product_name', 'sizes.size_name', 'products.div', 'order_product_structures.actual_weight', 'products.sort', 'products.package_size']);
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
class OrderTitleRegionExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithEvents
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
                    'sort' => $structure->sort ?? 0,
                    'package_size' => $structure->package_size ?? 0
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
                'package_size' => $product['package_size'],
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
    
    public function array(): array
    {
        $data = [];
        
        // 1-qator: Tuman nomi va sana
        $firstRow = [];
        $firstRow[] = $this->regionName; // A ustuni - tuman nomi
        
        // Bo'sh kataklar
        $emptyCount = 2 + count($this->kindergartens); // B va C ustunlari + bog'chalar
        for($i = 0; $i < $emptyCount; $i++) {
            $firstRow[] = '';
        }
        
        // Oxirgi ustun - sana (Jami ustuni ustida)
        $firstRow[] = $this->orderTitle;
        
        $data[] = $firstRow;
        
        // 2-qator: Headers
        $headings = ['№', 'Махсулот номи', 'Ўлчов'];
        
        foreach($this->kindergartens as $kindergarten) {
            $headings[] = $kindergarten['number_of_org'];
        }
        
        $headings[] = 'Жами';
        $data[] = $headings;
        
        // Har bir bog'cha uchun jami yig'indisini hisoblash
        $counts = [];
        foreach($this->kindergartens as $kindergarten) {
            $counts[$kindergarten['id']] = 0;
        }
        
        // 3-qatordan: Ma'lumotlar
        $counter = 1;
        foreach($this->productData as $productId => $product) {
            $package_size = $product['package_size'] ?? 0;
            $summ = 0;
            
            $row = [
                $counter++,
                $product['name'],
                (($package_size != null && $package_size > 0)) ? 'Дона' : $product['unit'],
            ];
            
            // Har bir bog'cha uchun qiymat (PDF dagi kabi hisoblash)
            foreach($this->kindergartens as $kindergarten) {
                $weight = $product['kindergartens'][$kindergarten['id']] ?? 0;
                
                // Package_size bo'yicha hisoblash (PDF dagi kabi)
                $displayValue = 0;
                if($package_size != null && $package_size > 0) {
                    $displayValue = $weight / $package_size;
                } else {
                    $displayValue = $weight;
                }
                
                // Counts ga qo'shish (faqat unit_id != 3 bo'lgan mahsulotlar uchun)
                if($product['unit_id'] != 3) {
                    $counts[$kindergarten['id']] += $displayValue;
                }
                
                $summ += $displayValue;
                $row[] = $displayValue > 0 ? $displayValue : '';
            }
            
            // Jami
            $row[] = number_format($summ, 0, '.', '');
            
            $data[] = $row;
        }
        
        // Oxirgi qator: Jami yig'indisi
        $totalRow = ['', 'Жами', ''];
        foreach($this->kindergartens as $kindergarten) {
            $totalRow[] = number_format($counts[$kindergarten['id']], 1, '.', '');
        }
        $totalRow[] = '';
        $data[] = $totalRow;
        
        return $data;
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
                // 2-qator (header) balandligini sozlash
                $event->sheet->getDelegate()->getRowDimension('2')->setRowHeight(30);
            },
        ];
    }
    
    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $lastRow = count($this->productData) + 3; // +3 chunki 1-qator title, 2-qator header, oxirgi qator Jami
        // 3 = A,B,C | kindergartens + 1 = Jami ustuni
        $lastColumnIndex = 3 + count($this->kindergartens) + 1;
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumnIndex);
        
        // 1-qator: Tuman nomi va sana
        // Tuman nomi (A1)
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Sana (oxirgi ustun)
        $sheet->getStyle($lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // 2-qator: Header style
        $sheet->getStyle('A2:' . $lastColumn . '2')->applyFromArray([
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
        
        // Data style (3-qatordan oxirgi qatorgacha)
        $sheet->getStyle('A3:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Jami ustuni
        $sheet->getStyle($lastColumn . '3:' . $lastColumn . ($lastRow-1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C8E6C9']
            ]
        ]);
        
        // Oxirgi qator (Jami yig'indisi) - ko'k rangli
        $sheet->getStyle('A' . $lastRow . ':' . $lastColumn . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'd9edf7'] // PDF dagi kabi och ko'k
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
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
        
        // 1-qatorni balandroq qilish
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        return [];
    }
}

