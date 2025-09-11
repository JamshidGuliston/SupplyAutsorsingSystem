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
            @foreach($ages as $age)
                <th rowspan="2">{{ $age->description }}</th>
            @endforeach
                <th rowspan="2">Жами</th>
            @foreach($ages as $age)
                <th rowspan="2">{{ $age->description }}</th>
            @endforeach
            @foreach($ages as $age)
                <th rowspan="2">{{ $age->description }}</th>
            @endforeach
                <th rowspan="2">Жами</th>
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ {{ $costs[$ages[0]->id]->raise }}%</th>
                <th>ҚҚС (НДС) {{ $costs[$ages[0]->id]->nds }}%</th>
                <th>Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <tr class="sub-header">
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ {{ $costs[$ages[0]->id]->raise }}%</th>
                <th>ҚҚС (НДС) {{ $costs[$ages[0]->id]->nds }}%</th>
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
                <tr class="data-row">
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $number_childrens[$day->id]->short_name }}</td>
                    <td>{{ $day->day_number }}/{{ $day->month_name }}/{{ $day->year_name }}</td>
                    @foreach($ages as $age)
                        <td>{{ number_format($number_childrens[$day->id]->where('age_range_id', $age->id)->kingar_children_number, 0, ',', ' ') }}</td>
                    @endforeach
                    <td>{{ number_format(0, 0, ',', ' ') }}</td>
                    @foreach($ages as $age)
                        <td>{{ number_format($costs[$age->id]->cost, 2, ',', ' ') }}</td>
                    @endforeach
                    @php $total_cost = 0; @endphp
                    @foreach($ages as $age)
                        <td>{{ number_format($costs[$age->id]->cost * $number_childrens[$day->id]->where('age_range_id', $age->id)->kingar_children_number, 2, ',', ' ') }}</td>
                        @php $total_cost += $total_cost + number_format($costs[$age->id]->cost * $number_childrens[$day->id]->where('age_range_id', $age->id)->kingar_children_number, 2, ',', ' ');
                    @endforeach
                    <!-- Жами етказиб бериш харажатлари -->
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[0]->id]->nds / 100), 2, ',', ' ') }}</td>
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[0]->id]->nds / 100) * ($costs[$ages[0]->id]->raise / 100), 2, ',', ' ') }}</td>
                    <td>{{ number_format(($total_cost / (1 + $costs[$ages[0]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[0]->id]->nds / 100) * ($costs[$ages[0]->id]->raise / 100)) * ($costs[$ages[0]->id]->nds / 100), 2, ',', ' ') }}</td>
                    <!-- Жами етказиб бериш суммаси (НДС билан) -->
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[0]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[0]->id]->nds / 100) * ($costs[$ages[0]->id]->raise / 100) + ($total_cost / (1 + $costs[$ages[0]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[0]->id]->nds / 100) * ($costs[$ages[0]->id]->raise / 100)) * ($costs[$ages[0]->id]->nds / 100), 2, ',', ' ') }}</td>
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
