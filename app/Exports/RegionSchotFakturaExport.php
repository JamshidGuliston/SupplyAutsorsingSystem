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

class RegionSchotFakturaExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
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
            ->join('years', 'days.year_id', '=', 'years.id')
            ->join('months', 'days.month_id', '=', 'months.id')
            ->get(['days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name', 'days.created_at']);

        $costs = [];
        $number_childrens = [];
        $ages = Age_range::all();

        foreach ($ages as $age) {
            $costs[$age->id] = Protsent::where('region_id', $this->id)
                ->where('age_range_id', $age->id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days->last()->created_at->format('Y-m-d'))
                ->first();
            $number_childrens[$age->id] = Number_children::where('number_childrens.day_id', '>=', $this->start)
                ->where('number_childrens.day_id', '<=', $this->end)
                ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                ->where('king_age_name_id', $age->id)
                ->sum('kingar_children_number');
        }

        // Ustama va NDS uchun default qiymatlar (agar specific age_range topilmasa)
        $ustama_settings = Protsent::where('region_id', $this->id)
            ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
            ->where('end_date', '>=', $days->last()->created_at->format('Y-m-d'))
            ->first();

        if (!$ustama_settings) {
            // Agar topilmasa, default qiymatlar
            $ustama_settings = (object)[
                'raise' => 0,
                'nds' => 0
            ];
        }

        // Autsorser ma'lumotlari (kompaniya ma'lumotlari)
        $autorser = config('company.autorser');

        // Buyurtmachi ma'lumotlari
        $buyurtmachi = [
            'company_name' => $region->region_name . ' ММТБ' ?? '',
            'address' => $region->region_name,
            'inn' => '________________',
            'bank_account' => '___________________________________',
            'mfo' => '00014',
            'account_number' => '23402000300100001010',
            'treasury_account' => '_______________',
            'treasury_inn' => '________________',
            'bank' => 'Марказий банк ХККМ',
            'phone' => '__________________________',
        ];

        $contract_env = env('CONTRACT_DATA');

        $contract_data = $contract_env ? explode(',', $contract_env)[$region->id - 1] ?? " ______ '______' ___________ 2025 й"
            : " ______ '______' ___________ 2025 й";


        // Hisob-faktura raqami va sanasi
        if (is_null(env('INVOICE_NUMBER'))) {
            $invoice_number = $days->last()->month_id . '-' . $this->id;
        }
        else {
            $invoice_number = $days->last()->month_id . '/' . env('INVOICE_NUMBER');
        }
        $invoice_date = $days->last()->created_at->format('d.m.Y');

        $data = [];

        // Header qismi
        $data[] = ['СЧЁТ-ФАКТУРА', '', '', '', '', '', '', '', ''];
        $data[] = ['№ ' . ($invoice_number ?? "_________________________________"), '', '', '', '', '', '', '', ''];
        $data[] = [($invoice_date . "  й") ?? "_________________________________", '', '', '', '', '', '', '', ''];
        $data[] = ["Хизмат кўрсатиш шартномаси: № " . $contract_data, '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator

        // Kompaniya ma'lumotlari
        $data[] = ['Аутсорсер:', '', '', '', 'Буюртмачи:', '', '', '', ''];
        $data[] = ['Ташкилот: ' . ($autorser['company_name'] ?? 'IOS-Service MCHJ'), '', '', '', 'Ташкилот: ' . ($buyurtmachi['company_name'] ?? '_________________________________'), '', '', '', ''];
        $data[] = ['Манзил: ' . ($autorser['address'] ?? 'Toshkent shahri, Olmazor tumani, 1'), '', '', '', 'Манзил: ' . ($buyurtmachi['address'] ?? ''), '', '', '', ''];
        $data[] = ['ИНН: ' . ($autorser['inn'] ?? '123456789'), '', '', '', 'ИНН: ' . ($buyurtmachi['inn'] ?? ''), '', '', '', ''];
        $data[] = ['МФО: ' . ($autorser['mfo'] ?? '12345'), '', '', '', 'МФО: ' . ($buyurtmachi['mfo'] ?? ''), '', '', '', ''];
        $data[] = ['Хисоб рақам: ' . ($autorser['bank_account'] ?? '1234567890123456'), '', '', '', 'Х/р: ' . ($buyurtmachi['bank_account'] ?? ''), '', '', '', ''];
        $data[] = ['Банк: ' . ($autorser['bank'] ?? 'Асака банк'), '', '', '', 'Ягона ғ.х/р: ' . ($buyurtmachi['account_number'] ?? ''), '', '', '', ''];
        $data[] = ['Телефон: ' . ($autorser['phone'] ?? '+998901234567'), '', '', '', 'Банк: ' . ($buyurtmachi['bank'] ?? ""), '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator

        // Jadval header
        $data[] = ['№', 'Иш, хизмат номи', 'Ўл.бир', 'Сони', 'Нархи', 'Етказиб бериш нархи', 'ҚҚС ва устама', '', 'Кўрсатилган хизмат суммаси (ҚҚС билан)'];
        $data[] = ['', '', '', '', '', '', '%', 'Сумма', ''];

        // Ma'lumotlar qatorlari
        $currentRow = 17; // Jadval ma'lumotlari 17-qatordan boshlanadi
        $tr = 1;

        foreach ($ages as $age) {
            if (isset($costs[$age->id]) && $costs[$age->id]) {
                $data[] = [
                    $tr++,
                    $region->region_name . " MMTB " . $age->description . "ли гуруҳ тарбияланувчилари учун кўрсатилган " . $days[0]->year_name . " йил " . $days[0]->day_number . "-" . $days[count($days) - 1]->day_number . " " . $days[0]->month_name . " даги Аутсорсинг хизмати",
                    'хизмат',
                    1,
                    // Нархи - formula bilan
                    '=' . ($number_childrens[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0) . '/(1+' . ($costs[$age->id]->nds ?? 0) . '/100)',
                    // Етказиб бериш нархи - E ustuniga havola
                    '=E' . $currentRow,
                    ($costs[$age->id]->nds ?? 0) . '%',
                    // NDS summa - formula
                    '=' . ($number_childrens[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0) . '*' . ($costs[$age->id]->nds ?? 0) . '/(100+' . ($costs[$age->id]->nds ?? 0) . ')',
                    // Jami summa - formula
                    '=' . ($number_childrens[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0)
                ];
                $currentRow++;
            }
        }

        // Ustama qatori
        $data[] = [
            $tr++,
            'Аутсорсинг хизмати устамаси ' . ($ustama_settings->raise ?? 0) . '%',
            'Хизмат',
            1,
            '',
            // Ustama summasi - oldingi qatorlarning E ustuni yig'indisining foizi
            '=SUM(E17:E' . ($currentRow - 1) . ')*' . ($ustama_settings->raise ?? 0) . '/100',
            ($ustama_settings->nds ?? 0) . '%',
            // Ustama NDS
            '=F' . $currentRow . '*' . ($ustama_settings->nds ?? 0) . '/100',
            // Ustama jami
            '=F' . $currentRow . '+H' . $currentRow
        ];
        $currentRow++;

        // Jami qator
        $data[] = [
            '',
            'Жами сумма:',
            '',
            '',
            '',
            // Jami narx
            '=SUM(F17:F' . ($currentRow - 1) . ')',
            '',
            // Jami NDS
            '=SUM(H17:H' . ($currentRow - 1) . ')',
            // Jami xizmat summasi
            '=SUM(I17:I' . ($currentRow - 1) . ')'
        ];

        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator

        // Footer
        $data[] = ['Аутсорсер директори: ____________________________', '', '', '', 'Бўлими директори: ____________________________', '', '', '', ''];
        $data[] = ['Бош хисобчиси: ____________________________', '', '', '', 'Бош хисобчиси: ____________________________', '', '', '', ''];

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bu qism registerEvents da qilinadi
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Header merge cells va markazga joylashtirish
            $sheet->mergeCells('A1:I1'); // СЧЁТ-ФАКТУРА
            $sheet->mergeCells('A2:I2'); // Invoice number
            $sheet->mergeCells('A3:I3'); // Invoice date
            $sheet->mergeCells('A4:I4'); // Contract data

            // Header qatorlarini markazga joylashtirish
            $sheet->getStyle('A1:I4')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Header shriftini kattalashtirish
            $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
            $sheet->getStyle('A2:A4')->getFont()->setSize(14);

            // Company info merge
            $sheet->mergeCells('A6:D6'); // Аутсорсер
            $sheet->mergeCells('E6:I6'); // Буюртмачи
            for ($row = 7; $row <= 13; $row++) {
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->mergeCells('E' . $row . ':I' . $row);
            }

            // Table header merge (rowspan va colspan uchun)
            $sheet->mergeCells('A15:A16'); // № (rowspan=2)
            $sheet->mergeCells('B15:B16'); // Иш, хизмат номи (rowspan=2)
            $sheet->mergeCells('C15:C16'); // Ўл.бир (rowspan=2)
            $sheet->mergeCells('D15:D16'); // Сони (rowspan=2)
            $sheet->mergeCells('E15:E16'); // Нархи (rowspan=2)
            $sheet->mergeCells('F15:F16'); // Етказиб бериш нархи (rowspan=2)
            $sheet->mergeCells('G15:H15'); // ҚҚС ва устама (colspan=2)
            $sheet->mergeCells('I15:I16'); // Кўрсатилган хизмат суммаси (rowspan=2)

            // Footer merge (oxirgi 2 qator)
            $sheet->mergeCells('A' . ($highestRow - 1) . ':D' . ($highestRow - 1));
            $sheet->mergeCells('E' . ($highestRow - 1) . ':I' . ($highestRow - 1));
            $sheet->mergeCells('A' . $highestRow . ':D' . $highestRow);
            $sheet->mergeCells('E' . $highestRow . ':I' . $highestRow);

            // Jami qator uchun merge (oxiridan 3-chi qator)
            $sheet->mergeCells('B' . ($highestRow - 3) . ':E' . ($highestRow - 3)); // "Жами сумма:" span

            // Jadval qismini aniqlash (15-qatordan boshlab jadval header)
            $tableStartRow = 15;

            // Jadval headeri uchun style (2 ta qator)
            $headerRange = 'A' . $tableStartRow . ':' . $highestColumn . ($tableStartRow + 1);
            $sheet->getStyle($headerRange)->applyFromArray([
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

            // Jadval ma'lumotlari uchun border (faqat jadval, footer emas)
            $dataRange = 'A' . ($tableStartRow + 2) . ':' . $highestColumn . ($highestRow - 3); // Footer ni chiqarib tashlash
            $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

            // Matn alignment
            $sheet->getStyle('B' . ($tableStartRow + 2) . ':B' . $highestRow)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->getStyle('E' . ($tableStartRow + 2) . ':I' . ($highestRow - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Footer qismiga alohida style (border bo'lmasin)
            $sheet->getStyle('A' . ($highestRow - 1) . ':I' . $highestRow)
                ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // Jami qatoriga style
            $sheet->getStyle('B' . ($highestRow - 3) . ':I' . ($highestRow - 3))
                ->getFont()->setBold(true);
            $sheet->getStyle('F' . ($highestRow - 3) . ':I' . ($highestRow - 3))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, // №
            'B' => 50, // Иш, хизмат номи
            'C' => 8, // Ўл.бир
            'D' => 8, // Сони
            'E' => 15, // Нархи
            'F' => 15, // Етказиб бериш нархи
            'G' => 8, // %
            'H' => 15, // Сумма
            'I' => 20, // Кўрсатилган хизмат суммаси
        ];
    }
} 