<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use App\Models\Protsent;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransportationSecondaryExcelExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $id, $start, $end, $costid;
    
    public function __construct($id, $start, $end, $costid)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
    }

    public function array(): array
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $days = Day::where('days.id', '>=', $this->start)->where('days.id', '<=', $this->end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $kindgar->region_id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $this->id)
                    ->where('king_age_name_id', $age->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
            }
        }

        $data = [];
        
        // Header qismi - PDF bilan bir xil
        $data[] = [$kindgar->kingar_name . ' да ' . $days[0]->day_number . '-' . $days[count($days)-1]->day_number . ' ' . $days[0]->month_name . ' ' . $days[0]->year_name . ' йил кунлари болалар катнови ва аутсорсинг хизмати харажатлари тўғрисида маълумот'];
        $data[] = [''];
        
        // Jadval header - PDF bilan bir xil struktura (13 ustun)
        $header1 = ['№', 'Таомнома', 'Сана', 'Буюртма бўйича бола сони', '', '', 'Бир нафар болага сарфланган харажат НДС билан', '', 'Жами етказиб бериш харажат НДС билан', '', '', 'Устама ҳақ', 'Жами етказиб бериш суммаси (НДС билан)'];
        $data[] = $header1;
        
        // Sub header - PDF bilan bir xil
        $raise = $costs->where('age_range_id', 4)->first()->raise ?? 28.5;
        $nds = $costs->where('age_range_id', 4)->first()->nds ?? 12;
        
        $header2 = ['', '', '', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', '9-10,5 соатлик гуруҳ', '4 соатлик гуруҳ', 'Жами', $raise . '%', ''];
        $data[] = $header2;
        
        // Ma'lumotlar qatorlari
        $row_number = 1;
        $currentDataRow = 5;
        
        // Jami hisoblash uchun o'zgaruvchilar
        $total_children_9_10 = 0;
        $total_children_4 = 0;
        $total_children_all = 0;
        $total_cost_9_10 = 0;
        $total_cost_4 = 0;
        $total_delivery_9_10 = 0;
        $total_delivery_4 = 0;
        $total_delivery_all = 0;
        $total_markup = 0;
        $total_final_amount = 0;
        
        foreach($days as $day) {
            // Bolalar sonini hisoblash
            $children_9_10 = 0;
            $children_4 = 0;
            $menu_name = '';
            
            foreach($number_childrens[$day->id] as $age_id => $child) {
                if($age_id == 4) { // 9-10.5 soatlik guruh
                    $menu_name = $child->menu_name ?? '';
                    $children_9_10 += $child->kingar_children_number ?? 0;
                } elseif($age_id == 3) { // 4 soatlik guruh
                    $children_4 += $child->kingar_children_number ?? 0;
                }
            }
            
            $eater_cost9_10 = $costs->where('age_range_id', 4)->first()->eater_cost ?? 0;
            $eater_cost4 = $costs->where('age_range_id', 3)->first()->eater_cost ?? 0;
            
            // Hisoblashlar
            $children_all = $children_9_10 + $children_4;
            $delivery_9_10 = $children_9_10 * $eater_cost9_10;
            $delivery_4 = $children_4 * $eater_cost4;
            $delivery_all = $delivery_9_10 + $delivery_4;
            
            // Ustama hisoblash (Secondary da faqat ustama ko'rsatiladi)
            $markup = $delivery_all * ($raise / 100);
            $final_amount = $delivery_all + $markup;
            
            // Jami hisoblash
            $total_children_9_10 += $children_9_10;
            $total_children_4 += $children_4;
            $total_children_all += $children_all;
            $total_cost_9_10 += $eater_cost9_10;
            $total_cost_4 += $eater_cost4;
            $total_delivery_9_10 += $delivery_9_10;
            $total_delivery_4 += $delivery_4;
            $total_delivery_all += $delivery_all;
            $total_markup += $markup;
            $total_final_amount += $final_amount;
            
            $row = [
                $row_number++,
                $menu_name,
                $day->day_number . '/' . $day->month_name . '/' . $day->year_name,
                $children_9_10,
                $children_4,
                $children_all,
                $eater_cost9_10,
                $eater_cost4,
                $delivery_9_10,
                $delivery_4,
                $delivery_all,
                $markup,
                $final_amount
            ];
            
            $data[] = $row;
            $currentDataRow++;
        }
        
        // Jami qatori - PDF bilan bir xil
        $data[] = [
            '',
            '',
            'ЖАМИ',
            $total_children_9_10,
            $total_children_4,
            $total_children_all,
            $total_cost_9_10,
            $total_cost_4,
            $total_delivery_9_10,
            $total_delivery_4,
            $total_delivery_all,
            $total_markup,
            $total_final_amount
        ];
        
        // Imzo qismi
        $data[] = [''];
        $data[] = ['Аутсорсер директори: ____________________________', '', '', '', '', '', '', '', '', '', '', '', ''];
        $data[] = ['Буюртмачи директори: ____________________________', '', '', '', '', '', '', '', '', '', '', '', ''];
        
        return $data;
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
                
                // Header merge va style
                $sheet->mergeCells('A1:M1');
                $sheet->getStyle('A1')->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
                
                // Jadval header merge cells - PDF bilan bir xil (13 ustun)
                $sheet->mergeCells('A3:A4'); // №
                $sheet->mergeCells('B3:B4'); // Таомнома
                $sheet->mergeCells('C3:C4'); // Сана
                $sheet->mergeCells('D3:F3'); // Буюртма бўйича бола сони
                $sheet->mergeCells('G3:H3'); // Бир нафар болага сарфланган харажат НДС билан
                $sheet->mergeCells('I3:K3'); // Жами етказиб бериш харажат НДС билан
                $sheet->mergeCells('L3:L4'); // Устама ҳақ
                $sheet->mergeCells('M3:M4'); // Жами етказиб бериш суммаси (НДС билан)
                
                // Header style
                $sheet->getStyle('A3:M4')->applyFromArray([
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
                
                // Ma'lumotlar border
                $dataStartRow = 5;
                $dataEndRow = $highestRow - 3; // Imzo qatorlaridan oldin
                $dataRange = 'A' . $dataStartRow . ':M' . $dataEndRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                
                // Jami qatori style
                $totalRow = $dataEndRow;
                $sheet->getStyle('A' . $totalRow . ':M' . $totalRow)
                      ->getFont()->setBold(true);
                $sheet->getStyle('A' . $totalRow . ':M' . $totalRow)
                      ->applyFromArray([
                          'fill' => [
                              'fillType' => Fill::FILL_SOLID,
                              'startColor' => ['rgb' => 'D0D0D0'],
                          ],
                          'borders' => [
                              'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                          ],
                      ]);
                
                // Number format - raqamli ustunlar uchun
                $sheet->getStyle('D' . $dataStartRow . ':M' . $dataEndRow)
                      ->getNumberFormat()->setFormatCode('#,##0.00');
                
                // Imzo qatorlari style
                $signatureRow1 = $highestRow - 2;
                $signatureRow2 = $highestRow - 1;
                $sheet->getStyle('A' . $signatureRow1 . ':A' . $signatureRow1)
                      ->getFont()->setBold(true);
                $sheet->getStyle('A' . $signatureRow2 . ':A' . $signatureRow2)
                      ->getFont()->setBold(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // №
            'B' => 12,  // Таомнома
            'C' => 15,  // Сана
            'D' => 12,  // 9-10.5 соатлик бола сони
            'E' => 12,  // 4 соатлик бола сони
            'F' => 10,  // Жами
            'G' => 12,  // 9-10.5 соатлик нарх
            'H' => 12,  // 4 соатлик нарх
            'I' => 15,  // 9-10.5 delivery
            'J' => 15,  // 4 delivery
            'K' => 15,  // Жами delivery
            'L' => 15,  // Устама ҳақ
            'M' => 20,  // Жами етказиб бериш суммаси (НДС билан)
        ];
    }
}
