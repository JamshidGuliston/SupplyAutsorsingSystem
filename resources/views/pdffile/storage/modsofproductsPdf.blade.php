<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maxsulotlar hisoboti</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .date-info {
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #333;
        }
        .product-name {
            text-align: left;
            font-weight: bold;
        }
        .unit {
            font-size: 9px;
        }
        .amount {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>МАХСУЛОТЛАР ҲИСОБОТИ</h2>
    </div>
    
    <div class="date-info">
        <strong>Davr:</strong> {{ $reportData['start_date'] }} - {{ $reportData['end_date'] }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20%;">Махсулот номи</th>
                <th rowspan="2" style="width: 8%;">Ул.бир</th>
                <th colspan="3" style="width: 24%;">Кирим</th>
                <th colspan="3" style="width: 24%;">Чиқим</th>
                <th colspan="3" style="width: 24%;">Қолдиқ</th>
            </tr>
            <tr>
                <th>Микдори</th>
                <th>Уртача нархи</th>
                <th>Суммаси</th>
                <th>Микдори</th>
                <th>Нархи</th>
                <th>Суммаси</th>
                <th>Микдори</th>
                <th>Нархи</th>
                <th>Суммаси</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['products'] as $product)
            <tr>
                <td class="product-name">{{ $product['p_name'] }}</td>
                <td class="unit">{{ $product['size_name'] }}</td>
                <td>{{ number_format($product['weight'], 2) }}</td>
                <td>{{ number_format($product['middlecost'], 2) }}</td>
                <td class="amount">{{ number_format($product['weight'] * $product['middlecost'], 2) }}</td>
                <td>{{ number_format($product['minusweight'], 2) }}</td>
                <td>{{ number_format($product['middlecost'], 2) }}</td>
                <td class="amount">{{ number_format($product['minusweight'] * $product['middlecost'], 2) }}</td>
                <td>{{ number_format($product['weight'] - $product['minusweight'], 2) }}</td>
                <td>{{ number_format($product['middlecost'], 2) }}</td>
                <td class="amount">{{ number_format(($product['weight'] - $product['minusweight']) * $product['middlecost'], 2) }}</td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td><strong>JAMI:</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>{{ number_format($reportData['total_taking'], 2) }}</strong></td>
                <td></td>
                <td></td>
                <td><strong>{{ number_format($reportData['total_giving'], 2) }}</strong></td>
                <td></td>
                <td></td>
                <td><strong>{{ number_format($reportData['total_mod'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Hisobot {{ date('d.m.Y H:i') }} da tayyorlandi</p>
    </div>
</body>
</html> 