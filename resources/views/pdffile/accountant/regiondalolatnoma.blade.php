<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регион Далолатнома</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 16px;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .work-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .act-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .act-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .date-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .city {
            text-align: left;
        }
        
        .date {
            text-align: right;
        }
        
        .intro-text {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .table-container {
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 12px 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 16px;
        }
        
        .number-col {
            width: 8%;
        }
        
        .work-name-col {
            width: 60%;
            text-align: left;
        }
        
        .amount-col {
            width: 32%;
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .total-row td {
            text-align: center;
        }
        
        .summary-text {
            margin: 20px 0;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .amount-text {
            font-weight: bold;
            font-size: 18px;
        }
        
        .written-amount {
            font-style: italic;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-block {
            width: 45%;
            text-align: left;
        }
        
        .signature-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .signature-info {
            margin-bottom: 5px;
            font-size: 15px;
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
            margin: 20px 0 5px 0;
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
        <div class="work-title">Бажарилган ишлар</div>
        <div class="act-title">ДАЛОЛАТНОМАСИ № {{ $invoice_number ?? "3/2" }}</div>
        <div class="date-info">
            <div class="city">{{ $buyurtmachi['address'] ?? 'Олмалик шахар' }}</div>
            <div class="date">{{ $invoice_date ?? '19.09.2025 йил' }}</div>
        </div>
    </div>

    <!-- Kirish matni -->
    <div class="intro-text">
        Бизлар қуйидаги имзо чекувчилар {{ $autorser['company_name'] ?? 'ASIA BEST DISTRIBUTION SERVICE' }} директори Б.Тажибaев бир томондан ва {{ $buyurtmachi['company_name'] ?? 'Олмалик шахар ММТБ' }} иккинчи томондан <br>{{ $contract_data ?? '25111006442027' }}даги шартнома асосида қуйидаги миқдорда бажарилганлиги ҳақида туздик:
    </div>

    <!-- Jadval -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="number-col">№</th>
                    <th class="work-name-col">Иш, хизмат номи</th>
                    <th class="amount-col">Бажарилган ишлар миқдори (ҚҚС билан)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_amount = 0;
                    $tr = 1;
                @endphp
                
                @foreach($ages as $age)
                <tr>
                    <td class="number-col">{{ $tr++ }}</td>
                    <td class="work-name-col">
                        {{ $buyurtmachi['address'] ?? 'Олмалик шахар' }} ММТБга тасарруфидаги барча ДМТТ {{ $age->description ?? '9-10,5 соатлик' }} тарбияланувчилари учун {{ $days->first()->year_name ?? '2025' }} йил {{ $days->first()->day_number ?? '02' }}-{{ $days->last()->day_number ?? '19' }} {{ $days->first()->month_name ?? 'сентябр' }}да аутсорсинг асосида кунига уч маҳал овқатланишни ташкил этиш бўйича:
                    </td>
                    <td class="amount-col">
                        @php
                            $amount = $total_number_children[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                            $total_amount += $amount;
                        @endphp
                        {{ number_format($amount, 2, '.', ' ') }}
                    </td>
                </tr>
                @endforeach
                
                <tr>
                    <td class="number-col">{{ $tr++ }}</td>
                    <td class="work-name-col">Аутсорсинг хизмати ({{ $costs[$ages->first()->id]->raise ?? '28,5' }}%)</td>
                    <td class="amount-col">
                        @php
                            $outsourcing_amount = $total_amount * (($costs[$ages->first()->id]->raise ?? 28.5) / 100);
                            $total_amount += $outsourcing_amount;
                        @endphp
                        {{ number_format($outsourcing_amount, 2, '.', ' ') }}
                    </td>
                </tr>

                <!-- Jami qator -->
                <tr class="total-row">
                    <td></td>
                    <td class="text-center font-bold">ЖАМИ</td>
                    <td class="amount-col font-bold">{{ number_format($total_amount, 2, '.', ' ') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Xulosa matni -->
    <div class="summary-text">
        Бажарилган ишлар учун тўлов миқдори барча устама хак ва соликларни хисобга олган холда <br>ҚҚС билан <span class="amount-text">{{ number_format($total_amount, 2, '.', ' ') }}</span>  сумни ташкил этади.
    </div>

    <!-- Imzo qismi -->
    <div class="footer">
        <div class="footer-section">
            <div class="signature-title">Аутсорсер:</div>
            <div class="signature-info">{{ $autorser['company_name'] ?? 'ASIA BEST DISTRIBUTION SERVICE' }}</div>
            <div class="signature-info">директори: {{ $autorser['company_director'] ?? 'B.Tajibaev' }}</div>
            <div class="signature-line"></div>
        </div>
        
        <div class="footer-section">
            <div class="signature-title">Истемолчи:</div>
            <div class="signature-info">{{ $region->region_name ?? 'Олмалик шахар' }} ММТБ</div>
            <div class="signature-info">Директори</div>
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>
