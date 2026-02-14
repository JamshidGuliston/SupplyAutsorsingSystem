<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Active_menu;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Protsent;
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

class ReportProductsOfRegionExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $start, $end, $ageid;

    public function __construct($id, $start, $end, $ageid)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->ageid = $ageid;
    }

    public function array(): array
    {
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'days.month_id', 'years.year_name', 'days.created_at']);

        $protsent = Protsent::where('region_id', $this->id)
            ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
            ->where('end_date', '>=', $days[count($days) - 1]->created_at->format('Y-m-d'))
            ->get();

        $age = Age_range::where('id', $this->ageid)->first();
        $products = Product::all();
        $region = Region::where('id', $this->id)->first();
        $kindgardens = Kindgarden::where('region_id', $this->id)->where('hide', 1)->get();

        $nakproducts = [];
        foreach ($days as $day) {
            $join = Number_children::where('number_childrens.day_id', $day->id)
                ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                ->where('king_age_name_id', $this->ageid)
                ->get();

            $productscount = [];
            foreach ($join as $row) {
                $active_menu = Active_menu::where('day_id', $day->id)
                    ->where('title_menu_id', $row->kingar_menu_id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get();

                foreach ($active_menu as $menu) {
                    if (!isset($productscount[$row->kingar_name_id][$menu->product_name_id])) {
                        $productscount[$row->kingar_name_id][$menu->product_name_id] = 0;
                    }
                    $productscount[$row->kingar_name_id][$menu->product_name_id] += $menu->weight;
                }
            }

            foreach ($productscount as $key => $row) {
                $product = Product::whereIn('products.id', array_keys($row))
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get(['products.id', 'products.product_name', 'sizes.size_name', 'products.sort', 'products.div']);
                $childs = Number_children::where('day_id', $day->id)
                    ->where('kingar_name_id', $key)
                    ->where('king_age_name_id', $this->ageid)
                    ->sum('kingar_children_number');
                if (!isset($nakproducts[0][$day->id])) {
                    $nakproducts[0][$day->id] = 0;
                    $nakproducts[0]['product_name'] = "Болалар сони";
                    $nakproducts[0]['size_name'] = "";
                }
                $nakproducts[0][$day->id] += $childs;
                foreach ($row as $product_id => $weight) {
                    if (!isset($nakproducts[$product_id][$day->id])) {
                        $nakproducts[$product_id][$day->id] = 0;
                        $nakproducts[$product_id]['product_name'] = $product->where('id', $product_id)->first()->product_name;
                        $nakproducts[$product_id]['sort'] = $product->where('id', $product_id)->first()->sort;
                        $nakproducts[$product_id]['size_name'] = $product->where('id', $product_id)->first()->size_name ?? '';
                    }
                    $nakproducts[$product_id][$day->id] += ($weight * $childs) / $product->where('id', $product_id)->first()->div;
                }
            }
        }

        usort($nakproducts, function ($a, $b) {
            if (isset($a["sort"]) and isset($b["sort"])) {
                return $a["sort"] > $b["sort"];
            }
        });

        // Excel ma'lumotlarini tayyorlash
        $data = [];

        // Sarlavha qo'shish
        $data[] = [$region->region_name . " " . $age->age_name . " yoshli bolalar uchun mahsulotlar hisoboti"];
        $data[] = [$days[0]->day_number . " " . $days[0]->month_name . " " . $days[0]->year_name . " dan " .
            $days[count($days) - 1]->day_number . " " . $days[count($days) - 1]->month_name . " " . $days[count($days) - 1]->year_name . " gacha"];
        $data[] = []; // Bo'sh qator

        // Jadval sarlavhalari
        $headers = ['Махсулот номи', 'Ўл.бир'];
        foreach ($days as $day) {
            $headers[] = sprintf("%02d", $day->day_number) . "." . sprintf("%02d", $day->month_id % 12 == 0 ? 12 : $day->month_id % 12);
        }
        $headers[] = 'Жами';
        $data[] = $headers;

        // Ma'lumotlar qatorlari
        foreach ($nakproducts as $key => $row) {
            if (isset($row['product_name'])) {
                $rowData = [
                    $row['product_name'],
                    $row['size_name'] ?? ''
                ];

                $total = 0;
                foreach ($days as $day) {
                    $value = $row[$day->id] ?? 0;
                    $rowData[] = number_format($value, 3);
                    $total += $value;
                }

                $rowData[] = number_format($total, 3);
                $data[] = $rowData;
            }
        }

        return $data;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 25, // Махсулот номи
            'B' => 8, // Ўл.бир
        ];

        // Ustunlar nomlarini yaratish (C dan boshlab)
        $columns = range('C', 'Z');
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)->count();

        // Agar ko'p kun bo'lsa, AA, AB, AC... ni qo'shamiz
        if ($days + 1 > 24) {
            foreach (range('A', 'Z') as $first) {
                foreach (range('A', 'Z') as $second) {
                    $columns[] = $first . $second;
                }
            }
        }

        $columnIndex = 0;
        // Har bir kun uchun ustun kengligi
        for ($i = 0; $i < $days; $i++) {
            if (isset($columns[$columnIndex])) {
                $widths[$columns[$columnIndex]] = 10;
                $columnIndex++;
            }
        }

        // Jami ustuni
        if (isset($columns[$columnIndex])) {
            $widths[$columns[$columnIndex]] = 12;
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
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
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
            AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Barcha ma'lumotlar uchun border qo'shish
            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();

            $sheet->getStyle('A4:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

            // Sarlavhalarni merge qilish
            $sheet->mergeCells('A1:' . $lastColumn . '1');
            $sheet->mergeCells('A2:' . $lastColumn . '2');

            // Birinchi qatorni bold qilish (Болалар сони)
            if ($lastRow > 3) {
                $sheet->getStyle('A5:' . $lastColumn . '4')->applyFromArray([
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