<?php

namespace App\Exports;

use App\Models\Kindgarden;
use App\Models\Region;
use App\Models\Day;
use App\Models\Number_children;
use App\Models\Protsent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;

class SchotFakturaThirdExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $kindgardenId;
    protected $start;
    protected $end;
    protected $kindgar;
    protected $region;
    protected $days;
    protected $costs;
    protected $total_number_children;
    protected $autorser;
    protected $buyurtmachi;
    protected $invoice_number;
    protected $invoice_date;
    protected $contract_data;

    public function __construct($kindgardenId, $start, $end)
    {
        $this->kindgardenId = $kindgardenId;
        $this->start = $start;
        $this->end = $end;
        $this->prepareData();
    }

    protected function prepareData()
    {
        $this->kindgar = Kindgarden::where('id', $this->kindgardenId)->with('age_range')->first();
        $this->region = Region::where('id', $this->kindgar->region_id)->first();
        
        $this->days = Day::where('days.id', '>=', $this->start)
                ->where('days.id', '<=', $this->end)
                ->join('years', 'days.year_id', '=', 'years.id')
                ->join('months', 'days.month_id', '=', 'months.id')
                ->get(['days.day_number', 'months.id as month_id', 'years.year_name', 'days.created_at']);
        
        $this->costs = [];
        $this->total_number_children = [];
        
        foreach($this->kindgar->age_range as $age){
            $this->costs[$age->id] = Protsent::where('region_id', $this->kindgar->region_id)
                        ->where('age_range_id', $age->id)
                        ->where('end_date', '>=', $this->days->last()->created_at->format('Y-m-d'))
                        ->first();
            
            $this->total_number_children[$age->id] = Number_children::where('day_id', '>=', $this->start)
                ->where('day_id', '<=', $this->end)
                ->where('kingar_name_id', $this->kindgardenId)
                ->where('king_age_name_id', $age->id)
                ->sum('kingar_children_number');
        }
        
        $this->autorser = config('company.autorser');
        
        $this->buyurtmachi = [
            'company_name' => $this->region->region_name.' ММТБга тасарруфидаги '.$this->kindgar->number_of_org .'-сонли ДМТТ',
            'address' => $this->region->region_name,
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
        $this->contract_data = $contract_env ? explode(',', $contract_env)[$this->region->id - 1] ?? " ______ '______' ___________ 2025 й"
            : " ______ '______' ___________ 2025 й";
        
        if(is_null(env('INVOICE_NUMBER'))){
            $this->invoice_number = $this->days->last()->month_id.'-'. $this->kindgar->number_of_org;
        }else{
            $this->invoice_number = $this->days->last()->month_id.'/'.env('INVOICE_NUMBER');
        }
        $this->invoice_date = $this->days->last()->created_at->format('d.m.Y');
    }

    public function collection()
    {
        $data = collect();
        
        // Header qismi
        $data->push(['СЧЁТ-ФАКТУРА']);
        $data->push(['№ ' . $this->invoice_number]);
        $data->push(['фактура санаcи: ' . $this->invoice_date . ' й']);
        $data->push(['Хизмат кўрсатиш шартномаси: ' . $this->contract_data]);
        $data->push([]);
        
        // Kompaniya ma'lumotlari
        $data->push(['Аутсорсер:', '', '', '', 'Буюртмачи:']);
        $data->push(['Ташкилот:', $this->autorser['company_name'] ?? '', '', '', 'Ташкилот:', $this->buyurtmachi['company_name']]);
        $data->push(['Манзил:', $this->autorser['address'] ?? '', '', '', 'Манзил:', $this->buyurtmachi['address']]);
        $data->push(['ИНН:', $this->autorser['inn'] ?? '', '', '', 'ИНН:', $this->buyurtmachi['inn']]);
        $data->push(['МФО:', $this->autorser['mfo'] ?? '', '', '', 'МФО:', $this->buyurtmachi['mfo']]);
        $data->push(['Хисоб рақам:', $this->autorser['bank_account'] ?? '', '', '', 'Х/р:', $this->buyurtmachi['bank_account']]);
        $data->push(['Банк:', $this->autorser['bank'] ?? '', '', '', 'Ягона ғ.х/р:', $this->buyurtmachi['account_number']]);
        $data->push(['Телефон:', $this->autorser['phone'] ?? '', '', '', 'Банк:', $this->buyurtmachi['bank']]);
        $data->push([]);
        
        // Jadval ma'lumotlari
        $tr = 1;
        $sum_base = 0;
        $qqs_base = 0;
        $total_base = 0;
        
        foreach($this->kindgar->age_range as $age){
            // F17 = bolalar soni * eater_cost
            $f17 = $this->total_number_children[$age->id] * ($this->costs[$age->id]->eater_cost ?? 0);
            $sum_base += $f17;
            
            // H17 = F17 * nds/100
            $h17 = $f17 * (($this->costs[$age->id]->nds ?? 0) / 100);
            $qqs_base += $h17;
            
            // I17 = H17 + F17
            $i17 = $h17 + $f17;
            $total_base += $i17;
            
            $data->push([
                $tr++,
                $age->description . 'га кўрсатилган Аутсорсинг хизмати',
                'бола',
                $this->total_number_children[$age->id],
                number_format($f17, 2, '.', ''),
                ($this->costs[$age->id]->nds ?? 0) . '%',
                number_format($h17, 2, '.', ''),
                number_format($i17, 2, '.', '')
            ]);
        }
        
        // Ustama qatori
        // F18 = F17 * raise
        $f18 = $sum_base * (($this->costs[4]->raise ?? 0) / 100);
        
        // H18 = F18 * nds/100
        $h18 = $f18 * (($this->costs[4]->nds ?? 0) / 100);
        
        // I18 = H18 + F18
        $i18 = $h18 + $f18;
        
        $data->push([
            $tr++,
            'Аутсорсинг хизмати устамаси',
            'Хизмат',
            1,
            number_format($f18, 2, '.', ''),
            ($this->costs[4]->nds ?? 0) . '%',
            number_format($h18, 2, '.', ''),
            number_format($i18, 2, '.', '')
        ]);
        
        // Jami qator
        // F19 = SUM(F17:F18)
        $sum_total = $sum_base + $f18;
        
        // H19 = SUM(H17:H18)
        $qqs_total = $qqs_base + $h18;
        
        // I19 = SUM(I17:I18)
        $total_sum = $total_base + $i18;
        
        $data->push([
            '',
            '',
            '',
            'Жами сумма:',
            number_format($sum_total, 2, '.', ''),
            '',
            number_format($qqs_total, 2, '.', ''),
            number_format($total_sum, 2, '.', '')
        ]);
        
        return $data;
    }

    public function headings(): array
    {
        return [
            '№',
            'Маҳсулот, иш, хизматлар номи',
            'Ўл.бир',
            'Сони',
            'Нархи',
            'ҚҚС %',
            'Шундан ҚҚС',
            'Кўрсатилган хизмат суммаси (ҚҚС билан)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header qatorlarini birlashtirish va stillashtirish
        $sheet->mergeCells('A1:H1'); // СЧЁТ-ФАКТУРА
        $sheet->mergeCells('A2:H2'); // № 
        $sheet->mergeCells('A3:H3'); // фактура санаcи
        $sheet->mergeCells('A4:H4'); // Хизмат кўрсатиш шартномаси
        
        // Header stilini qo'llash
        $sheet->getStyle('A1:H4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Kompaniya ma'lumotlarini formatlash
        $sheet->getStyle('A6:H13')->applyFromArray([
            'font' => ['size' => 11],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP
            ]
        ]);
        
        // Jadval headerini topish (15-qator)
        $headerRow = 15;
        $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);
        
        // Jadval ma'lumotlari (16-qatordan boshlab)
        $dataStartRow = 16;
        $lastRow = $dataStartRow + count($this->kindgar->age_range) + 1; // yosh guruhlari + ustama + jami
        
        $sheet->getStyle('A' . $dataStartRow . ':H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Mahsulot nomini chapga tekislash
        $sheet->getStyle('B' . $dataStartRow . ':B' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ]);
        
        // Jami qatorini formatlash
        $sheet->getStyle('A' . $lastRow . ':H' . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);
        
        // Ustun kengliklarini sozlash
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(18);
        
        // Qator balandliklarini sozlash
        $sheet->getRowDimension($headerRow)->setRowHeight(40);
        
        return [];
    }

    public function title(): string
    {
        return 'Счёт-фактура';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Footer qo'shish
                $lastRow = 15 + count($this->kindgar->age_range) + 2; // header + data + jami
                $footerRow = $lastRow + 2;
                
                $sheet->setCellValue('A' . $footerRow, 'Аутсорсер директори: ____________________________');
                $sheet->setCellValue('E' . $footerRow, 'Буюртмачи директори: ____________________________');
                
                $sheet->getStyle('A' . $footerRow . ':H' . $footerRow)->applyFromArray([
                    'font' => ['size' => 11]
                ]);
            }
        ];
    }
}

