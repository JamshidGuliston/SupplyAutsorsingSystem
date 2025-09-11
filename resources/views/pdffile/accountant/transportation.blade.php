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
                <th rowspan="2" class="number-col"></th>
                <th rowspan="2" class="meal-col">Таом нома</th>
                <th rowspan="2" class="date-col">Сана</th>
                <th colspan="3">Буюртма бўйича бола сони</th>
                <th colspan="2">Бир нафар болага сарфланган харажат НДС билан</th>
                <th colspan="3">Жами етказиб бериш харажат НДС билан</th>
                <th colspan="3">Жами етказиб бериш харажатлари</th>
                <th rowspan="2" class="final-total-col">Жами етказиб бериш суммаси (НДС билан)</th>
            </tr>
            
            <tr class="sub-header">
            @foreach($ages as $age)
                <th>{{ $age->description }}</th>
            @endforeach
                <th>Жами</th>
            @foreach($ages as $age)
                <th>{{ $age->description }}</th>
            @endforeach
            @foreach($ages as $age)
                <th>{{ $age->description }}</th>
            @endforeach
                <th>Жами</th>
                <th>Сумма (безНДС)</th>
                <th>Устама ҳақ {{ $costs[$ages[0]->id]->raise }}%</th>
                <th>ҚҚС (НДС) {{ $costs[$ages[0]->id]->nds }}%</th>
            </tr>
            <!-- Ma'lumot qatorlari -->
            @php
                $row_number = 1;
                $total_age_children = [];
                $total_all_children = 0;
                $total_eater_cost = [];
                $total_spends_all = [];
                $total_spends = 0;
                $total_without_nds = 0;
                $total_raise = 0;
                $total_nds = 0;
                $total_summ = 0;
            @endphp
            @foreach($days as $day)
                <tr class="data-row">
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $number_childrens[$day->id][$ages[1]->id]->short_name ?? $number_childrens[$day->id][$ages[1]->id]->menu_name ?? '' }}</td>
                    <td>{{ $day->day_number }}/{{ $day->month_name }}/{{ $day->year_name }}</td>
                    <!-- Буюртма бўйича бола сони -->
                    @php $total_children = 0; @endphp
                    @foreach($ages as $age)
                    @if(isset($number_childrens[$day->id][$age->id]->kingar_children_number))
                        <td>{{ number_format($number_childrens[$day->id][$age->id]->kingar_children_number ?? 0, 0, ',', ' ') }}</td>
                        @php $total_children += $number_childrens[$day->id][$age->id]->kingar_children_number ?? 0; @endphp
                        @php $total_all_children += $number_childrens[$day->id][$age->id]->kingar_children_number ?? 0; @endphp
                        @if(!isset($total_age_children[$age->id]))
                            @php $total_age_children[$age->id] = 0; @endphp
                        @endif
                        @php $total_age_children[$age->id] += $number_childrens[$day->id][$age->id]->kingar_children_number ?? 0; @endphp
                    @else
                        <td>{{ "-" }}</td>
                    @endif
                    @endforeach
                    <td>{{ number_format($total_children, 0, ',', ' ') }}</td>
                    <!-- Бир нафар болага сарфланган харажат НДС билан -->
                    @foreach($ages as $age)
                    @if(isset($number_childrens[$day->id][$age->id]->kingar_children_number))
                        <td>{{ number_format($costs[$age->id]->eater_cost ?? 0, 2, ',', ' ') }}</td>
                        @if(!isset($total_eater_cost[$age->id]))
                            @php $total_eater_cost[$age->id] = 0; @endphp
                        @endif
                        @php $total_eater_cost[$age->id] += $costs[$age->id]->eater_cost; @endphp
                    @else
                        <td>{{ "-" }}</td>
                    @endif
                    @endforeach
                    <!-- Жами етказиб бериш харажатлари -->
                    @php $total_cost = 0;@endphp
                    @foreach($ages as $age)
                    @if(isset($number_childrens[$day->id][$age->id]->kingar_children_number))
                        <td>{{ number_format($costs[$age->id]->eater_cost * $number_childrens[$day->id][$age->id]->kingar_children_number, 2, ',', ' ') }}</td>
                        @php $total_cost += $total_cost + $costs[$age->id]->eater_cost * $number_childrens[$day->id][$age->id]->kingar_children_number; @endphp
                        @if(!isset($total_spends_all[$age->id]))
                            @php $total_spends_all[$age->id] = 0; @endphp
                        @endif
                        @php $total_spends_all[$age->id] += $costs[$age->id]->eater_cost * $number_childrens[$day->id][$age->id]->kingar_children_number; @endphp
                    @else
                        <td>{{ "-" }}</td>
                    @endif
                    @endforeach
                    <td>{{ number_format($total_cost, 2, ',', ' ') }}</td>
                    @php $total_spends += $total_cost; @endphp
                    <!-- Жами етказиб бериш харажатлари -->
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[1]->id]->nds / 100), 2, ',', ' ') }}</td>
                    @php $total_without_nds += $total_cost / (1 + $costs[$ages[1]->id]->nds / 100); @endphp
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100), 2, ',', ' ') }}</td>
                    @php $total_raise += $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100); @endphp
                    <td>{{ number_format(($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100)) * ($costs[$ages[1]->id]->nds / 100), 2, ',', ' ') }}</td>
                    @php $total_nds += ($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100)) * ($costs[$ages[1]->id]->nds / 100); @endphp
                    <!-- Жами етказиб бериш суммаси (НДС билан) -->
                    <td>{{ number_format($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100) + ($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100)) * ($costs[$ages[1]->id]->nds / 100), 2, ',', ' ') }}</td>
                    @php $total_summ += $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100) + ($total_cost / (1 + $costs[$ages[1]->id]->nds / 100) + $total_cost / (1 + $costs[$ages[1]->id]->nds / 100) * ($costs[$ages[1]->id]->raise / 100)) * ($costs[$ages[1]->id]->nds / 100); @endphp
                </tr>
            @endforeach
            
            <!-- Jami qatori -->
            <tr class="total-row">
                <td colspan="3"><strong>ЖАМИ</strong></td>
                @foreach($ages as $age)
                    <td><strong>{{ number_format($total_age_children[$age->id] ?? 0, 0, ',', ' ') }}</strong></td>
                @endforeach
                <td><strong>{{ number_format($total_all_children ?? 0, 0, ',', ' ') }}</strong></td>
                @foreach($ages as $age)
                    <td><strong>{{ number_format($total_eater_cost[$age->id] ?? 0, 2, ',', ' ') }}</strong></td>
                @endforeach
                @foreach($ages as $age)
                    <td><strong>{{ number_format($total_spends_all[$age->id] ?? 0, 2, ',', ' ') }}</strong></td>
                @endforeach
                <td><strong>{{ number_format($total_spends ?? 0, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_without_nds ?? 0, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_raise ?? 0, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_nds ?? 0, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_summ, 2, ',', ' ') }}</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <div class="signature-section">
            <strong>Аутсорсер:</strong><br>
            {{ env('COMPANY_NAME') }}<br>
            директор: _________________________
        </div>
        
        <div class="signature-section">
            <strong>Истемолчи:</strong><br>
            {{ $kindgar->kingar_name }}<br>
            директор: <span class="signature-line"></span>
        </div>
    </div>
</body>
</html>
