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

class SchotFakturaSecondExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
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
        $kindgar = Kindgarden::where('id', $this->id)->with('age_range')->first();
        $region = Region::where('id', $kindgar->region_id)->first();
        
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
                ->join('years', 'days.year_id', '=', 'years.id')
                ->join('months', 'days.month_id', '=', 'months.id')
                ->get(['days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name', 'days.created_at']);
        
        $costs = [];
        $total_number_children = [];
        foreach($kindgar->age_range as $age){
            $costs[$age->id] = Protsent::where('region_id', $kindgar->region_id)
                        ->where('age_range_id', $age->id)
                        ->where('end_date', '>=', $days->last()->created_at->format('Y-m-d'))
                        ->first();
            $total_number_children[$age->id] = Number_children::where('day_id', '>=', $this->start)
                    ->where('day_id', '<=', $this->end)
                    ->where('kingar_name_id', $this->id)
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
        }
        
        // Autsorser ma'lumotlari
        $autorser = config('company.autorser');
        
        // Buyurtmachi ma'lumotlari
        $buyurtmachi = [
            'company_name' => $region->region_name.' ММТБга тасарруфидаги '.$kindgar->number_of_org .'-сонли ДМТТ' ?? '',
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

        $contract_data = is_null(env('CONTRACT_DATA')) ? " ______ '______' ___________ 2025 й" : " 25111006438231       16.07.2025 й";
        $invoice_number = is_null(env('INVOICE_NUMBER')) ? $days->last()->month_id.'-'. $kindgar->number_of_org : $days->last()->month_id.'/'.env('INVOICE_NUMBER');
        $invoice_date = $days->last()->created_at->format('d.m.Y');

        $data = [];
        
        // Header qismi
        $data[] = ['СЧЁТ-ФАКТУРА', '', '', '', '', '', '', '', ''];
        $data[] = ['№ ' . $invoice_number, '', '', '', '', '', '', '', ''];
        $data[] = [$invoice_date . "  й", '', '', '', '', '', '', '', ''];
        $data[] = ["Хизмат кўрсатиш шартномаси: № " . $contract_data, '', '', '', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator
        
        // Kompaniya ma'lumotlari
        $data[] = ['Аутсорсер:', '', '', '', 'Буюртмачи:', '', '', '', ''];
        $data[] = ['Ташкилот: ' . ($autorser['company_name'] ?? 'IOS-Service MCHJ'), '', '', '', 'Ташкилот: ' . $buyurtmachi['company_name'], '', '', '', ''];
        $data[] = ['Манзил: ' . ($autorser['address'] ?? 'Toshkent shahri, Olmazor tumani, 1'), '', '', '', 'Манзил: ' . $buyurtmachi['address'], '', '', '', ''];
        $data[] = ['ИНН: ' . ($autorser['inn'] ?? '123456789'), '', '', '', 'ИНН: ' . $buyurtmachi['inn'], '', '', '', ''];
        $data[] = ['МФО: ' . ($autorser['mfo'] ?? '12345'), '', '', '', 'МФО: ' . $buyurtmachi['mfo'], '', '', '', ''];
        $data[] = ['Хисоб рақам: ' . ($autorser['bank_account'] ?? '1234567890123456'), '', '', '', 'Х/р: ' . $buyurtmachi['bank_account'], '', '', '', ''];
        $data[] = ['Банк: ' . ($autorser['bank'] ?? 'Асака банк'), '', '', '', 'Ягона ғ.х/р: ' . $buyurtmachi['account_number'], '', '', '', ''];
        $data[] = ['Телефон: ' . ($autorser['phone'] ?? '+998901234567'), '', '', '', 'Банк: ' . $buyurtmachi['bank'], '', '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator
        
        // Jadval header
        $data[] = ['№', 'Иш, хизмат номи', 'Ўл.бир', 'Сони', 'Нархи', 'Етказиб бериш нархи', 'ҚҚС ва устама', '', 'Кўрсатилган хизмат суммаси (ҚҚС билан)'];
        $data[] = ['', '', '', '', '', '', '%', 'Сумма', ''];
        
        // Ma'lumotlar qatorlari
        $currentDataRow = 17; // Jadval ma'lumotlari 17-qatordan boshlanadi
        $tr = 1;
        $total_base_amount = 0; // Asosiy summa uchun
        
        foreach($kindgar->age_range as $age) {
            if(isset($costs[$age->id]) && $costs[$age->id] && isset($total_number_children[$age->id]) && $total_number_children[$age->id] > 0) {
                $base_amount = ($total_number_children[$age->id] ?? 0) * ($costs[$age->id]->eater_cost ?? 0);
                $total_base_amount += $base_amount;
                
                $data[] = [
                    $tr++,
                    $kindgar->number_of_org . '-ДМТТ ' . $age->description . ' ёшли гуруҳ тарбияланувчилари учун кўрсатилган ' . $days[0]->year_name . ' йил ' . $days[0]->day_number . '-' . $days[count($days)-1]->day_number . ' ' . $days[0]->month_name . ' даги Аутсорсинг хизмати',
                    'хизмат',
                    1,
                    // Нархи - formula bilan
                    '=' . ($total_number_children[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0) . '/(1+' . ($costs[$age->id]->nds ?? 0) . '/100)',
                    // Етказиб бериш нархи - E ustuniga havola
                    '=E' . $currentDataRow,
                    ($costs[$age->id]->nds ?? 0) . '%',
                    // NDS summa - formula
                    '=' . ($total_number_children[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0) . '*' . ($costs[$age->id]->nds ?? 0) . '/(100+' . ($costs[$age->id]->nds ?? 0) . ')',
                    // Jami summa - formula
                    '=' . ($total_number_children[$age->id] ?? 0) . '*' . ($costs[$age->id]->eater_cost ?? 0)
                ];
                $currentDataRow++;
            }
        }
        
        // Аутсорсинг хизмати устамаси qatori qo'shish
        if($total_base_amount > 0) {
            // Ustama protsentini topish (birinchi yosh guruhi uchun)
            $first_age = $kindgar->age_range->first();
            $raise_percent = $costs[$first_age->id]->raise ?? 28.5;
            $raise_amount = $total_base_amount * ($raise_percent / 100);
            
            $data[] = [
                $tr++,
                'Аутсорсинг хизмати устамаси',
                'Хизмат',
                1,
                '', // Нархи bo'sh
                '', // Етказиб бериш нархи bo'sh
                $raise_percent . '%',
                '=' . $raise_amount, // Ustama summasi
                '=' . $raise_amount // Jami summa
            ];
            $currentDataRow++;
        }
        
        // Jami qator
        $data[] = [
            '',
            'Жами сумма:',
            '',
            '',
            '',
            // Jami narx
            '=SUM(F17:F' . ($currentDataRow-1) . ')',
            '',
            // Jami NDS
            '=SUM(H17:H' . ($currentDataRow-1) . ')',
            // Jami xizmat summasi
            '=SUM(I17:I' . ($currentDataRow-1) . ')'
        ];
        
        $data[] = ['', '', '', '', '', '', '', '', '']; // Bo'sh qator
        
        // Footer
        $data[] = ['Аутсорсер директори: ____________________________', '', '', '', 'Бўлими директори: ____________________________', '', '', '', ''];
        $data[] = ['Бош хисобчиси: ____________________________', '', '', '', 'Бош хисобчиси: ____________________________', '', '', '', ''];
        
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
                $sheet->mergeCells('A1:I1'); // СЧЁТ-ФАКТУРА
                $sheet->mergeCells('A2:I2'); // Invoice number
                $sheet->mergeCells('A3:I3'); // Invoice date
                $sheet->mergeCells('A4:I4'); // Contract data
                
                $sheet->getStyle('A1:I4')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
                $sheet->getStyle('A2:A4')->getFont()->setSize(14);
                
                // Company info merge
                $sheet->mergeCells('A6:D6'); // Аутсорсер
                $sheet->mergeCells('E6:I6'); // Буюртмачи
                for($row = 7; $row <= 13; $row++) {
                    $sheet->mergeCells('A'.$row.':D'.$row);
                    $sheet->mergeCells('E'.$row.':I'.$row);
                }
                
                // Table header merge
                $sheet->mergeCells('A15:A16'); // №
                $sheet->mergeCells('B15:B16'); // Иш, хизмат номи
                $sheet->mergeCells('C15:C16'); // Ўл.бир
                $sheet->mergeCells('D15:D16'); // Сони
                $sheet->mergeCells('E15:E16'); // Нархи
                $sheet->mergeCells('F15:F16'); // Етказиб бериш нархи
                $sheet->mergeCells('G15:H15'); // ҚҚС ва устама
                $sheet->mergeCells('I15:I16'); // Кўрсатилган хизмат суммаси
                
                // Jadval header style
                $sheet->getStyle('A15:I16')->applyFromArray([
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
                
                // Ma'lumotlar qismi border (footer ni chiqarib tashlash)
                $dataRange = 'A17:I' . ($highestRow - 3);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Jami qatori merge va style
                $sheet->mergeCells('B'.($highestRow-3).':E'.($highestRow-3)); // "Жами сумма:" span
                $sheet->getStyle('B'.($highestRow-3).':I'.($highestRow-3))
                      ->getFont()->setBold(true);
                $sheet->getStyle('F'.($highestRow-3).':I'.($highestRow-3))
                      ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Footer merge
                $sheet->mergeCells('A'.($highestRow-1).':D'.($highestRow-1));
                $sheet->mergeCells('E'.($highestRow-1).':I'.($highestRow-1));
                $sheet->mergeCells('A'.$highestRow.':D'.$highestRow);
                $sheet->mergeCells('E'.$highestRow.':I'.$highestRow);
                
                // Number format
                $sheet->getStyle('E17:I'.($highestRow-3))
                      ->getNumberFormat()->setFormatCode('#,##0.00');
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 50,  // Иш, хизмат номи
            'C' => 8,   // Ўл.бир
            'D' => 8,   // Сони
            'E' => 15,  // Нархи
            'F' => 15,  // Етказиб бериш нархи
            'G' => 8,   // %
            'H' => 15,  // Сумма
            'I' => 20,  // Кўрсатилган хизмат суммаси
        ];
    }
} 