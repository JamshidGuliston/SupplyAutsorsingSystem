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
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 16px;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .invoice-number {
            font-size: 15px;
            margin-bottom: 15px;
        }
        
        .invoice-date {
            font-size: 16px;
        }
        
        .company-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .company-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .company-details {
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        
        .value {
            display: inline-block;
        }
        
        .table-container {
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 16px;
        }
        
        .product-name {
            text-align: left;
            width: 40%;
        }
        
        .unit {
            width: 10%;
        }
        
        .quantity {
            width: 10%;
        }
        
        .price {
            width: 15%;
        }
        
        .amount {
            width: 15%;
        }
        
        .vat {
            width: 10%;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
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
            padding-right: 2%;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 27px 0 5px 0;
        }
        
        .signature-label {
            font-size: 16px;
            text-align: left;
        }
        
        /* Snappy uchun qo'shimcha sozlamalar */
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Matn ko'rinishi uchun */
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
        
        /* Responsive uchun */
        @media print {
            body {
                font-size: 16px;
            }
            
            .header {
                margin-bottom: 15px;
            }
            
            .company-info {
                margin-bottom: 15px;
            }
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
                    <th class="product-name">Маҳсулот, иш, хизматлар номи</th>
                    <th class="unit">Ўл.бир</th>
                    <th class="quantity">Сони</th>
                    <th class="price">Нархи</th>
                    <th class="price">Етказиб бериш нархи</th>
                    <th class="price">ҚҚС ва устама</th>
                    <th class="amount">Кўрсатилган хизмат суммаси (ҚҚС билан)</th>
                    <th class="vat">Шундан ҚҚС</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_amount = 0;
                    $total_nds = 0;
                    $tr = 1;
                @endphp
                
                @foreach($kindgar->age_range as $age)
                <tr>
                    <td class="order-number">{{ $tr++ }}</td>
                    <td class="product-name">{{ $age->description . "га кўрсатилган Аутсорсинг хизмати" }}</td>
                    <td class="unit">{{ 'бола' }}</td>
                    <td class="quantity">{{ $total_number_children[$age->id] }}</td>
                    <td class="price">{{ number_format($costs[$age->id]->eater_cost ?? 0, 2) }}</td>
                    <td class="price">{{ number_format($total_number_children[$age->id] * $costs[$age->id]->eater_cost ?? 0, 2) }}</td>
                    <td class="price">{{ $costs[$age->id]->nds ?? 0 }}%</td>
                    <td class="amount">
                        @php
                            $amount = $total_number_children[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                            $total_amount += $amount;
                        @endphp
                        {{ number_format($amount, 2) }}
                    </td>
                    <td class="vat">
                        @php
                            $vat = $amount * ($costs[$age->id]->nds/(100+$costs[$age->id]->nds) ?? 0);
                            $total_nds += $vat;
                        @endphp
                        {{ number_format($vat, 2) }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td>{{ $tr++ }}</td>
                    <td>Аутсорсинг хизмати устамаси</td>
                    <td>Хизмат</td>
                    <td>1</td>
                    <td></td>
                    <td></td>
                    <td>{{ $costs[4]->raise . "%" ?? 0 }}</td>
                    <td>{{ number_format($total_amount * ($costs[4]->raise/100 ?? 0), 2) }}</td>
                    <td>{{ number_format($total_amount * ($costs[4]->raise/100) * ($costs[4]->nds/(100+$costs[4]->nds)), 2) }}</td>
                    @php $total_nds += $total_amount * ($costs[4]->raise/(100)) * ($costs[4]->nds/(100+$costs[4]->nds)); @endphp
                    @php $total_amount += $total_amount * ($costs[4]->raise/100 ?? 0); @endphp
                </tr>

                <!-- Jami qator -->
                <tr class="total-row">
                    <td></td>
                    <td colspan="6" class="text-right font-bold">Жами сумма:</td>
                    <td class="amount font-bold">{{ number_format($total_amount, 2) }}</td>
                    <td class="vat font-bold">{{ number_format($total_nds, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer qismi -->
    <div class="footer">
        <div class="footer-section">
            <!-- <div class="signature-line"></div> -->
            <div class="signature-label">Аутсорсер директори: ____________________________</div>
        </div>
        <div class="footer-section">
            <!-- <div class="signature-line"></div> -->
            <div class="signature-label">Буюртмачи директори: ____________________________</div>
        </div>
    </div>
</body>
</html> 