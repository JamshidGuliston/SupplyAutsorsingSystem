<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счет фактура</title>
    <style>
        /* Snappy uchun optimizatsiya qilingan CSS */
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .invoice-date {
            font-size: 14px;
        }
        
        .company-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .company-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .company-details {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .value {
            display: inline-block;
        }
        
        .table-container {
            margin: 15px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 14px;
        }
        
        .product-name {
            text-align: left;
            width: 35%;
        }
        
        .unit {
            width: 8%;
        }
        
        .quantity {
            width: 8%;
        }
        
        .price {
            width: 12%;
        }
        
        .vat-percent {
            width: 8%;
        }
        
        .vat {
            width: 12%;
        }
        
        .amount {
            width: 250px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        
        .footer-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }
        
        .signature-label {
            font-size: 14px;
            text-align: left;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header qismi -->
    <div class="header">
        <div class="invoice-title">СЧЁТ-ФАКТУРА</div>
        <div class="invoice-number">№ {{ $invoice_number ?? "_________________________________" }}</div>
        <div class="invoice-date">{{ "фактура санаcи: " . $invoice_date."  й" ?? "_________________________________" }}</div>
        <div class="invoice-date">{{ "Хизмат кўрсатиш шартномаси: ".$contract_data }}</div>
    </div>

    <!-- Kompaniya ma'lumotlari -->
    <div class="company-info">
        <div class="company-section">
            <div class="section-title">Аутсорсер:</div>
            <div class="company-details">
                <span class="label">Ташкилот:</span>
                <span class="value">{{ $autorser['company_name'] ?? 'IOS-Service MCHJ' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Манзил:</span>
                <span class="value">{{ $autorser['address'] ?? 'Toshkent shahri, Olmazor tumani, 1' }}</span>
            </div>
            <div class="company-details">
                <span class="label">ИНН:</span>
                <span class="value">{{ $autorser['inn'] ?? '123456789' }}</span>
            </div>
            <div class="company-details">
                <span class="label">МФО:</span>
                <span class="value">{{ $autorser['mfo'] ?? '12345' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Хисоб рақам:</span>
                <span class="value">{{ $autorser['bank_account'] ?? '1234567890123456' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Банк:</span>
                <span class="value">{{ $autorser['bank'] ?? 'Асака банк' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Телефон:</span>
                <span class="value">{{ $autorser['phone'] ?? '+998901234567' }}</span>
            </div>
        </div>

        <div class="company-section">
            <div class="section-title">Буюртмачи:</div>
            <div class="company-details">
                <span class="label">Ташкилот:</span>
                <span class="value">{{ $buyurtmachi['company_name'] ?? '_________________________________' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Манзил:</span>
                <span class="value">{{ $buyurtmachi['address'] ?? '' }}</span>
            </div>
            <div class="company-details">
                <span class="label">ИНН:</span>
                <span class="value">{{ $buyurtmachi['inn'] ?? '' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Х/р:</span>
                <span class="value">{{ $buyurtmachi['bank_account'] ?? '' }}</span>
            </div>
            <div class="company-details">
                <span class="label">МФО:</span>
                <span class="value">{{ $buyurtmachi['mfo'] ?? '' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Ягона ғ.х/р:</span>
                <span class="value">{{ $buyurtmachi['account_number'] ?? '' }}</span>
            </div>
            <div class="company-details">
                <span class="label">Банк:</span>
                <span class="value">{{ $buyurtmachi['bank'] ?? "" }}</span>
            </div>
        </div>
    </div>

    <!-- Mahsulotlar jadvali -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="order-number">№</th>
                    <th class="product-name">Иш, хизматлар номи</th>
                    <th class="unit">Ўл.бир</th>
                    <th class="quantity">Сони</th>
                    <th class="price">Нархи</th>
                    <th class="price">Етказиб бериш суммаси</th>
                    <th class="vat-percent">ҚҚС %</th>
                    <th class="vat">Шундан ҚҚС</th>
                    <th class="amount">Кўрсатилган хизмат суммаси (ҚҚС билан)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tr = 1;
                    $sum_base = 0; // F17 - asosiy summa
                    $sum_raise = 0; // F18 - ustama
                    $sum_total = 0; // F19 - jami
                    
                    $qqs_base = 0; // H17 - asosiy QQS
                    $qqs_raise = 0; // H18 - ustama QQS
                    $qqs_total = 0; // H19 - jami QQS
                    
                    $total_base = 0; // I17 - asosiy QQS bilan
                    $total_raise = 0; // I18 - ustama QQS bilan
                    $total_sum = 0; // I19 - jami QQS bilan
                @endphp
                
                @foreach($kindgar->age_range as $age)
                    @php
                        // F17 = bolalar soni * eater_cost
                        $f17 = $total_number_children[$age->id] * ($costs[$age->id]->eater_cost ?? 0) /(1 + (($costs[$age->id]->nds ?? 0) / 100));
                        $sum_base += $f17;
                        
                        // H17 = F17 * nds/100
                        $h17 = $total_number_children[$age->id] * ($costs[$age->id]->eater_cost ?? 0) * (($costs[$age->id]->nds ?? 0) / 100) / (1 + (($costs[$age->id]->nds ?? 0) / 100));
                        $qqs_base += $h17;
                        
                        // I17 = H17 + F17
                        $i17 = $h17 + $f17;
                        $total_base += $i17;
                    @endphp
                    <tr>
                        <td class="order-number">{{ $tr++ }}</td>
                        <td class="product-name">{{ $buyurtmachi['address'] . " " . $kindgar->number_of_org .'-сонли ДМТТ ' . $age->description . " тарбияланувчилари учун " . $days[0]->year_name . " йил " . $days[0]->day_number . "-" . $days[count($days)-1]->day_number . " " . $days->first()->month_name . "да аутсорсинг асосида кунига уч маҳал овқатланишни ташкил этиш бўйича:" }}</td>
                        <td class="unit">{{ 'cум' }}</td>
                        <td class="quantity">{{ "1" }}</td>
                        <td class="price">{{ number_format($f17, 2) }}</td>
                        <td class="price">{{ number_format($f17, 2) }}</td>
                        <td class="vat-percent">{{ $costs[$age->id]->nds ?? 0 }}%</td>
                        <td class="vat">{{ number_format($h17, 2) }}</td>
                        <td class="amount">{{ number_format($i17, 2) }}</td>
                    </tr>
                @endforeach
                
                @php
                    // F18 = F17 * raise
                    $f18 = $sum_base * (($costs[$age->id]->raise ?? 0) / 100);
                    $sum_raise = $f18;
                    
                    // H18 = F18 * nds/100
                    $h18 = $f18 * (($costs[$age->id]->nds ?? 0) / 100);
                    $qqs_raise = $h18;
                    
                    // I18 = H18 + F18
                    $i18 = $h18 + $f18;
                    $total_raise = $i18;
                    
                    // F19 = SUM(F17:F18)
                    $sum_total = $sum_base + $sum_raise;
                    
                    // H19 = SUM(H17:H18)
                    $qqs_total = $qqs_base + $qqs_raise;
                    
                    // I19 = SUM(I17:I18)
                    $total_sum = $total_base + $total_raise;
                @endphp
                
                <!-- Ustama qatori -->
                <tr>
                    <td>{{ $tr++ }}</td>
                    <td class="product-name">Аутсорсинг хизмати устамаси ({{ $costs[$age->id]->raise ?? 0 }}%)</td>
                    <td>cум</td>
                    <td>1</td>
                    <td>{{ number_format($f18, 2) }}</td>
                    <td>{{ number_format($f18, 2) }}</td>
                    <td>{{ $costs[$age->id]->nds ?? 0 }}%</td>
                    <td>{{ number_format($h18, 2) }}</td>
                    <td>{{ number_format($i18, 2) }}</td>
                </tr>

                <!-- Jami qator -->
                <tr class="total-row">
                    <td colspan="4" class="text-right font-bold">Жами сумма:</td>
                    <td></td>
                    <td class="font-bold">{{ number_format($sum_total, 2) }}</td>
                    <td></td>
                    <td class="font-bold">{{ number_format($qqs_total, 2) }}</td>
                    <td class="font-bold">{{ number_format($total_sum, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer qismi -->
    <div class="footer">
        <div class="footer-section">
            <span class="signature-label">Топширди:</span>
            <br/>
            <br/>
            <span class="signature-label">{{ $autorser['company_name'] ?? '___________________________' }}</span>
            <br/>
            <br/>
            <div class="signature-label">Аутсорсер директори: ____________________________ {{ $autorser['company_director'] }}</div>
            <br/>
            <div class="signature-label"> Бош хисобчиси: ___________________________ </div>
        </div>
        <div class="footer-section">
            <span class="signature-label">Қабул қилди:</span>
            <br/>
            <br/>
            <br/>
            <br/>
            <div class="signature-label"> {{ $kindgar->number_of_org .'-сонли ДМТТ ' }} директори: ___________      ______________________</div>
        </div>
    </div>
</body>
</html>

