<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Protsent;
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

class NakapitWithoutCostExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $request, $id, $ageid, $start, $end;
    
    public function __construct(Request $request, $id, $ageid, $start, $end)
    {
        $this->request = $request;
        $this->id = $id;
        $this->ageid = $ageid;
        $this->start = $start;
        $this->end = $end;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $this->ageid)->first();
        $days = Day::where('id', '>=', $this->start)->where('id', '<=', $this->end)->get();
        
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
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get();
            
            $productscount = [];
            foreach($join as $row){
                if(!isset($productscount[$row->product_name_id][$this->ageid])){
                    $productscount[$row->product_name_id][$this->ageid] = 0;
                }
                $productscount[$row->product_name_id][$this->ageid] += $row->weight;
                $productscount[$row->product_name_id][$this->ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->product_name_id][$this->ageid.'div'] = $row->div;
                $productscount[$row->product_name_id][$this->ageid.'sort'] = $row->sort;
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                $productscount[$row->product_name_id]['size_name'] = $row->size_name;
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $childs = Number_children::where('day_id', $day->id)
                                    ->where('kingar_name_id', $this->id)
                                    ->where('king_age_name_id', $this->ageid)
                                    ->sum('kingar_children_number');    
                    $nakproducts[0][$day->id] = $childs;
                    $nakproducts[0]['product_name'] = "Болалар сони";
                    $nakproducts[0]['size_name'] = "";
                    $nakproducts[$key][$day->id] = ($row[$this->ageid]*$row[$this->ageid.'-children']) / $row[$this->ageid.'div'];
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$this->ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
        }
        
        $protsent = Protsent::where('region_id', Kindgarden::where('id', $this->id)->first()->region_id)->first();
        
        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $data = [];
        
        // Header - oy ID sini to'g'rilash
        $first_month_id = $days[0]->month_id % 12 == 0 ? 12 : $days[0]->month_id % 12;
        $last_month_id = $days[count($days)-1]->month_id % 12 == 0 ? 12 : $days[count($days)-1]->month_id % 12;
        
        $data[] = [$kindgar->kingar_name . ' ' . $age->description . ' ёшдаги болаларнинг ' . $days[0]->day_number . '.' . $first_month_id . ' дан ' . $days[count($days)-1]->day_number . '.' . $last_month_id . ' гача бўлган махсулот сарфи хақида маълумот'];
        $data[] = [''];
        
        // Jadval header - oy ID sini to'g'rilash
        $header = ['№', 'Махсулот номи', 'Ўлчов бирлиги'];
        foreach($days as $day) {
            $month_id = $day->month_id % 12 == 0 ? 12 : $day->month_id % 12;
            $header[] = $day->day_number . '.' . $month_id;
        }
        $header[] = 'Жами';
        $data[] = $header;
        
        // Ma'lumotlar
        $row_number = 1;
        foreach($nakproducts as $key => $product) {
            if(isset($product['product_name'])) {
                $row = [$row_number++, $product['product_name'], $product['size_name']];
                
                $total = 0;
                foreach($days as $day) {
                    $value = $product[$day->id] ?? 0;
                    $row[] = number_format($value, 2);
                    $total += $value;
                }
                $row[] = number_format($total, 2);
                
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
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                
                // Jadval header style
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
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
                $dataRange = 'A4:' . $highestColumn . $highestRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Mahsulot nomlari uchun left alignment
                $sheet->getStyle('B4:B' . $highestRow)
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                
                // Number format
                $sheet->getStyle('D4:' . $highestColumn . $highestRow)
                      ->getNumberFormat()->setFormatCode('#,##0.00');
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // №
            'B' => 30,  // Махсулот номи
            'C' => 15,  // Ўлчов бирлиги
        ];
    }
} 