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

class ReportRegionSecondaryExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
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
        $ages = Age_range::orderBy('id', 'desc')->get();
        $costs = Protsent::where('region_id', $this->id)
            ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
            ->where('end_date', '>=', $days[count($days) - 1]->created_at->format('Y-m-d'))
            ->get();
        $region = Region::where('id', $this->id)->first();
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

        // Her bir age uchun alohida sahifa yaratish (PDF kabi)
        foreach ($ages as $age) {
            if (!empty($data)) {
                // Page break uchun bo'sh qatorlar
                $data[] = [''];
                $data[] = [''];
            }

            // Header qismi
            $data[] = [$region->region_name . ' ДМТТларда ' . $days[0]->day_number . '-' . $days[count($days) - 1]->day_number . ' ' . $days[0]->month_name . ' ' . $days[0]->year_name . ' йил кунлари ' . $age->description . 'и учун аутсорсинг хизмати харажатлари тўғрисидаги маълумот'];
            $data[] = [''];

            // Jadval header - 1-qator
            $data[] = ['', 'ДМТТ', 'Кунлар', 'Харжатлар', '', '', 'Жами'];

            // Jadval header - 2-qator  
            $data[] = ['', '', '', 'Сумма (ҚҚС сиз)', 'Устама хақ ' . ($costs[0]->raise ?? 28.5) . '%', 'ҚҚС ' . ($costs[0]->nds ?? 12) . '%', ''];

            // Ma'lumotlar qatorlari
            $currentDataRow = count($data) + 1;
            $row_number = 1;
            $hasData = false;

            foreach ($kindgardens as $kindgarden) {
                $children = $number_childrens[$kindgarden->id][$age->id] ?? 0;

                if ($children == 0) {
                    continue; // Skip if no children
                }

                $hasData = true;
                $price = $costs->where('age_range_id', $age->id)->first()->eater_cost ?? 0;

                $row = [
                    $row_number++,
                    $kindgarden->number_of_org . '-ДМТТ',
                    $row_number == 2 ? $days[0]->day_number . '-' . $days[count($days) - 1]->day_number . ' ' . $days[0]->month_name : '', // Rowspan effect
                    // QQSsiz jami xarajat formula
                    '=(' . $children . '*' . $price . ')/(1+' . (($costs[0]->nds ?? 12) / 100) . ')',
                    // Ustama formula
                    '=D' . $currentDataRow . '*' . (($costs[0]->raise ?? 28.5) / 100),
                    // QQS formula
                    '=(D' . $currentDataRow . '+E' . $currentDataRow . ')*' . (($costs[0]->nds ?? 12) / 100),
                    // Jami to'lanadigan summa
                    '=D' . $currentDataRow . '+E' . $currentDataRow . '+F' . $currentDataRow
                ];

                $data[] = $row;
                $currentDataRow++;
            }

            if ($hasData) {
                // Jami qatori
                $totalRow = ['', '', 'Жами'];
                for ($i = 4; $i <= 7; $i++) {
                    $colLetter = $this->getColumnLetter($i);
                    $startRow = count($data) - ($row_number - 2);
                    $endRow = count($data);
                    $totalRow[] = '=SUM(' . $colLetter . $startRow . ':' . $colLetter . $endRow . ')';
                }
                $data[] = $totalRow;

                // Footer
                $data[] = [''];
                $data[] = [env('COMPANY_NAME', ''), '', '', '', '', '', ''];
                $data[] = ['Директор: _____________________', '', '', '', '', '', ''];
            }
        }

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

            // Header merge va style (har bir age group uchun)
            $currentRow = 1;
            while ($currentRow <= $highestRow) {
                $cellValue = $sheet->getCell('A' . $currentRow)->getCalculatedValue();

                if (strpos($cellValue, 'ДМТТларда') !== false) {
                    // Header merge
                    $sheet->mergeCells('A' . $currentRow . ':' . $highestColumn . $currentRow);
                    $sheet->getStyle('A' . $currentRow)->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A' . $currentRow)->getFont()->setSize(14)->setBold(true);

                    // Jadval header qatorlarini topish
                    $headerRow1 = $currentRow + 2;
                    $headerRow2 = $currentRow + 3;

                    if ($headerRow2 <= $highestRow) {
                        // Merge cells for table headers
                        $sheet->mergeCells('A' . $headerRow1 . ':A' . $headerRow2); // №
                        $sheet->mergeCells('B' . $headerRow1 . ':B' . $headerRow2); // ДМТТ
                        $sheet->mergeCells('C' . $headerRow1 . ':C' . $headerRow2); // Кунлар
                        $sheet->mergeCells('D' . $headerRow1 . ':F' . $headerRow1); // Харжатлар
                        $sheet->mergeCells('G' . $headerRow1 . ':G' . $headerRow2); // Жами

                        // Jadval header style
                        $sheet->getStyle('A' . $headerRow1 . ':' . $highestColumn . $headerRow2)
                            ->applyFromArray([
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

                        // Ma'lumotlar qatorlarini topish va border qo'shish
                        $dataStartRow = $headerRow2 + 1;
                        $dataEndRow = $dataStartRow;

                        // Data qatorlarini sanash
                        while ($dataEndRow <= $highestRow) {
                            $cellValue = $sheet->getCell('A' . $dataEndRow)->getCalculatedValue();
                            if ($cellValue === '' || $cellValue === 'Жами' || strpos($cellValue, 'Директор') !== false) {
                                break;
                            }
                            $dataEndRow++;
                        }
                        $dataEndRow--;

                        if ($dataEndRow >= $dataStartRow) {
                            // Ma'lumotlar qismi border
                            $sheet->getStyle('A' . $dataStartRow . ':G' . $dataEndRow)
                                ->applyFromArray([
                                    'borders' => [
                                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                                    ],
                                ]);

                            // Jami qatori style
                            if ($dataEndRow + 1 <= $highestRow) {
                                $sheet->getStyle('A' . ($dataEndRow + 1) . ':G' . ($dataEndRow + 1))
                                    ->getFont()->setBold(true);
                                $sheet->getStyle('A' . ($dataEndRow + 1) . ':G' . ($dataEndRow + 1))
                                    ->applyFromArray([
                                        'fill' => [
                                            'fillType' => Fill::FILL_SOLID,
                                            'startColor' => ['rgb' => 'D0D0D0'],
                                        ],
                                        'borders' => [
                                            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                                        ],
                                    ]);
                            }
                        }
                    }

                    $currentRow = $headerRow2 + 20; // Skip to next potential section
                }
                else {
                    $currentRow++;
                }
            }

            // Number format
            $sheet->getStyle('D:G')->getNumberFormat()->setFormatCode('#,##0.00');
        },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8, // №
            'B' => 15, // ДМТТ
            'C' => 20, // Кунлар
            'D' => 18, // Сумма (ҚҚС сиз)
            'E' => 18, // Устама хақ
            'F' => 15, // ҚҚС
            'G' => 20, // Жами
        ];
    }
} 