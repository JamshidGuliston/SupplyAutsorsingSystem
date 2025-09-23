<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xisobot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background-color: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 18px;
            line-height: 1.4;
        }
        p
        .table-container {
            width: 100%;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            font-size: 14px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px 4px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
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
        
        .region-total-row {
            background-color: #c0c0c0;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .footer-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 2%;
        }

        .page-break {
            page-break-before: always;
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
        
        .company-name {
            font-weight: bold;
            margin-top: 20px;
        }
        
        .director-label {
            margin-top: 10px;
        }
        
        .stamp-area {
            margin-top: 20px;
            text-align: center;
        }
        
        .number-col {
            width: 5%;
        }
        
        .mtt-col {
            width: 15%;
        }
        
        .month-col {
            width: 15%;
        }
        
        .amount-col {
            width: 20%;
        }
        
        .surcharge-col {
            width: 20%;
        }
        
        .vat-col {
            width: 20%;
        }
        
        .total-col {
            width: 25%;
        }
    </style>
</head>
<body>
    @foreach($ages as $age)
    @if(!$loop->first)
        <div class="page-break"></div>
    @endif
    <div class="header">
        {{ $region->region_name }} ДМТТларда {{ $days[0]->day_number }}-{{ $days[count($days)-1]->day_number }} {{ $days[0]->month_name }} {{ $days[0]->year_name }} йил кунлари {{ $age->description }}и учун аутсорсинг хизмати харажатлари тўғрисидаги маълумот
    </div>
    
    <div class="table-container">
        <table>
            <!-- Asosiy sarlavha qatorlari -->
            <tr class="header-row">
                <th rowspan="2" class="number-col">№</th>
                <th rowspan="2" class="mtt-col">ДМТТ</th>
                <th rowspan="2" class="month-col">Кунлар</th>
                <th colspan="3" class="amount-col">Харжатлар</th>
                <th rowspan="2" class="total-col">Жами</th>
            </tr>
            
            <tr class="sub-header">
                <th>Сумма (ҚҚС сиз)</th>
                <th>Устама хақ {{ $costs[0]->raise ?? 28.5 }}%</th>
                <th>ҚҚС {{ $costs[0]->nds ?? 12 }}%</th>
            </tr>
            
            <!-- Ma'lumot qatorlari -->
            @php
                $total_without_vat = 0;
                $total_surcharge = 0;
                $total_vat = 0;
                $total_payment = 0;
                $row_number = 1;
                $no_childrens = 0;
                $bool = true;
            @endphp
            @foreach($kindgardens as $kindgarden)
                @if($number_childrens[$kindgarden->id][$age->id] == 0)
                    @php $no_childrens++; @endphp
                @endif
            @endforeach
            
            @foreach($kindgardens as $kindgarden)
                @if($number_childrens[$kindgarden->id][$age->id] == 0)
                    @continue
                @endif
                @php
                    // Bolalar sonini hisoblash
                    $children = $number_childrens[$kindgarden->id][$age->id] ?? 0; // current age
                    
                    // Narxlarni olish
                    $price = $costs->where('age_range_id', $age->id)->first()->eater_cost ?? 0; // current age uchun narx
                    
                    // Jami xarajat
                    $total_cost_row = ($children * $price);
                    
                    // QQSsiz jami xarajat
                    $cost_without_vat = $total_cost_row / (1 + (($costs[0]->nds ?? 12)/100));
                    
                    // Ustama
                    $surcharge = $cost_without_vat * (($costs[0]->raise ?? 28.5)/100);
                    
                    // QQS
                    $vat = ($cost_without_vat + $surcharge) * (($costs[0]->nds ?? 12)/100);
                    
                    // Jami to'lanadigan summa
                    $total_payment_row = $cost_without_vat + $surcharge + $vat;
                    
                    // Jami hisoblash
                    $total_without_vat += $cost_without_vat;
                    $total_surcharge += $surcharge;
                    $total_vat += $vat;
                    $total_payment += $total_payment_row;
                @endphp
                
                <tr class="data-row">
                    <td class="number-col">{{ $row_number++ }}</td>
                    <td class="mtt-col">{{ $kindgarden->number_of_org }}-ДМТТ</td>
                    @if($bool)
                        @php $bool = false; @endphp
                        <td rowspan="{{ count($kindgardens)-$no_childrens }}" class="month-col">{{ $days[0]->day_number }}-{{ $days[count($days)-1]->day_number }} {{ $days[0]->month_name }}</td>
                    @endif
                    <td class="amount-col">{{ number_format($cost_without_vat, 2, ',', ' ') }}</td>
                    <td class="surcharge-col">{{ number_format($surcharge, 2, ',', ' ') }}</td>
                    <td class="vat-col">{{ number_format($vat, 2, ',', ' ') }}</td>
                    <td class="total-col">{{ number_format($total_payment_row, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            
            <!-- Jami qatori -->
            <tr class="total-row">
                <td colspan="3"><strong>Жами</strong></td>
                <td><strong>{{ number_format($total_without_vat, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_surcharge, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_vat, 2, ',', ' ') }}</strong></td>
                <td><strong>{{ number_format($total_payment, 2, ',', ' ') }}</strong></td>
            </tr>
                        
        </table>
    </div>
    
    <!-- Footer qismi -->
    <div class="footer">
        <div class="footer-section">
            <div class="company-name">{{ env('COMPANY_NAME') }}</div>
            <div class="director-label">Директор: _____________________</div>
            <div class="stamp-area">
                <!-- Muhr joylashuvi -->
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>
