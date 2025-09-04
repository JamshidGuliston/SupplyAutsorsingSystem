<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счет фактура</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('https://fonts.googleapis.com/css2?family=DejaVu+Sans:wght@400;700&display=swap');
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .invoice-date {
            font-size: 14px;
        }
        .company-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .company-section {
            width: 48%;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .company-details {
            margin-bottom: 8px;
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
            margin: 30px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
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
            background-color: #f9f9f9;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-section {
            width: 45%;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-title">СЧЕТ ФАКТУРА</div>
        <div class="invoice-number">№ {{ time() }}</div>
        <div class="invoice-date">{{ date('d.m.Y') }}</div>
        <div style="margin-top: 10px; font-size: 12px;">
            товарно-отгрузочным документам № {{ time() }} от {{ date('d.m.Y') }}
        </div>
    </div>

    <div class="company-info">
        <div class="company-section">
            <div class="section-title">AUTSORSER</div>
            <div class="company-details">
                <span class="label">Kompaniya:</span>
                <span class="value">{{ $autorser['company_name'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Manzil:</span>
                <span class="value">{{ $autorser['address'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">P/c:</span>
                <span class="value">{{ $autorser['bank_account'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Bank:</span>
                <span class="value">{{ $autorser['bank'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">MFO:</span>
                <span class="value">{{ $autorser['mfo'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">INN:</span>
                <span class="value">{{ $autorser['inn'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Tel:</span>
                <span class="value">{{ $autorser['phone'] }}</span>
            </div>
        </div>

        <div class="company-section">
            <div class="section-title">BUYURTMACHI</div>
            <div class="company-details">
                <span class="label">Kompaniya:</span>
                <span class="value">{{ $buyurtmachi['company_name'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">INN:</span>
                <span class="value">{{ $buyurtmachi['inn'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">MFO:</span>
                <span class="value">{{ $buyurtmachi['mfo'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Xisob raqami:</span>
                <span class="value">{{ $buyurtmachi['account_number'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">G'azna x/r:</span>
                <span class="value">{{ $buyurtmachi['treasury_account'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">INN:</span>
                <span class="value">{{ $buyurtmachi['treasury_inn'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Bank:</span>
                <span class="value">{{ $buyurtmachi['bank'] }}</span>
            </div>
            <div class="company-details">
                <span class="label">Tel:</span>
                <span class="value">{{ $buyurtmachi['phone'] }}</span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>№</th>
                    <th class="product-name">Гуруҳлар</th>
                    <th class="unit">Ўл. бир</th>
                    <th class="quantity">Сони</th>
                    <th class="price">Нархи</th>
                    <th class="amount">Кўрсатилган хизмат суммаси (ҚҚС билан)</th>
                    <th class="vat">Шундан ҚҚС</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $counter = 1;
                    $total_service_amount = 0;
                    $total_vat_amount = 0;
                @endphp
                @foreach($kindgar->age_range as $age)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td class="product-name">{{ $age->description }}</td>
                            <td class="unit">{{ "бола" }}</td>
                            <td class="quantity">{{ number_format($total_number_children[$age->id], 0) }}</td>
                            <td class="price">{{ number_format($costs->where('age_range_id', $age->id)->first()->eater_cost, 2) }}</td>
                            <td class="amount">{{ number_format($total_number_children[$age->id] * $costs->where('age_range_id', $age->id)->first()->eater_cost, 2) }}</td>
                            <td class="vat">{{ number_format($total_number_children[$age->id] * $costs->where('age_range_id', $age->id)->first()->eater_cost * $costs->where('age_range_id', $age->id)->first()->nds / 100, 2) }}</td>
                        </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5"><strong>Ҳисоб-фактура суммаси:</strong></td>
                    <td class="amount"><strong>{{ number_format($total_service_amount, 2) }}</strong></td>
                    <td class="vat"><strong>{{ number_format($total_vat_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="signatures">
        <div class="signature-section">
            <div class="signature-label">Директор</div>
            <div class="signature-line"></div>
            <div>Нишонов Қ</div>
        </div>
        
        <div class="signature-section">
            <div class="signature-label">Bosh xisobchisi</div>
            <div class="signature-line"></div>
            <div></div>
        </div>
    </div>

    <div class="signatures" style="margin-top: 20px;">
        <div class="signature-section">
            <div class="signature-label">Директор</div>
            <div class="signature-line"></div>
            <div></div>
        </div>
        
        <div class="signature-section">
            <div class="signature-label">Bosh xisobchisi</div>
            <div class="signature-line"></div>
            <div></div>
        </div>
    </div>
</body>
</html> 