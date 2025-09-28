<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SvodExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $start, $end, $kindgardens, $region_id, $cost_id, $over, $nds;
    
    public function __construct($start, $end, $kindgardens, $region_id, $cost_id, $over, $nds)
    {
        $this->start = $start;
        $this->end = $end;
        $this->kindgardens = $kindgardens;
        $this->region_id = $region_id;
        $this->cost_id = $cost_id;
        $this->over = $over;
        $this->nds = $nds;
    }

    public function array(): array
    {
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);

        $regions = Region::all();
        $nakproducts = [];
        $kindgardens = [];
        
        foreach($this->kindgardens as $row_id){
            array_push($kindgardens, Kindgarden::where('id', $row_id)->first());
            foreach($days as $day){
                $ages = Age_range::all();
                foreach($ages as $age){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('kingar_name_id', $row_id)
                        ->where('king_age_name_id', $age->id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                    $productscount = array();
                    foreach($join as $row){
                        if(!isset($productscount[$row->product_name_id][$row->age_range_id])){
                            $productscount[$row->product_name_id][$row->age_range_id] = 0;
                        }
                        $productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
                        $productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
                        $productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
                        $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                        $productscount[$row->product_name_id][$row->age_range_id.'sort'] = $row->sort;
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    
                    foreach($productscount as $key => $row){
                        if(!isset($nakproducts[$key][$row_id])){
                            $nakproducts[$key][$row_id] = 0;
                        }
                        $nakproducts[$key][$row_id] += ($row[$age->id]*$row[$age->id.'-children']) / $row[$age->id.'div'];
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['sort'] = $row[$age->id.'sort'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                    }
                }
            }
        }
        
        $costs = bycosts::where('day_id', $this->cost_id)
                ->where('region_name_id', $this->region_id)
                ->orderBy('day_id', 'DESC')->get();

        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        // Excel ma'lumotlarini tayyorlash
        $data = [];
        
        // Sarlavha qo'shish
        $region_name = $regions->find($kindgardens[0]->region_id)->region_name;
        $data[] = [$region_name . " мттларнинг " . $days[0]->year_name . " йил " . $days[0]->month_name . " ойида берилган озиқ овқат махсулотларининг хисоб-китоби"];
        $data[] = []; // Bo'sh qator

        // Jadval sarlavhalari
        $headers = ['Махсулот', 'Ўл.бир', 'Нарх'];
        foreach($kindgardens as $kg) {
            $headers[] = $kg->kingar_name;
        }
        $headers[] = 'КГ';
        $headers[] = 'Сумма';
        $data[] = $headers;

        // Ma'lumotlar qatorlari
        foreach($nakproducts as $key => $row){
            if(isset($row['product_name'])){
                $rowData = [
                    $row['product_name'],
                    $row['size_name'],
                    number_format($row[0] ?? 0, 2)
                ];
                
                $summ = 0;
                foreach($kindgardens as $kg){
                    $value = $row[$kg->id] ?? 0;
                    $rowData[] = number_format($value, 3);
                    $summ += $value;
                }
                
                $rowData[] = number_format($summ, 3);
                $rowData[] = number_format($summ * ($row[0] ?? 0), 2);
                
                $data[] = $rowData;
            }
        }

        // Jami qator
        $totalRow = ['Жами:', '', ''];
        $regionsumm = [];
        $totalSum = 0;
        
        foreach($kindgardens as $kg){
            $kgSum = 0;
            foreach($nakproducts as $key => $row){
                if(isset($row['product_name']) && isset($row[$kg->id]) && isset($row[0])){
                    $kgSum += $row[$kg->id] * $row[0];
                }
            }
            $totalRow[] = number_format($kgSum, 2);
            $totalSum += $kgSum;
        }
        $totalRow[] = '';
        $totalRow[] = number_format($totalSum, 2);
        $data[] = $totalRow;

        // Ustama qatori
        $ustRow = ['Устама ' . $this->over . '%', '', ''];
        foreach($kindgardens as $kg){
            $kgSum = 0;
            foreach($nakproducts as $key => $row){
                if(isset($row['product_name']) && isset($row[$kg->id]) && isset($row[0])){
                    $kgSum += $row[$kg->id] * $row[0];
                }
            }
            $ustRow[] = number_format($kgSum / 100 * $this->over, 2);
        }
        $ustRow[] = '';
        $ustRow[] = number_format($totalSum / 100 * $this->over, 2);
        $data[] = $ustRow;

        // Summa ustama bilan
        $ustTotalRow = ['Сумма Устама билан', '', ''];
        foreach($kindgardens as $kg){
            $kgSum = 0;
            foreach($nakproducts as $key => $row){
                if(isset($row['product_name']) && isset($row[$kg->id]) && isset($row[0])){
                    $kgSum += $row[$kg->id] * $row[0];
                }
            }
            $ustTotalRow[] = number_format($kgSum + $kgSum / 100 * $this->over, 2);
        }
        $ustTotalRow[] = '';
        $ustTotalRow[] = number_format($totalSum + $totalSum / 100 * $this->over, 2);
        $data[] = $ustTotalRow;

        // NDS qatori
        $ndsRow = ['НДС ' . $this->nds . '%', '', ''];
        foreach($kindgardens as $kg){
            $kgSum = 0;
            foreach($nakproducts as $key => $row){
                if(isset($row['product_name']) && isset($row[$kg->id]) && isset($row[0])){
                    $kgSum += $row[$kg->id] * $row[0];
                }
            }
            $ndsRow[] = number_format(($kgSum + $kgSum / 100 * $this->over) / 100 * $this->nds, 2);
        }
        $ndsRow[] = '';
        $ndsRow[] = number_format(($totalSum + $totalSum / 100 * $this->over) / 100 * $this->nds, 2);
        $data[] = $ndsRow;

        // Jami summa NDS bilan
        $finalRow = ['Жами сумма НДС билан', '', ''];
        foreach($kindgardens as $kg){
            $kgSum = 0;
            foreach($nakproducts as $key => $row){
                if(isset($row['product_name']) && isset($row[$kg->id]) && isset($row[0])){
                    $kgSum += $row[$kg->id] * $row[0];
                }
            }
            $finalSum = $kgSum + $kgSum / 100 * $this->over + ($kgSum + $kgSum / 100 * $this->over) / 100 * $this->nds;
            $finalRow[] = number_format($finalSum, 2);
        }
        $finalRow[] = '';
        $finalSum = $totalSum + $totalSum / 100 * $this->over + ($totalSum + $totalSum / 100 * $this->over) / 100 * $this->nds;
        $finalRow[] = number_format($finalSum, 2);
        $data[] = $finalRow;

        return $data;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 25,  // Махсулот
            'B' => 8,   // Ўл.бир
            'C' => 10,  // Нарх
        ];
        
        // Ustunlar nomlarini yaratish (D dan boshlab)
        $columns = range('D', 'Z');
        if(count($this->kindgardens) * 2 + 2 > 23) {
            // Agar ko'p ustun kerak bo'lsa, AA, AB, AC... ni qo'shamiz
            foreach(range('A', 'Z') as $first) {
                foreach(range('A', 'Z') as $second) {
                    $columns[] = $first . $second;
                }
            }
        }
        
        $columnIndex = 0;
        // Har bir bog'cha uchun 2 ta ustun
        for($i = 0; $i < count($this->kindgardens) * 2; $i++) {
            if(isset($columns[$columnIndex])) {
                $widths[$columns[$columnIndex]] = 12;
                $columnIndex++;
            }
        }
        
        // KG va Summa ustunlari
        if(isset($columns[$columnIndex])) {
            $widths[$columns[$columnIndex]] = 12;  // КГ
            $columnIndex++;
        }
        if(isset($columns[$columnIndex])) {
            $widths[$columns[$columnIndex]] = 15;  // Сумма
        }
        
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE6E6FA']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Barcha ma'lumotlar uchun border qo'shish
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                $sheet->getStyle('A3:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Sarlavhani merge qilish
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                
                // Jami qatorlarni bold qilish
                $totalRows = [$lastRow - 4, $lastRow - 3, $lastRow - 2, $lastRow - 1, $lastRow];
                foreach($totalRows as $row) {
                    $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => 'FFF0F0F0']
                        ],
                    ]);
                }
            },
        ];
    }
} 