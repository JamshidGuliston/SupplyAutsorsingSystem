<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolalar qatnovi va autsorsing xizmati xarajatlari</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .table-container {
            width: 100%;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 9px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
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
            width: 30px;
        }
        
        .meal-col {
            width: 60px;
        }
        
        .date-col {
            width: 80px;
        }
        
        .children-col {
            width: 80px;
        }
        
        .cost-col {
            width: 100px;
        }
        
        .total-cost-col {
            width: 120px;
        }
        
        .breakdown-col {
            width: 100px;
        }
        
        .final-total-col {
            width: 120px;
            font-weight: bold;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            pointer-events: none;
        }
        
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-section {
            width: 45%;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 20px;
            padding-bottom: 5px;
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
                <th rowspan="3" class="number-col"></th>
                <th rowspan="3" class="meal-col">Таом нома</th>
                <th rowspan="3" class="date-col">Сана</th>
                <th colspan="3">Буюртма бўйича бола сони</th>
                <th colspan="2">Бир нафар болага сарфланган харажат НДС билан</th>
                <th colspan="3">Жами етказиб бериш харажат НДС билан</th>
                <th colspan="4">Жами етказиб бериш харажатлари</th>
                <th rowspan="3" class="final-total-col">Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <tr class="sub-header">
                <th rowspan="2">9-10,5 соатлик гуруҳ</th>
                <th rowspan="2">4 соатлик гуруҳ</th>
                <th rowspan="2">Жами</th>
                <th rowspan="2">9-10,5 соатлик гуруҳ</th>
                <th rowspan="2">4 соатлик гуруҳ</th>
                <th rowspan="2">9-10,5 соатлик гуруҳ</th>
                <th rowspan="2">4 соатлик гуруҳ</th>
                <th rowspan="2">Жами</th>
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ 28,5%</th>
                <th>ҚҚС (НДС) 12%</th>
                <th>Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <tr class="sub-header">
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ 28,5%</th>
                <th>ҚҚС (НДС) 12%</th>
                <th>Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <!-- Ma'lumot qatorlari -->
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
            @endphp
            
            @foreach($days as $day)
                @php
                    // Bolalar sonini hisoblash
                    $children_9_10 = 0;
                    $children_4 = 0;
                    
                    foreach($number_childrens[$day->id] as $child) {
                        if($child->king_age_name_id == 3) { // 9-10.5 soatlik guruh
                            $children_9_10 += $child->kingar_children_number;
                        } elseif($child->king_age_name_id == 4) { // 4 soatlik guruh
                            $children_4 += $child->kingar_children_number;
                        }
                    }
                    
                    $children_all = $children_9_10 + $children_4;
                    
                    // Narxlarni olish
                    $cost_9_10 = 17866.00; // 9-10.5 soatlik guruh uchun narx
                    $cost_4 = 4355.00; // 4 soatlik guruh uchun narx
                    
                    // Yetkazib berish xarajatlari
                    $delivery_9_10 = $children_9_10 * $cost_9_10;
                    $delivery_4 = $children_4 * $cost_4;
                    $delivery_all = $delivery_9_10 + $delivery_4;
                    
                    // Xarajatlar tahlili
                    $amount_without_nds = $delivery_all / 1.12; // QQSsiz summa
                    $markup = $amount_without_nds * 0.285; // 28.5% ustama
                    $nds = $amount_without_nds * 0.12; // 12% QQS
                    $final_amount = $amount_without_nds + $markup + $nds;
                    
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
                    $total_nds += $nds;
                    $total_final_amount += $final_amount;
                @endphp
                
                <tr class="data-row">
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $row_number-1 }}-T</td>
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
                    <td>{{ number_format($nds, 2, ',', ' ') }}</td>
                    <td><strong>{{ number_format($final_amount, 2, ',', ' ') }}</strong></td>
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
                <td><strong>{{ number_format($total_final_amount, 2, ',', ' ') }}</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <div class="signature-section">
            <strong>Аутсорсер:</strong><br>
            ASIA BEST DISTRIBUTION SERVICE МЧЖ<br>
            директори: Т.Саидов
        </div>
        
        <div class="signature-section">
            <strong>Истемолчи:</strong><br>
            {{ $kindgar->kingar_name }}<br>
            директори: <span class="signature-line"></span>
        </div>
    </div>
</body>
</html>
