<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Protsent;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RegionSchotFakturaExport implements FromView, WithStyles, WithColumnWidths
{
    protected $id, $start, $end;
    
    public function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
    }

    public function view(): View
    {
        $kindgardens = Kindgarden::where('region_id', $this->id)->get();

        $region = Region::where('id', $this->id)->first();
        
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
                ->join('years', 'days.year_id', '=', 'years.id')
                ->join('months', 'days.month_id', '=', 'months.id')
                ->get(['days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name', 'days.created_at']);
        
        $costs = [];
        $number_childrens = [];
        $ages = Age_range::all();
        
        foreach($ages as $age){
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
        
        if(!$ustama_settings){
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
            'company_name' => $region->region_name.' ММТБ' ?? '',
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

        if(is_null(env('CONTRACT_DATA'))){
            $contract_data = " ______ '______' ___________ 2025 й";
        }else{
            $contract_data = " 25111006438231       16.07.2025 й";
        }
        
        // Hisob-faktura raqami va sanasi
        if(is_null(env('INVOICE_NUMBER'))){
            $invoice_number = $days->last()->month_id.'-'. $this->id;
        }else{
            $invoice_number = $days->last()->month_id.'/'.env('INVOICE_NUMBER');
        }
        $invoice_date = $days->last()->created_at->format('d.m.Y');
        
        return view('pdffile.accountant.regionschotfakturaexcel', compact('contract_data', 'costs', 'ages', 'days', 'kindgardens', 'autorser', 'buyurtmachi', 'invoice_number', 'invoice_date', 'number_childrens', 'region', 'ustama_settings'));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header bo'yicha style
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Ma'lumotlar uchun border
            'A:I' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Header qatorlari uchun background
            '7:8' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'F0F0F0',
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
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