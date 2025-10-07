<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Active_menu;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
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

class NormExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $ageid, $start, $end, $costid;
    
    public function __construct($id, $ageid, $start, $end, $costid)
    {
        $this->id = $id;
        $this->ageid = $ageid;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $this->ageid)->first();
        $days = Day::where('id', '>=', $this->start)->where('id', '<=', $this->end)->get();
        $date = Day::where('days.id', $this->start)->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->first(['days.day_number', 'months.month_name', 'years.year_name']);
        
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $this->id)
                    ->where('king_age_name_id', $this->ageid)
                    ->leftjoin('active_menus', function($join){
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('norm_categories', 'products.norm_cat_id', '=', 'norm_categories.id')
                    ->join('norms', 'products.norm_cat_id', '=', 'norms.norm_cat_id')
                    ->where('norms.norm_age_id', $this->ageid)
                    ->where('norms.noyuk_id', 1)
                    ->get();
            
            $productscount = [];
            foreach($join as $row){
                if(!isset($productscount[$row->norm_cat_id][$this->ageid])){
                    $productscount[$row->norm_cat_id][$this->ageid] = 0;
                }
                $productscount[$row->norm_cat_id][$this->ageid] += $row->weight;
                $productscount[$row->norm_cat_id][$this->ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->norm_cat_id][$this->ageid.'div'] = $row->div;
                $productscount[$row->norm_cat_id]['product_name'] = $row->norm_name_short;
                $productscount[$row->norm_cat_id][$this->ageid.'sort'] = $row->sort;
                $productscount[$row->norm_cat_id]['norm_weight'] = $row->norm_weight;
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    if(!isset($nakproducts[$key]['children'])){
                        $nakproducts[$key]['children'] = 0;
                    }
                    $nakproducts[$key][$day->id] = ($row[$this->ageid]*$row[$this->ageid.'-children']) / $row[$this->ageid.'div'];
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['norm_weight'] = $row['norm_weight'];
                    $nakproducts[$key]['children'] += $row[$this->ageid.'-children'];
                    $nakproducts[$key]['sort'] = $row[$this->ageid.'sort'];
                    $nakproducts[$key]['div'] = $row[$this->ageid.'div'];
                }
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $numberOfChild = Number_children::where('kingar_name_id', $this->id)
            ->where('king_age_name_id', $this->ageid)
            ->where('day_id', '>=', $this->start)
            ->where('day_id', '<=', $this->end)->sum('kingar_children_number');

        $data = [];
        
        // Header - PDF bilan bir xil
        $data[] = [$date->year_name . ' йил ' . $date->month_name . ' ойида мактабгача таълим муассасаларида тарбияланувчиларнинг озиқ-овқат маҳсулотлари билан таъминланиши ҳақида маълумот'];
        $data[] = [''];
        $data[] = [$kindgar->kingar_name . ' / ' . $age->age_name];
        $data[] = ['Хисобот давридаги бола катнови: ' . $numberOfChild . ' нафар'];
        $data[] = [''];
        
        // Jadval header - PDF bilan bir xil
        $header = [
            'Махсулот номи',
            '1 бола учун уртача кунлик меъёр (гр хисобида)',
            'меъёр бўйича сарфланиши лозим булган махсулот микдори (кг хисобида)',
            'Хақиқий харажат (кг хисобида)',
            'Меъёрга нисбатан фарқи Кам(-) Ортиқча(+)',
            'таъминланиш даражаси %'
        ];
        $data[] = $header;
        
        // Jami hisoblash uchun o'zgaruvchilar
        $total_norm_required = 0;
        $total_actual_consumption = 0;
        $total_difference = 0;
        
        // Ma'lumotlar
        foreach($nakproducts as $key => $product) {
            if(isset($product['product_name'])) {
                $product_name = $product['product_name'];
                $norm_weight = $product['norm_weight'];
                $div = $product['div'];
                
                // Haqiqiy sarflanish
                $actual_consumption = 0;
                foreach($days as $day) {
                    if(isset($product[$day->id])) {
                        $actual_consumption += $product[$day->id];
                    }
                }
                
                // Norm bo'yicha kerakli miqdor
                if(mb_substr($product_name, 0, 3) == 'Тух') {
                    $norm_required = $norm_weight * $numberOfChild;
                } else {
                    $norm_required = ($norm_weight * $numberOfChild) / $div;
                }
                
                // Farq
                $difference = $actual_consumption - $norm_required;
                
                // Ta'minlanish darajasi
                $supply_percentage = $norm_required > 0 ? ($actual_consumption / $norm_required) * 100 : 0;
                
                // Jami hisoblash
                $total_norm_required += $norm_required;
                $total_actual_consumption += $actual_consumption;
                $total_difference += $difference;
                
                $row = [
                    $product_name,
                    $norm_weight,
                    number_format($norm_required, 3),
                    number_format($actual_consumption, 3),
                    number_format($difference, 3),
                    number_format($supply_percentage, 3)
                ];
                
                $data[] = $row;
            }
        }
        
        // Jami qatori - PDF bilan bir xil
        $total_supply_percentage = $total_norm_required > 0 ? ($total_actual_consumption / $total_norm_required) * 100 : 0;
        
        $totalRow = [
            'Жами',
            '',
            number_format($total_norm_required, 3),
            number_format($total_actual_consumption, 3),
            number_format($total_difference, 3),
            number_format($total_supply_percentage, 3)
        ];
        $data[] = $totalRow;
        
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
                
                // Header merge va style - PDF bilan bir xil
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                
                // Kindgarden va age info style
                $sheet->getStyle('A3')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3')->getFont()->setBold(true);
                
                $sheet->getStyle('A4')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4')->getFont()->setBold(true);
                
                // Jadval header style - PDF bilan bir xil
                $headerRow = 6; // Jadval header qatori
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
                $dataEndRow = $highestRow - 1; // Jami qatoridan oldin
                $dataRange = 'A' . $dataStartRow . ':' . $highestColumn . $dataEndRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Mahsulot nomlari uchun left alignment
                $sheet->getStyle('A' . $dataStartRow . ':A' . $dataEndRow)
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Jami qatori style
                $totalRow = $highestRow;
                $sheet->getStyle('A' . $totalRow . ':' . $highestColumn . $totalRow)
                      ->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':' . $highestColumn . $totalRow)
                      ->applyFromArray([
                          'fill' => [
                              'fillType' => Fill::FILL_SOLID,
                              'startColor' => ['rgb' => 'D0D0D0'],
                          ],
                          'borders' => [
                              'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                          ],
                      ]);
                
                // Number format - raqamli ustunlar uchun
                $sheet->getStyle('B' . $dataStartRow . ':' . $highestColumn . $highestRow)
                      ->getNumberFormat()->setFormatCode('#,##0.000');
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(35); // Махсулот номи
                $sheet->getColumnDimension('B')->setWidth(25); // 1 бола учун уртача кунлик меъёр
                $sheet->getColumnDimension('C')->setWidth(30); // меъёр бўйича сарфланиши лозим булган
                $sheet->getColumnDimension('D')->setWidth(20); // Хақиқий харажат
                $sheet->getColumnDimension('E')->setWidth(25); // Меъёрга нисбатан фарқи
                $sheet->getColumnDimension('F')->setWidth(20); // таъминланиш даражаси
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,  // Махсулот номи
            'B' => 25,  // 1 бола учун уртача кунлик меъёр
            'C' => 30,  // меъёр бўйича сарфланиши лозим булган
            'D' => 20,  // Хақиқий харажат
            'E' => 25,  // Меъёрга нисбатан фарқи
            'F' => 20,  // таъминланиш даражаси
        ];
    }
} 