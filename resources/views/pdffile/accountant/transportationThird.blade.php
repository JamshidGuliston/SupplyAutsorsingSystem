<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolalar qatnovi va autsorsing xizmati xarajatlari</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 20px;
            margin: 0;
            padding: 15px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            font-size: 20px;
            width: 100%;
            margin-top: {{ count($days) <= 10 ? '80px' : (count($days) <= 20 ? '20px' : '1px') }};
        }
        
        .table-container {
            width: 100%;
            max-width: 100%;
            overflow: visible;
            display: flex;
            justify-content: center;
        }
        
        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 12px;
            margin: 0 auto;
        }
        
        th {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            white-space: wrap;
            min-width: 60px;
        }
        td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            min-width: 60px;
            padding-top: <?php echo count($days) <= 15 ? '10px' : (count($days) <= 20 ? '7px' : '3px') ?>;
            padding-bottom: <?php echo count($days) <= 15 ? '6px' : (count($days) <= 20 ? '7px' : '3px') ?>;
        }
        
        .header-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .sub-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        .data-row {
            background-color: white;
        }
        
        .total-row {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        
        .number-col {
            width: 40px;
            min-width: 40px;
        }
        
        .meal-col {
            width: 80px;
            min-width: 80px;
        }
        
        .date-col {
            width: 100px;
            min-width: 100px;
        }
        
        .children-col {
            width: 90px;
            min-width: 90px;
        }
        
        .cost-col {
            width: 120px;
            min-width: 80px;
        }
        
        .total-cost-col {
            width: 140px;
            min-width: 100px;
        }
        
        .breakdown-col {
            width: 120px;
            min-width: 120px;
        }
        
        .final-total-col {
            width: 150px;
            min-width: 100px;
            font-weight: bold;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            pointer-events: none;
        }
        
        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .footer-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            margin-top: 30px;
            padding-right: 2%;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 20px 0 5px 0;
        }
        
        .signature-label {
            font-size: 16px;
            text-align: center;
        }
        
        
        /* Responsive adjustments for PDF */
        @media print {
            body {
                font-size: 14px;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px 2px;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">Страница</div>
    
    <div class="header">
        {{ $kindgar->kingar_name }} да {{ $days[0]->day_number }}-{{ $days[count($days)-1]->day_number }} {{ $days[0]->month_name }} {{ $days[0]->year_name }} йил кунлари болалар катнови ва аутсорсинг хизмати харажатлари тўғрисида маълумот
    </div>
    
    <div class="table-container">
        <table>
            <!-- Asosiy sarlavha qatorlari -->
            <tr class="header-row">
                <th rowspan="2" class="number-col">№</th>
                <th rowspan="2" class="meal-col">Таомнома</th>
                <th rowspan="2" class="date-col">Сана</th>
                <th colspan="3">Буюртма бўйича бола сони</th>
                <th colspan="2">Бир нафар болага сарфланган харажат НДС билан</th>
                <th colspan="3">Жами етказиб бериш харажат НДС билан</th>
                <th colspan="3">Жами етказиб бериш харажат</th>
                <th rowspan="2" class="final-total-col">Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <tr class="sub-header">
                <th>9-10,5 соатлик гуруҳ</th>
                <th>4 соатлик гуруҳ</th>
                <th>Жами</th>
                <th>9-10,5 соатлик гуруҳ</th>
                <th>4 соатлик гуруҳ</th>
                <th>9-10,5 соатлик гуруҳ</th>
                <th>4 соатлик гуруҳ</th>
                <th>Жами</th>
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ {{$costs->where('age_range_id', 4)->first()->raise ?? 28.5}}%</th>
                <th>ҚҚС (НДС) {{$costs->where('age_range_id', 4)->first()->nds ?? 12}}%</th>
                <!-- <th>Жами сумма</th> -->
            </tr>
            
            <!-- Ma'\''lumot qatorlari -->
            @php
                $total_children_9_10 = 0;
                $total_children_4 = 0;
                $total_children_all = 0;
                $total_cost_9_10 = 0;
                $total_cost_4 = 0;
                $total_delivery_9_10 = 0;
                $total_delivery_4 = 0;
                $total_delivery_all = 0;
                $total_amount_without_nds = 0;
                $total_markup = 0;
                $total_nds = 0;
                $total_final_amount = 0;
                $row_number = 1;
                $menu_name = '';
            @endphp
            
            @foreach($days as $day)
                @php
                    // Bolalar sonini hisoblash
                    $children_9_10 = 0;
                    $children_4 = 0;
                    
                    foreach($number_childrens[$day->id] as $age_id => $child) {
                        if($age_id == 4) { // 9-10.5 soatlik guruh
                            $menu_name = $child->menu_name ?? '';
                            $children_9_10 += $child->kingar_children_number ?? 0;
                        } elseif($age_id == 3) { // 4 soatlik guruh
                            $children_4 += $child->kingar_children_number ?? 0;
                        }
                    }
                    
                    $children_all = $children_9_10 + $children_4;

                    $eater_cost9_10 = $costs->where('age_range_id', 4)->first()->eater_cost ?? 0;
                    $eater_cost4 = $costs->where('age_range_id', 3)->first()->eater_cost ?? 0;
                    $raise = $costs->where('age_range_id', 4)->first()->raise ?? 28.5;
                    $nds = $costs->where('age_range_id', 4)->first()->nds ?? 12;
                    
                    // Narxlarni olish
                    $cost_9_10 = $eater_cost9_10; // 9-10.5 soatlik guruh uchun narx
                    $cost_4 = $eater_cost4; // 4 soatlik guruh uchun narx
                    
                    // Yetkazib berish xarajatlari
                    $delivery_9_10 = $children_9_10 * $cost_9_10;
                    $delivery_4 = $children_4 * $cost_4;
                    $delivery_all = $delivery_9_10 + $delivery_4;
                    
                    // Xarajatlar tahlili (umumiy)
                    $amount_without_nds = $delivery_all / (1 + $nds / 100); // QQSsiz summa
                    $markup = $amount_without_nds * $raise / 100; // Ustama
                    $nds_amount = ($amount_without_nds + $markup) * $nds / 100; // QQS
                    $final_amount = $amount_without_nds + $markup + $nds_amount;
                    
                    // Jami hisoblash
                    $total_children_9_10 += $children_9_10;
                    $total_children_4 += $children_4;
                    $total_children_all += $children_all;
                    $total_cost_9_10 += $cost_9_10;
                    $total_cost_4 += $cost_4;
                    $total_delivery_9_10 += $delivery_9_10;
                    $total_delivery_4 += $delivery_4;
                    $total_delivery_all += $delivery_all;
                    $total_amount_without_nds += $amount_without_nds;
                    $total_markup += $markup;
                    $total_nds += $nds_amount;
                    $total_final_amount += $final_amount;
                @endphp
                <tr class="data-row">
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $menu_name ?? '' }}</td>
                    <td>{{ $day->day_number }}/{{ $day->month_name }}/{{ $day->year_name }}</td>
                    <td>{{ number_format($children_9_10, 0, ',', ' ') }}</td>
                    <td>{{ number_format($children_4, 0, ',', ' ') }}</td>
                    <td>{{ number_format($children_all, 0, ',', ' ') }}</td>
                    <td>{{ number_format($cost_9_10, 2, ',', ' ') }}</td>
                    <td>{{ number_format($cost_4, 2, ',', ' ') }}</td>
                    <td>{{ number_format($delivery_9_10, 2, ',', ' ') }}</td>
                    <td>{{ number_format($delivery_4, 2, ',', ' ') }}</td>
                    <td>{{ number_format($delivery_all, 2, ',', ' ') }}</td>
                    <td>{{ number_format($amount_without_nds, 2, ',', ' ') }}</td>
                    <td>{{ number_format($markup, 2, ',', ' ') }}</td>
                    <td>{{ number_format($nds_amount, 2, ',', ' ') }}</td>
                    <td>{{ number_format($final_amount, 2, ',', ' ') }}</td>
                    <!-- <td><strong>{{ number_format($final_amount, 2, ',', ' ') }}</strong></td> -->
                </tr>
            @endforeach
            
            <!-- Jami qatori -->
            <tr class="total-row">
                <td colspan="3"><strong>ЖАМИ</strong></td>
                <td><strong>{{ number_format($total_children_9_10, 0, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_children_4, 0, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_children_all, 0, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_cost_9_10, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_cost_4, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_delivery_9_10, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_delivery_4, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_delivery_all, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_amount_without_nds, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_markup, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_nds, 2, ',', ' ') }}</strong></td>
                <!-- <td><strong>{{ number_format($total_final_amount, 2, ',', ' ') }}</strong></td> -->
                <td><strong>{{ number_format($total_final_amount, 2, ',', ' ') }}</strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Footer qismi -->
    <div class="footer">
        <div class="footer-section">
            <div class="signature-label">Аутсорсер директори: ____________________________</div>
        </div>
        <div class="footer-section">
            <div class="signature-label">Буюртмачи директори: ____________________________</div>
        </div>
    </div>
</body>
</html>
