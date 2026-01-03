<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ShopsHistoryReportExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $reportData;
    protected $days;
    protected $dateType;

    public function __construct($reportData, $days, $dateType)
    {
        $this->reportData = $reportData;
        $this->days = $days;
        $this->dateType = $dateType;
    }

    public function array(): array
    {
        $data = [];

        // Header
        $dateInfo = $this->getDateInfo();
        $data[] = ['Yetkazuvchilar hisoboti - ' . $dateInfo];
        $data[] = [''];

        // Har bir tuman uchun alohida jadval
        foreach ($this->reportData as $regionName => $regionData) {
            // Tuman nomi
            $data[] = [$regionName . ' tumani'];
            $data[] = [''];

            $kindgardensOrdered = collect($regionData['kindgardens'])->sortBy('number_org');
            $maxColumnsPerTable = 12; // Maksimal ustunlar soni bitta jadvalda
            $kindgardenChunks = $kindgardensOrdered->chunk($maxColumnsPerTable);
            $totalChunks = $kindgardenChunks->count();

            foreach ($kindgardenChunks as $chunkIndex => $kindgardenChunk) {
                $isLastChunk = ($chunkIndex == $totalChunks - 1);

                // Agar davomi bo'lsa, sarlavha qo'shamiz
                if ($chunkIndex > 0) {
                    $data[] = ['Davomi (' . ($chunkIndex + 1) . '-qism):'];
                }

                // Jadval header qurish
                $headerRow = ['Maxsulot nomi', 'O\'lchov birligi'];

                foreach ($kindgardenChunk as $kindgardenId => $kindgarden) {
                    $headerRow[] = $kindgarden['number_org'];
                }

                // Faqat oxirgi chunkda Jami ustunini qo'shamiz
                if ($isLastChunk) {
                    $headerRow[] = 'Jami';
                }

                $data[] = $headerRow;

                // Maxsulotlar qatorlarini qurish
                foreach ($regionData['products'] as $productId => $product) {
                    $row = [
                        $product['name'],
                        $product['size']
                    ];

                    // Har bir bog'cha uchun miqdorni qo'shish (faqat joriy chunk)
                    foreach ($kindgardenChunk as $kindgardenId => $kindgarden) {
                        $weight = $product['kindgardens'][$kindgardenId] ?? 0;
                        $row[] = $weight > 0 ? round($weight, 2) : '-';
                    }

                    // Faqat oxirgi chunkda umumiy jamini qo'shamiz
                    if ($isLastChunk) {
                        $grandTotal = 0;
                        foreach ($kindgardensOrdered as $kId => $kg) {
                            $grandTotal += $product['kindgardens'][$kId] ?? 0;
                        }
                        $row[] = round($grandTotal, 2);
                    }

                    $data[] = $row;
                }

                // Bo'sh qator (chunklar orasida)
                $data[] = [''];
            }

            // Bo'sh qator (tumanlararasida ajratish uchun)
            $data[] = [''];
        }

        return $data;
    }

    private function getDateInfo()
    {
        if ($this->dateType === 'daily' && $this->days->count() === 1) {
            $day = $this->days->first();
            return $day->day_number . '.' . $day->month->month_name . '.' . $day->year->year_name;
        } elseif ($this->dateType === 'monthly') {
            $day = $this->days->first();
            return $day->month->month_name . ' ' . $day->year->year_name;
        } elseif ($this->dateType === 'range' && $this->days->count() > 0) {
            $firstDay = $this->days->first();
            $lastDay = $this->days->last();
            return $firstDay->day_number . '.' . $firstDay->month->month_name . ' - ' .
                   $lastDay->day_number . '.' . $lastDay->month->month_name . '.' . $lastDay->year->year_name;
        }

        return 'Noma\'lum sana';
    }

    private function getColumnLetter($columnNumber) {
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
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Main header style (1-qator)
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Har bir jadval headeriga style qo'llash
                $currentRow = 1;
                while ($currentRow <= $highestRow) {
                    $cellValue = $sheet->getCell('A' . $currentRow)->getValue();

                    // Tuman nomi header
                    if (is_string($cellValue) && strpos($cellValue, 'tumani') !== false) {
                        $sheet->mergeCells('A' . $currentRow . ':' . $highestColumn . $currentRow);
                        $sheet->getStyle('A' . $currentRow)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 12,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E0E0E0'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                            ],
                        ]);
                    }

                    // "Davomi" sarlavhasini formatlash
                    if (is_string($cellValue) && strpos($cellValue, 'Davomi') !== false) {
                        $sheet->mergeCells('A' . $currentRow . ':' . $highestColumn . $currentRow);
                        $sheet->getStyle('A' . $currentRow)->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11,
                                'color' => ['rgb' => '4a5568'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                            ],
                        ]);
                    }

                    // Jadval header (Maxsulot nomi, O'lchov birligi...)
                    if (is_string($cellValue) && $cellValue === 'Maxsulot nomi') {
                        $tableHeaderRow = $currentRow;
                        $sheet->getStyle('A' . $tableHeaderRow . ':' . $highestColumn . $tableHeaderRow)
                              ->applyFromArray([
                                  'font' => ['bold' => true],
                                  'fill' => [
                                      'fillType' => Fill::FILL_SOLID,
                                      'startColor' => ['rgb' => 'F0F0F0'],
                                  ],
                                  'borders' => [
                                      'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                                  ],
                                  'alignment' => [
                                      'horizontal' => Alignment::HORIZONTAL_CENTER,
                                      'vertical' => Alignment::VERTICAL_CENTER,
                                  ],
                              ]);

                        // Ma'lumotlar qismiga border qo'llash
                        $dataStartRow = $tableHeaderRow + 1;
                        $dataEndRow = $dataStartRow;

                        // Ma'lumotlar oxirini topish
                        while ($dataEndRow <= $highestRow &&
                               !empty($sheet->getCell('A' . $dataEndRow)->getValue()) &&
                               $sheet->getCell('A' . $dataEndRow)->getValue() !== 'Maxsulot nomi' &&
                               strpos($sheet->getCell('A' . $dataEndRow)->getValue(), 'tumani') === false &&
                               strpos($sheet->getCell('A' . $dataEndRow)->getValue(), 'Davomi') === false) {
                            $dataEndRow++;
                        }
                        $dataEndRow--;

                        if ($dataStartRow <= $dataEndRow) {
                            $sheet->getStyle('A' . $dataStartRow . ':' . $highestColumn . $dataEndRow)
                                  ->applyFromArray([
                                      'borders' => [
                                          'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                                      ],
                                  ]);

                            // Number format for numeric columns
                            $sheet->getStyle('C' . $dataStartRow . ':' . $highestColumn . $dataEndRow)
                                  ->getNumberFormat()->setFormatCode('#,##0.00');

                            // Jami ustuniga bold style (oxirgi ustun)
                            $lastColValue = $sheet->getCell($highestColumn . $tableHeaderRow)->getValue();
                            if ($lastColValue === 'Jami') {
                                $sheet->getStyle($highestColumn . $dataStartRow . ':' . $highestColumn . $dataEndRow)
                                      ->getFont()->setBold(true);
                            }
                        }
                    }

                    $currentRow++;
                }

                // Set page orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                // Auto height for rows
                foreach (range(1, $highestRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,  // Maxsulot nomi
            'B' => 15,  // O'lchov birligi
        ];
    }
}
