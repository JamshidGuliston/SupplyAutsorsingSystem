<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\titlemenu_food;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SpendedkgExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $start, $end, $costid;
    
    public function __construct($id, $start, $end, $costid)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->with('age_range')->first();
        $nakproducts = [];
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('years', 'days.year_id', '=', 'years.id')
            ->join('months', 'days.month_id', '=', 'months.id')
            ->get(['days.id', 'days.day_number', 'days.month_id', 'years.year_name', 'months.month_name']);
        
        foreach($days as $day){
            foreach($kindgar->age_range as $age){
                $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('kingar_name_id', $this->id)
                        ->where('king_age_name_id', $age->id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                
                $productscount = [];
                foreach($join as $row){
                    if(!isset($productscount[$row->product_name_id][$age->id])){
                        $productscount[$row->product_name_id][$age->id] = 0;
                    }
                    $productscount[$row->product_name_id][$age->id] += $row->weight;
                    $productscount[$row->product_name_id][$age->id.'-children'] = $row->kingar_children_number;
                    $productscount[$row->product_name_id][$age->id.'div'] = $row->div;
                    $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                    $productscount[$row->product_name_id][$age->id.'sort'] = $row->sort;
                    $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                }
                
                if($age->id != 3){
                    $foods = titlemenu_food::where('day_id', $day->id-1)->where('worker_age_id', $age->id)->get();
                }else{
                    $foods = [];
                }
                
                foreach($foods as $food){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                            ->where('kingar_name_id', $this->id)
                            ->where('king_age_name_id', $food->worker_age_id)
                            ->leftjoin('active_menus', function($join){
                                $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            })
                            ->where('active_menus.day_id', $day->id)
                            ->where('active_menus.age_range_id', $food->worker_age_id)
                            ->where('active_menus.menu_food_id', $food->food_id)
                            ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                            ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                            ->get();
                    foreach($join as $row){
                        $productscount[$row->product_name_id][$age->id."-worker"] = $row->weight * $row->workers_count;
                    }
                }

                foreach($productscount as $key => $row){
                    if(isset($row['product_name'])){
                        $childs = Number_children::where('day_id', $day->id)
                            ->where('kingar_name_id', $this->id)
                            ->where('king_age_name_id', $age->id)
                            ->sum('kingar_children_number');    
                        $nakproducts[0][$day->id] = $childs;
                        $nakproducts[0]['product_name'] = "Болалар сони";
                        $nakproducts[0]['size_name'] = "";
                        
                        if(!isset($nakproducts[$key][$day->id])){
                            $nakproducts[$key][$day->id] = 0;
                        }
                        $nakproducts[$key][$day->id] = ($row[$age->id]*$row[$age->id.'-children']) / $row[$age->id.'div'] + (isset($row[$age->id."-worker"]) ? $row[$age->id."-worker"] / $row[$age->id.'div'] : 0);
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['sort'] = $row[$age->id.'sort'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                    }
                }
            }
        }
        
        // Sort by sort field, but "Болалар сони" should be first
        usort($nakproducts, function ($a, $b){
            // "Болалар сони" har doim birinchi bo'lishi kerak
            if(isset($a['product_name']) && $a['product_name'] == "Болалар сони"){
                return -1;
            }
            if(isset($b['product_name']) && $b['product_name'] == "Болалар сони"){
                return 1;
            }
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
            return 0;
        });

        $data = [];
        
        // Header
        $first_day = $days->first();
        $last_day = $days->last();
        $month = Month::where('id', $first_day->month_id)->first();
        
        $header_text = $kindgar->kingar_name . ' да ' . 
                      sprintf('%04d', $first_day->year_name) . ' йил ' . 
                      $first_day->day_number . '-' . $last_day->day_number . ' ' . 
                      ($month ? $month->month_name : '') . ' кунларида сарфланган озиқ-овқат маҳсулотлар тўғрисида маълумот';
        
        $data[] = [$header_text];
        $data[] = [''];
        
        // Jadval header
        $header = ['Махсулотлар', 'Сана'];
        foreach($days as $day) {
            $header[] = $day->day_number;
        }
        $header[] = 'Жами';
        $data[] = $header;
        
        // Ma'lumotlar
        foreach($nakproducts as $key => $product) {
            if(isset($product['product_name'])) {
                // Mahsulot nomini faqat birinchi 3 so'zini olish (blade template'dagi kabi)
                // Lekin "Болалар сони" uchun to'liq nom
                if($product['product_name'] == "Болалар сони"){
                    $product_name_short = $product['product_name'];
                } else {
                    $product_name_words = explode(' ', $product['product_name']);
                    $product_name_short = implode(' ', array_slice($product_name_words, 0, 3));
                }
                
                $row = [$product_name_short];
                
                // O'lchov birligi
                if($product['product_name'] != "Болалар сони"){
                    $row[] = $product['size_name'] ?? '';
                } else {
                    $row[] = '';
                }
                
                $total = 0;
                foreach($days as $day) {
                    $value = $product[$day->id] ?? 0;
                    if($product['product_name'] == "Болалар сони"){
                        $row[] = $value;
                    } else {
                        $row[] = number_format($value, 3, '.', '');
                    }
                    $total += $value;
                }
                
                if($product['product_name'] == "Болалар сони"){
                    $row[] = $total;
                } else {
                    $row[] = number_format($total, 3, '.', '');
                }
                
                $data[] = $row;
            }
        }
        
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Header merge va style
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(12)->setBold(true);
                
                // Jadval header style (3-qator)
                $headerRow = 3;
                $sheet->getStyle('A' . $headerRow . ':' . $highestColumn . $headerRow)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Ma'lumotlar qismi border
                $dataStartRow = $headerRow + 1;
                $dataRange = 'A' . $dataStartRow . ':' . $highestColumn . $highestRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Mahsulot nomlari uchun left alignment
                $sheet->getStyle('A' . $dataStartRow . ':A' . $highestRow)
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Barcha ustunlar uchun center alignment (mahsulot nomidan tashqari)
                $sheet->getStyle('B' . $dataStartRow . ':' . $highestColumn . $highestRow)
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // "Болалар сони" qatorini bold qilish
                for($row = $dataStartRow; $row <= $highestRow; $row++){
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    if($cellValue == "Болалар сони" || strpos($cellValue, "Болалар сони") !== false){
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->getFont()->setBold(true);
                        // "Болалар сони" uchun integer format
                        $sheet->getStyle('C' . $row . ':' . $highestColumn . $row)
                              ->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
                
                // Number format - faqat raqamli ustunlar uchun (bold bo'lmagan qatorlar uchun)
                $sheet->getStyle('C' . $dataStartRow . ':' . $highestColumn . $highestRow)
                      ->getNumberFormat()->setFormatCode('#,##0.000');
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Махсулотлар
            'B' => 12,  // Сана (o'lchov birligi)
        ];
    }
}

