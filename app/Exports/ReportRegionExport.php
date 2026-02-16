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

class ReportRegionExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
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
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);

        $costs = Protsent::where('region_id', $this->id)
            ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
            ->where('end_date', '>=', $days[count($days) - 1]->created_at->format('Y-m-d'))
            ->get();

        $region = Region::where('id', $this->id)->first();
        $ages = Age_range::all();
        $kindgardens = Kindgarden::where('region_id', $this->id)->where('hide', 1)->get();

        $number_childrens = [];
        foreach ($kindgardens as $kindgarden) {
            foreach ($ages as $age) {
                $number_childrens[$kindgarden->id][$age->id] = Number_children::where('number_childrens.day_id', '>=', $this->start)
                    ->where('number_childrens.day_id', '<=', $this->end)
                    ->where('kingar_name_id', $kindgarden->id)
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
            }
        }

        $data = [];

        // Header qismi
        $data[] = [$region->region_name . ' мактабгача таълим ташкилотларига ' . $days[0]->month_name . ' ойида кўрсатилган Аутсорсинг хизмати хисоб китоби'];
        $data[] = [''];

        // Jadval header - 1-qator (asosiy headers)
        $data[] = ['Ташкилот номи', 'Буюртма бўйича бола сони', '', '1 бола учун белгиланган нарх', '', 'Жами харажат (сўмда)', 'ҚҚСсиз жами харажат', 'Устама ' . ($costs[0]->raise ?? 0) . '%', 'Жами суммаси', 'ҚҚС ' . ($costs[0]->nds ?? 0) . '%', 'Шартноманинг умумий қиймати ҚҚС билан'];

        // Jadval header - 2-qator (sub headers)
        $data[] = ['', '3-7 ёш', 'Қисқа гр', '3-7 ёш', 'Қисқа гр', '', '', '', '', '', ''];

        // Ma'lumotlar qatorlari
        $currentDataRow = 5; // Ma'lumotlar 5-qatordan boshlanadi

        foreach ($kindgardens as $kindgarden) {
            // Bolalar soni (alohida)
            $children_age4 = $number_childrens[$kindgarden->id][4] ?? 0; // 9-10.5 soatlik
            $children_age5 = $number_childrens[$kindgarden->id][5] ?? 0; // 10-12 soatlik
            $children_3_7 = $children_age4 + $children_age5; // 3-7 yosh umumiy

            $children_short = $number_childrens[$kindgarden->id][3] ?? 0; // Qisqa guruh 

            // Narxlar
            $price_age4 = $costs->where('age_range_id', 4)->first()->eater_cost ?? 0;
            $price_age5 = $costs->where('age_range_id', 5)->first()->eater_cost ?? 0;
            $price_short = $costs->where('age_range_id', 3)->first()->eater_cost ?? 0; // Qisqa guruh uchun narx

            // O'rtacha narx 3-7 yosh uchun
            if ($children_3_7 > 0) {
                $price_3_7 = (($children_age4 * $price_age4) + ($children_age5 * $price_age5)) / $children_3_7;
            }
            else {
                $price_3_7 = $price_age4;
            }

            $row = [
                $kindgarden->number_of_org . '-ДМТТ',
                $children_3_7,
                $children_short,
                $price_3_7,
                $price_short,
                // Jami xarajat formula
                '=(B' . $currentDataRow . '*D' . $currentDataRow . ')+(C' . $currentDataRow . '*E' . $currentDataRow . ')',
                // QQSsiz jami xarajat
                '=F' . $currentDataRow . '/(1+' . (($costs[0]->nds ?? 0) / 100) . ')',
                // Ustama
                '=G' . $currentDataRow . '*' . (($costs[0]->raise ?? 0) / 100),
                // Jami summasi
                '=G' . $currentDataRow . '+H' . $currentDataRow,
                // QQS
                '=I' . $currentDataRow . '*' . (($costs[0]->nds ?? 0) / 100),
                // Shartnomaning umumiy qiymati QQS bilan
                '=I' . $currentDataRow . '+J' . $currentDataRow
            ];

            $data[] = $row;
            $currentDataRow++;
        }

        // Jami qatori
        $totalRow = ['ЖАМИ:'];
        for ($i = 2; $i <= 11; $i++) {
            $colLetter = $this->getColumnLetter($i);
            $totalRow[] = '=SUM(' . $colLetter . '5:' . $colLetter . ($currentDataRow - 1) . ')';
        }
        $data[] = $totalRow;

        // Footer
        $data[] = [''];
        $data[] = ['Аутсорсер директори: ____________________________', '', '', '', '', '', '', '', '', '', 'Буюртмачи директори: ____________________________'];

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
            $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);

            // Jadval header merge cells (rowspan va colspan)
            $sheet->mergeCells('A3:A4'); // Ташкилот номи
            $sheet->mergeCells('B3:C3'); // Буюртма бўйича бола сони
            $sheet->mergeCells('D3:E3'); // 1 бола учун белгиланган нарх
            $sheet->mergeCells('F3:F4'); // Жами харажат
            $sheet->mergeCells('G3:G4'); // ҚҚСсиз жами харажат
            $sheet->mergeCells('H3:H4'); // Устама
            $sheet->mergeCells('I3:I4'); // Жами суммаси
            $sheet->mergeCells('J3:J4'); // ҚҚС
            $sheet->mergeCells('K3:K4'); // Шартноманинг умумий қиймати

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
            $sheet->mergeCells('A' . $highestRow . ':E' . $highestRow);
            $sheet->mergeCells('F' . $highestRow . ':' . $highestColumn . $highestRow);

            // Number format
            $sheet->getStyle('B5:' . $highestColumn . ($highestRow - 1))
                ->getNumberFormat()->setFormatCode('#,##0.00');

            // Tashkilot nomlari uchun left alignment
            $sheet->getStyle('A5:A' . ($highestRow - 1))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // Ташкилот номи
            'B' => 12, // 3-7 ёш
            'C' => 12, // Қисқа гр
            'D' => 15, // 3-7 ёш нарх
            'E' => 15, // Қисқа гр нарх
            'F' => 15, // Жами харажат
            'G' => 18, // ҚҚСсиз жами харажат
            'H' => 15, // Устама
            'I' => 15, // Жами суммаси
            'J' => 12, // ҚҚС
            'K' => 25, // Умумий қиймат
        ];
    }
} 