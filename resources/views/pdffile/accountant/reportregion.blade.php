<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tuman maktabgacha ta'lim tashkilotlariga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 18px;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .table-container {
            width: 100%;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 15px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px 4px;
            text-align: center;
            vertical-align: middle;
            white-space: wrap;
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
            width: 25%;
            vertical-align: top;
            padding-right: 2%;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 20px 0 5px 0;
        }
        
        .signature-label {
            font-size: 16px;
            text-align: left;
        }
        
    </style>
</head>
<body>
    <div class="watermark">Страница</div>
    
    <div class="header">
        {{ $region->region_name }} мактабгача таълим ташкилотларига {{ $days[0]->month_name }} ойида кўрсатилган Аутсорсинг хизмати хисоб китоби
    </div>
    
    <div class="table-container">
        <table>
            <!-- Asosiy sarlavha qatorlari -->
            <tr class="header-row">
                <th rowspan="2" class="org-name-col">Ташкилот номи</th>
                <th colspan="2">Буюртма бўйича бола сони</th>
                <th colspan="2">1 бола учун белгиланган нарх</th>
                <th rowspan="2" class="cost-col">Жами харажат (сўмда)</th>
                <th rowspan="2" class="breakdown-col">ҚҚСсиз жами харажат</th>
                <th rowspan="2" class="breakdown-col">Устама {{ $costs[0]->raise }}%</th>
                <th rowspan="2" class="breakdown-col">Жами суммаси</th>
                <th rowspan="2" class="breakdown-col">ҚҚС {{ $costs[0]->nds }}%</th>
                <th rowspan="2" class="final-total-col">Шартноманинг умумий қиймати ҚҚС билан</th>
            </tr>
            
            <tr class="sub-header">
                <th>3-7 ёш</th>
                <th>Қисқа гр</th>
                <th>3-7 ёш</th>
                <th>Қисқа гр</th>
            </tr>
            
            <!-- Ma'lumot qatorlari -->
            @php
                $total_children_3_7 = 0;
                $total_children_short = 0;
                $total_price_3_7 = 0;
                $total_price_short = 0;
                $total_cost = 0;
                $total_cost_without_qqs = 0;
                $total_markup = 0;
                $total_sum = 0;
                $total_qqs = 0;
                $total_final = 0;
                $row_number = 1;
            @endphp
            
            @foreach($kindgardens as $kindgarden)
                @php
                    $row_number = $kindgarden->number_of_org;
                    // Bolalar sonini hisoblash
                    $children_3_7 = $number_childrens[$kindgarden->id][4] ?? 0; // 3-7 yosh
                    $children_short = $number_childrens[$kindgarden->id][3] ?? 0; // Qisqa guruh 
                    
                    // Narxlarni olish
                    $price_3_7 = $costs->where('age_range_id', 4)->first()->eater_cost; // 3-7 yosh uchun narx
                    $price_short = $costs->where('age_range_id', 3)->first()->eater_cost; // Qisqa guruh uchun narx
                    // Jami narh
                    $total_price_3_7 += $price_3_7;
                    $total_price_short += $price_short;
                    // Jami xarajat
                    $total_cost_row = ($children_3_7 * $price_3_7) + ($children_short * $price_short);
                    
                    // QQSsiz jami xarajat
                    $cost_without_qqs = $total_cost_row / (1 + ($costs[0]->nds/100));
                    
                    // Ustama 29%
                    $markup = $cost_without_qqs * ($costs[0]->raise/100);
                    
                    // Jami summasi
                    $total_sum_row = $cost_without_qqs + $markup;
                    
                    // QQS 12%
                    $qqs = $total_sum_row * ($costs[0]->nds/100);
                    
                    // Shartnomaning umumiy qiymati QQS bilan
                    $final_total = $total_sum_row + $qqs;
                    
                    // Jami hisoblash
                    $total_children_3_7 += $children_3_7;
                    $total_children_short += $children_short;
                    $total_cost += $total_cost_row;
                    $total_cost_without_qqs += $cost_without_qqs;
                    $total_markup += $markup;
                    $total_sum += $total_sum_row;
                    $total_qqs += $qqs;
                    $total_final += $final_total;
                @endphp
                
                <tr class="data-row">
                    <td class="org-name-col">{{ $row_number }}-ДМТТ</td>
                    <td>{{ number_format($children_3_7, 0, ',', ' ') }}</td>
                    <td>{{ number_format($children_short, 0, ',', ' ') }}</td>
                    <td>{{ number_format($price_3_7, 1, ',', ' ') }}</td>
                    <td>{{ number_format($price_short, 1, ',', ' ') }}</td>
                    <td>{{ number_format($total_cost_row, 2, ',', ' ') }}</td>
                    <td>{{ number_format($cost_without_qqs, 2, ',', ' ') }}</td>
                    <td>{{ number_format($markup, 2, ',', ' ') }}</td>
                    <td>{{ number_format($total_sum_row, 2, ',', ' ') }}</td>
                    <td>{{ number_format($qqs, 2, ',', ' ') }}</td>
                    <td class="final-total-col">{{ number_format($final_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            
            <!-- Jami qatori -->
            <tr class="total-row">
                <td><strong>Jami</strong></td>
                <td><strong>{{ number_format($total_children_3_7, 0, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_children_short, 0, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_price_3_7, 1, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_price_short, 1, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_cost, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_cost_without_qqs, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_markup, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_sum, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_qqs, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_final, 2, ',', ' ') }}</strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Footer qismi -->
    <div class="footer">
        <div class="footer-section">
            <div class="signature-label">Аутсорсер:</div><br>
            {{ env('COMPANY_NAME') }}<br>
            директори: _____________________
        </div><div class="footer-section">
            <div class="signature-label"></div>
            <br>
            {{ env('COMPANY_NAME')}} <br>Бош хисобчиси: _____________________<br>
        </div>
        <div class="footer-section">
            <div class="signature-label">Истемолчи:</div>
            <br>
            {{ $region->region_name }} ММТБ <br>директори: _____________________<br>
        </div>
        <div class="footer-section">
            <div class="signature-label"></div>
            <br>
            {{ $region->region_name }} ММТБ<br>Бош хисобчиси: _____________________<br>
        </div>
    </div>
    </div>
</body>
</html>
