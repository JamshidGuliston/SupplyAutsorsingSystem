<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Protsent;
use App\Models\Region;
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

class NakapitelExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $request, $id, $ageid, $start, $end, $costid, $nds, $ust;
    
    public function __construct(Request $request, $id, $ageid, $start, $end, $costid, $nds, $ust)
    {
        $this->request = $request;
        $this->id = $id;
        $this->ageid = $ageid;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
        $this->nds = $nds;
        $this->ust = $ust;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $region = Region::where('id', $kindgar->region_id)->first();
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
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                $productscount[$row->product_name_id][$this->ageid.'sort'] = $row->sort;
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

        $costs = bycosts::where('day_id', $this->costid)->where('region_name_id', Kindgarden::where('id', $this->id)->first()->region_id)
                ->orderBy('day_id', 'DESC')->get();
        
        foreach($costs as $cost){
            $nakproducts[0][0] = 0;
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        $costsdays = bycosts::where('day_id', $this->costid)
                    ->where('region_name_id', Kindgarden::where('id', $this->id)->first()->region_id)
                    ->join('days', 'bycosts.day_id', '=', 'days.id')
                    ->join('years', 'days.year_id', '=', 'years.id')
                    ->orderBy('day_id', 'DESC')
                    ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
        $costs = [];
        $bool = [];
        foreach($costsdays as $row){
            if(!isset($bool[$row->day_id])){
                array_push($costs, $row);
                $bool[$row->day_id] = 1;
            }
        }

        $protsent = Protsent::where('region_id', Kindgarden::where('id', $this->id)->first()->region_id)
                    ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                    ->where('age_range_id', $this->ageid)
                    ->first();

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $data = [];
        
        // Header - PDF bilan bir xil
        $first_month_id = $days[0]->month_id % 12 == 0 ? 12 : $days[0]->month_id % 12;
        $last_month_id = $days[count($days)-1]->month_id % 12 == 0 ? 12 : $days[count($days)-1]->month_id % 12;
        
        $data[] = [env('COMPANY_NAME') . ' томонида Аутсорсинг хизмати кўрсатилаётган ' . $region->region_name . ' ' . $kindgar->number_of_org . '-сонли ДМТТнинг ' . $days->first()->day_number . '.' . sprintf('%02d', $first_month_id) . '.' . sprintf('%02d', $costs[0]->year_name) . ' йилдан ' . $days->last()->day_number . '.' . sprintf('%02d', $last_month_id) . '.' . sprintf('%02d', $costs[0]->year_name) . ' йилгача ' . $age->age_name . 'ли гуруҳи учун НАКАПИТЕЛ'];
        $data[] = [''];
        
        // Jadval header - PDF bilan bir xil
        $header = ['№', 'Махсулот номи', 'Ўлчов бирлиги', 'Нарх'];
        foreach($days as $day) {
            $month_id = $day->month_id % 12 == 0 ? 12 : $day->month_id % 12;
            $header[] = $day->day_number . '.' . sprintf('%02d', $month_id);
        }
        $header[] = 'Жами миқдор';
        $header[] = 'Жами сумма';
        $data[] = $header;
        
        // Ma'lumotlar
        $row_number = 1;
        $total_children = 0;
        $total_cost = 0;
        
        foreach($nakproducts as $key => $product) {
            if(isset($product['product_name'])) {
                $price = $product[0] ?? 0;
                $row = [$row_number++, $product['product_name'], $product['size_name'], number_format($price, 2)];
                
                $total_quantity = 0;
                foreach($days as $day) {
                    $value = $product[$day->id] ?? 0;
                    $row[] = number_format($value, 2);
                    $total_quantity += $value;
                }
                $total_sum = $total_quantity * $price;
                $row[] = number_format($total_quantity, 2);
                $row[] = number_format($total_sum, 2);
                
                // Bolalar sonini hisoblash
                if($product['product_name'] == "Болалар сони") {
                    $total_children = $total_quantity;
                }
                $total_cost += $total_sum;
                
                $data[] = $row;
            }
        }
        
        // Jadval oxiriga qo'shiladigan qatorlar - PDF bilan bir xil
        $protsent = Protsent::where('region_id', Kindgarden::where('id', $this->id)->first()->region_id)
                    ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                    ->where('age_range_id', $this->ageid)
                    ->first();
        
        if($protsent) {
            $eater_cost = $protsent->eater_cost ?? 0;
            $raise = $protsent->raise ?? 28.5;
            
            // 1 болани бир куник харажати
            $row1 = ['', '1 болани бир куник харажати', '', number_format($eater_cost, 2)];
            for($i = 0; $i < count($days); $i++) {
                $row1[] = '';
            }
            $row1[] = '';
            $row1[] = '';
            $data[] = $row1;
            
            // Ko'rsatilgan xizmat summasi QQS bilan
            $service_sum = $eater_cost * $total_children;
            $row2 = ['', 'Ko\'rsatilgan xizmat summasi QQS bilan', '', number_format($service_sum, 2)];
            for($i = 0; $i < count($days); $i++) {
                $row2[] = '';
            }
            $row2[] = '';
            $row2[] = '';
            $data[] = $row2;
            
            // Белгиланган устама
            $markup_amount = $service_sum * $raise / 100;
            $row3 = ['', 'Белгиланган устама ' . $raise . '%', '', number_format($markup_amount, 2)];
            for($i = 0; $i < count($days); $i++) {
                $row3[] = '';
            }
            $row3[] = '';
            $row3[] = '';
            $data[] = $row3;
            
            // 1 ойлик жами харажат
            $total_monthly_cost = $service_sum + $markup_amount;
            $row4 = ['', '1 ойлик жами харажат', '', number_format($total_monthly_cost, 2)];
            for($i = 0; $i < count($days); $i++) {
                $row4[] = '';
            }
            $row4[] = '';
            $row4[] = '';
            $data[] = $row4;
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
            'D' => 12,  // Нарх
        ];
    }
}
