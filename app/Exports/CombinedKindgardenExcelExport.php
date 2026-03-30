<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CombinedKindgardenExcelExport implements WithMultipleSheets
{
    public function __construct(
        private $id,
        private $start,
        private $end,
        private $costid
    ) {}

    public function sheets(): array
    {
        return [
            new CombinedSchotFakturaSheet($this->id, $this->start, $this->end),
            new CombinedDalolatnomaSheet($this->id, $this->start, $this->end),
            new CombinedTransportationSheet($this->id, $this->start, $this->end, $this->costid),
            new CombinedNakapitSheet($this->id, $this->start, $this->end, $this->costid),
            new CombinedContractSheet($this->id, $this->start, $this->end),
        ];
    }
}

// ── Sheet 1: Счёт-фактура ────────────────────────────────────────────────────
class CombinedSchotFakturaSheet extends SchotFakturaSecondExport implements WithTitle
{
    public function title(): string
    {
        return 'Счёт-фактура';
    }
}

// ── Sheet 2: Далолатнома ─────────────────────────────────────────────────────
class CombinedDalolatnomaSheet extends DalolatnomaExport implements WithTitle
{
    public function title(): string
    {
        return 'Далолатнома';
    }
}

// ── Sheet 3: Қатнов ──────────────────────────────────────────────────────────
class CombinedTransportationSheet extends TransportationExcelExport implements WithTitle
{
    public function title(): string
    {
        return 'Қатнов';
    }
}

// ── Sheet 4: Маҳсулот сарфи ──────────────────────────────────────────────────
class CombinedNakapitSheet extends SpendedkgExport implements WithTitle
{
    public function title(): string
    {
        return 'Маҳсулот сарфи';
    }
}

// ── Sheet 5: Шартнома ────────────────────────────────────────────────────────
class CombinedContractSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private $kindgar;
    private $region;
    private $contracts;

    public function __construct(private $id, private $start, private $end)
    {
        $this->kindgar = Kindgarden::where('id', $id)->first();
        $this->region  = Region::where('id', $this->kindgar->region_id)->first();
        $this->contracts = Contract::getForKindgarden($id, $this->kindgar->region_id);
    }

    public function title(): string
    {
        return 'Шартнома';
    }

    public function array(): array
    {
        $data = [];
        $data[] = ['Шартномалар рўйхати'];
        $data[] = [$this->kindgar->kingar_name . ' учун'];
        $data[] = [''];
        $data[] = ['№', 'Шартнома рақами', 'Шартнома санаси', 'Бошланиш санаси', 'Тугаш санаси', 'Вилоят'];

        $i = 1;
        foreach ($this->contracts as $contract) {
            $data[] = [
                $i++,
                $contract->contract_number,
                $contract->contract_date ? $contract->contract_date->format('d.m.Y') : '',
                $contract->start_date    ? $contract->start_date->format('d.m.Y')    : '',
                $contract->end_date      ? $contract->end_date->format('d.m.Y')      : '',
                $this->region->region_name,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 22,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A4:F4')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                    'font'      => ['bold' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                if ($highestRow >= 5) {
                    $sheet->getStyle('A5:F' . $highestRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $sheet->getStyle('A5:A' . $highestRow)
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B5:F' . $highestRow)
                          ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }
            },
        ];
    }
}
