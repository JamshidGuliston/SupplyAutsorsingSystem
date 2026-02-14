<?php

namespace App\Exports;

use App\Models\Kindgarden;
use App\Models\Region;
use App\Models\Day;
use App\Models\Protsent;
use App\Models\Number_children;
use App\Models\Age_range;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RegionDalolatnomaExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $id;
    protected $start;
    protected $end;
    protected $kindgardens;
    protected $region;
    protected $days;
    protected $costs;
    protected $total_number_children;
    protected $ages;
    protected $autorser;
    protected $buyurtmachi;
    protected $contract_data;
    protected $invoice_number;
    protected $invoice_date;

    public function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;

        // Ma'lumotlarni yuklash
        $this->loadData();
    }

    protected function loadData()
    {
        $this->kindgardens = Kindgarden::where('region_id', $this->id)->where('hide', 1)->get();
        $this->region = Region::where('id', $this->id)->first();
        $this->ages = Age_range::all();

        $this->days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('years', 'days.year_id', '=', 'years.id')
            ->join('months', 'days.month_id', '=', 'months.id')
            ->get(['days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name', 'days.created_at']);

        $this->costs = [];
        $this->total_number_children = [];

        // Har bir yosh guruhi uchun protsent va bolalar sonini olish
        foreach ($this->ages as $age) {
            $this->costs[$age->id] = Protsent::where('region_id', $this->id)
                ->where('age_range_id', $age->id)
                ->where('start_date', '<=', $this->days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $this->days->last()->created_at->format('Y-m-d'))
                ->first();
            if (!isset($this->total_number_children[$age->id])) {
                $this->total_number_children[$age->id] = 0;
            }
            $this->total_number_children[$age->id] += Number_children::where('number_childrens.day_id', '>=', $this->start)
                ->where('number_childrens.day_id', '<=', $this->end)
                ->whereIn('kingar_name_id', $this->kindgardens->pluck('id')->toArray())
                ->where('king_age_name_id', $age->id)
                ->sum('kingar_children_number');
        }

        // Autsorser ma'lumotlari
        $this->autorser = config('company.autorser');

        // Buyurtmachi ma'lumotlari
        $this->buyurtmachi = [
            'company_name' => $this->region->region_name . ' ММТБ' ?? '',
            'address' => $this->region->region_name,
        ];

        $contract_env = env('CONTRACT_DATA');
        $this->contract_data = $contract_env ? explode(',', $contract_env)[$this->region->id - 1] ?? " ______ '______' ___________ 2025 й"
            : " ______ '______' ___________ 2025 й";

        // Dalolatnoma raqami va sanasi
        if (is_null(env('INVOICE_NUMBER'))) {
            $this->invoice_number = $this->id . '-' . $this->days->last()->month_id;
        }
        else {
            $this->invoice_number = $this->days->last()->month_id . '/' . env('INVOICE_NUMBER');
        }
        $this->invoice_date = $this->days->last()->created_at->format('d.m.Y');
    }

    public function array(): array
    {
        $data = [];

        // Sarlavha qismi
        $data[] = ['', '', ''];
        $data[] = ['', 'Бажарилган ишлар', ''];
        $data[] = ['', 'ДАЛОЛАТНОМАСИ № ' . $this->invoice_number, ''];
        $data[] = [$this->buyurtmachi['address'] ?? 'Олмалик шахар', '', $this->invoice_date . ' йил'];
        $data[] = ['', '', ''];

        // Kirish matni
        $intro_text = 'Бизлар қуйидаги имзо чекувчилар ' . ($this->autorser['company_name'] ?? 'ASIA BEST DISTRIBUTION SERVICE') . ' директори Б.Тажибaев бир томондан ва ' . ($this->buyurtmachi['company_name'] ?? 'Олмалик шахар ММТБ') . ' иккинчи томондан ' . $this->contract_data . 'даги шартнома асосида қуйидаги миқдорда бажарилганлиги ҳақида туздик:';
        $data[] = ['', $intro_text, ''];
        $data[] = ['', '', ''];

        // Jadval sarlavhasi
        $data[] = ['№', 'Иш, хизмат номи', 'Бажарилган ишлар миқдори (ҚҚС билан)'];

        // Jadval ma'lumotlari
        $total_amount = 0;
        $row_number = 1;

        // Har bir yosh guruhi uchun qator qo'shish
        foreach ($this->ages as $age) {
            $amount = $this->total_number_children[$age->id] * ($this->costs[$age->id]->eater_cost ?? 0);
            $total_amount += $amount;

            $work_description = $this->buyurtmachi['address'] . ' ММТБга тасарруфидаги барча ДМТТ ' . ($age->description ?? '9-10,5 соатлик') . ' гуруҳ тарбияланувчилари учун ' . $this->days->first()->year_name . ' йил ' . $this->days->first()->day_number . '-' . $this->days->last()->day_number . ' ' . $this->days->first()->month_name . 'да аутсорсинг асосида кунига уч маҳал овқатланишни ташкил этиш бўйича:';

            $data[] = [
                $row_number++,
                $work_description,
                number_format($amount, 2, '.', ' ')
            ];
        }

        // Autsorsing xizmati qatori
        $outsourcing_amount = $total_amount * (($this->costs[$this->ages->first()->id]->raise ?? 28.5) / 100);
        $total_amount += $outsourcing_amount;

        $data[] = [
            $row_number++,
            'Аутсорсинг хизмати (' . ($this->costs[$this->ages->first()->id]->raise ?? '28,5') . '%)',
            number_format($outsourcing_amount, 2, '.', ' ')
        ];

        // Jami qatori
        $data[] = [
            '',
            'ЖАМИ',
            number_format($total_amount, 2, '.', ' ')
        ];

        $data[] = ['', '', ''];

        // Xulosa matni
        $summary_text = 'Бажарилган ишлар учун тўлов миқдори барча устама хак ва соликларни хисобга олган холда ҚҚС билан ' . number_format($total_amount, 2, '.', ' ') . ' сумни ташкил этади.';
        $data[] = ['', $summary_text, ''];
        $data[] = ['', '', ''];
        $data[] = ['', '', ''];

        // Imzo qismi
        $data[] = ['Аутсорсер:', 'Истемолчи:', ''];
        $data[] = [($this->autorser['company_name'] ?? 'ASIA BEST DISTRIBUTION SERVICE'), $this->region->region_name . ' ММТБ', ''];
        $data[] = ['директори: Б.Тажибaев', 'Директори', ''];
        $data[] = ['________________', '________________', ''];

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 80,
            'C' => 25,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Sarlavha qatorlarini
            2 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 18],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Jadval qatorlarini topish
            $tableStartRow = 8; // Jadval sarlavhasi qatori
            $tableEndRow = $tableStartRow + count($this->ages) + 3; // +3 chunki autsorsing va jami qatorlari

            // Jadval uchun border qo'shish
            $tableRange = 'A' . $tableStartRow . ':C' . $tableEndRow;
            $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

            // Jadval sarlavhasi
            $sheet->getStyle('A' . $tableStartRow . ':C' . $tableStartRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                ]);

            // Jadval ma'lumotlari
            for ($i = $tableStartRow + 1; $i <= $tableEndRow; $i++) {
                // A qatorini markazga align
                $sheet->getStyle('A' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // B qatorini chapga align
                $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);
                // C qatorini o'ngga align
                $sheet->getStyle('C' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Jami qatorini bold qilish
            $sheet->getStyle('A' . $tableEndRow . ':C' . $tableEndRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $tableEndRow . ':C' . $tableEndRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F8F9FA');

            // B qatorini keng qilish uchun
            $sheet->getColumnDimension('B')->setWidth(100);

            // Barcha qatorlarni yuqori qilish
            for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
        },
        ];
    }
}
