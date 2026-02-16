<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
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

class TransportationRegionExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $start, $end;

    public function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }

    public function array(): array
    {
        $kindgardens = Kindgarden::where('region_id', $this->id)->where('hide', 1)->get();
        $region = Region::where('id', $this->id)->first();
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $this->id)
            ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
            ->where('end_date', '>=', $days[count($days) - 1]->created_at->format('Y-m-d'))
            ->get();

        $number_childrens = [];
        foreach ($days as $day) {
            foreach ($ages as $age) {
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
            }
            $number_childrens[$day->id]["menu"] = Number_children::where('number_childrens.day_id', $day->id)
                ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                ->first();
        }

        $data = [];

        // Header qismi
        $data[] = [$region->region_name . 'ида ' . $days[0]->year_name . ' йил ' . $days[0]->day_number . '-' . $days[count($days) - 1]->day_number . ' ' . $days[0]->month_name . ' кунлари болалар катнови ва аутсорсинг хизмати харажатлари тўғрисида маълумот'];
        $data[] = [''];

        // Jadval header - 1-qator (asosiy headers)
        $header1 = ['№', 'Таомнома', 'Сана', 'Буюртма бўйича бола сони', '', '', 'Бир нафар болага сарфланган харажат НДС билан', '', 'Жами етказиб бериш харажат НДС билан', '', ''];

        // Age range headers qo'shish
        foreach ($ages as $age) {
            $header1[] = 'Жами етказиб бериш харажатлари (' . $age->description . ')';
            $header1[] = '';
            $header1[] = '';
            $header1[] = '';
        }
        $header1[] = 'Жами етказиб бериш суммаси (НДС билан)';

        $data[] = $header1;

        // Jadval header - 2-qator (sub headers)
        $raise = $costs->where('age_range_id', 4)->first()->raise ?? 0;
        $nds = $costs->where('age_range_id', 4)->first()->nds ?? 0;

        $header2 = ['', '', '', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами'];

        foreach ($ages as $age) {
            $header2[] = 'Сумма (безНДС)';
            $header2[] = 'Устама ҳақ ' . $raise . '%';
            $header2[] = 'ҚҚС (НДС) ' . $nds . '%';
            $header2[] = 'Жами сумма';
        }
        $header2[] = '';

        $data[] = $header2;

        // Ma'lumotlar qatorlari
        $row_number = 1;
        $currentDataRow = 5; // Ma'lumotlar 5-qatordan boshlanadi

        foreach ($days as $day) {
            // Bolalar sonini hisoblash
            $children_age4 = 0; // 9-10.5 soatlik
            $children_age5 = 0; // 10-12 soatlik
            $children_4 = 0; // 4 soatlik
            $menu_name = '';

            foreach ($number_childrens[$day->id] as $age_id => $child) {
                if ($age_id == "menu") {
                    $menu_name = $child->short_name ?? $child->menu_name ?? '';
                    continue;
                }
                if ($age_id == 4) { // 9-10.5 soatlik guruh
                    $children_age4 += $child ?? 0;
                }
                elseif ($age_id == 5) { // 10-12 soatlik guruh
                    $children_age5 += $child ?? 0;
                }
                elseif ($age_id == 3) { // 4 soatlik guruh
                    $children_4 += $child ?? 0;
                }
            }

            // Birlashtirilgan bolalar soni
            $children_9_10 = $children_age4 + $children_age5;
            $children_all = $children_9_10 + $children_4;

            // Narxlarni olish
            $eater_cost_age4 = $costs->where('age_range_id', 4)->first()->eater_cost ?? 0;
            $eater_cost_age5 = $costs->where('age_range_id', 5)->first()->eater_cost ?? 0;
            $eater_cost4 = $costs->where('age_range_id', 3)->first()->eater_cost ?? 0;

            // O'rtacha narxni hisoblash (9-10.5 va 10-12 soatlik uchun)
            if ($children_9_10 > 0) {
                $eater_cost9_10 = (($children_age4 * $eater_cost_age4) + ($children_age5 * $eater_cost_age5)) / $children_9_10;
            }
            else {
                // Bolalar bo'lmasa, default qilib 4-guruh narxini olamiz
                $eater_cost9_10 = $eater_cost_age4;
            }

            $row = [
                $row_number++,
                $menu_name,
                $day->day_number . '/' . $day->month_name . '/' . $day->year_name,
                $children_9_10,
                $children_4,
                '=D' . $currentDataRow . '+E' . $currentDataRow, // Jami bolalar soni
                $eater_cost9_10,
                $eater_cost4,
                '=D' . $currentDataRow . '*G' . $currentDataRow, // Delivery 9-10.5 (O'rtacha narx bilan to'g'ri chiqadi)
                '=E' . $currentDataRow . '*H' . $currentDataRow, // Delivery 4
                '=I' . $currentDataRow . '+J' . $currentDataRow // Jami delivery
            ];

            // Age range calculations
            $col = 'L'; // Starting from column L for age calculations
            foreach ($ages as $age) {
                if ($age->id == 4) { // 9-10.5 soatlik
                    // Summa (bezNDS)
                    $row[] = '=I' . $currentDataRow . '/(1+' . ($nds / 100) . ')';
                    // Ustama
                    $row[] = '=' . $col . $currentDataRow . '*' . ($raise / 100);
                    // NDS
                    $row[] = '=' . $col . $currentDataRow . '*' . ($nds / 100);
                    // Jami
                    $row[] = '=' . $col . $currentDataRow . '+' . chr(ord($col) + 1) . $currentDataRow . '+' . chr(ord($col) + 2) . $currentDataRow;
                }
                elseif ($age->id == 3) { // 4 soatlik
                    // Summa (bezNDS) - J ustunidan olish
                    $row[] = '=J' . $currentDataRow . '/(1+' . ($nds / 100) . ')';
                    // Ustama - L ustunidan olish (4 soatlik guruh uchun)
                    $row[] = '=' . $col . $currentDataRow . '*' . ($raise / 100);
                    // NDS - L ustunidan olish (4 soatlik guruh uchun)
                    $row[] = '=' . $col . $currentDataRow . '*' . ($nds / 100);
                    // Jami
                    $row[] = '=' . $col . $currentDataRow . '+' . chr(ord($col) + 1) . $currentDataRow . '+' . chr(ord($col) + 2) . $currentDataRow;
                }
                $col = chr(ord($col) + 4);
            }

            // Final total
            $row[] = '=' . chr(ord($col) - 4) . $currentDataRow . '+' . $col . $currentDataRow;

            $data[] = $row;
            $currentDataRow++;
        }

        // Jami qatori
        $totalRow = ['', '', 'ЖАМИ'];
        for ($i = 4; $i <= count($header1); $i++) {
            $colLetter = $this->getColumnLetter($i);
            $totalRow[] = '=SUM(' . $colLetter . '5:' . $colLetter . ($currentDataRow - 1) . ')';
        }
        $data[] = $totalRow;

        // Footer
        $data[] = [''];
        $data[] = ['Аутсорсер директори: ____________________________', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Буюртмачи директори: ____________________________'];

        return $data;
    }

    private function getColumnLetter($columnNumber)
    {
        $dividend = $columnNumber;
        $columnName = '';
        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = intval(($dividend - $modulo) / 26);
        }
        return $columnName;
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Header merge va style
            $sheet->mergeCells('A1:' . $highestColumn . '1');
            $sheet->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);

            // Jadval header merge cells (rowspan va colspan)
            // 1-qator merges
            $sheet->mergeCells('A3:A4'); // №
            $sheet->mergeCells('B3:B4'); // Таомнома
            $sheet->mergeCells('C3:C4'); // Сана
            $sheet->mergeCells('D3:F3'); // Буюртма бўйича бола сони
            $sheet->mergeCells('G3:H3'); // Бир нафар болага сарфланган харажат
            $sheet->mergeCells('I3:K3'); // Жами етказиб бериш харажат

            // Age range headers
            $col = 'L';
            foreach (Age_range::all() as $age) {
                $endCol = chr(ord($col) + 3);
                $sheet->mergeCells($col . '3:' . $endCol . '3');
                $col = chr(ord($endCol) + 1);
            }
            $sheet->mergeCells($col . '3:' . $col . '4'); // Final total

            // Jadval header style
            $sheet->getStyle('A3:' . $highestColumn . '4')->applyFromArray([
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
            $dataRange = 'A5:' . $highestColumn . ($highestRow - 2);
            $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

            // Jami qatori style
            $sheet->getStyle('A' . ($highestRow - 1) . ':' . $highestColumn . ($highestRow - 1))
                ->getFont()->setBold(true);
            $sheet->getStyle('A' . ($highestRow - 1) . ':' . $highestColumn . ($highestRow - 1))
                ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D0D0D0'],
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

            // Footer merge
            $sheet->mergeCells('A' . $highestRow . ':J' . $highestRow);
            $sheet->mergeCells('K' . $highestRow . ':' . $highestColumn . $highestRow);

            // Number format
            $sheet->getStyle('D5:' . $highestColumn . ($highestRow - 1))
                ->getNumberFormat()->setFormatCode('#,##0.00');
        },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, // №
            'B' => 12, // Таомнома
            'C' => 15, // Сана
            'D' => 12, // 9-10.5 соатлик бола сони
            'E' => 12, // 4 соатлик бола сони
            'F' => 10, // Жами
            'G' => 12, // 9-10.5 соатлик нарх
            'H' => 12, // 4 соатлик нарх
            'I' => 15, // 9-10.5 delivery
            'J' => 15, // 4 delivery
            'K' => 15, // Жами delivery
            'L' => 15, // Age 1 - bezNDS
            'M' => 12, // Age 1 - ustama
            'N' => 12, // Age 1 - NDS
            'O' => 15, // Age 1 - jami
            'P' => 15, // Age 2 - bezNDS
            'Q' => 12, // Age 2 - ustama
            'R' => 12, // Age 2 - NDS
            'S' => 15, // Age 2 - jami
            'T' => 20, // Final total
        ];
    }
} 