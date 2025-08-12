<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sotuv maxsulotlari</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial Unicode MS', 'Arial', sans-serif;
            font-size: 12px;
            margin: 0px 20px 3px 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .header p {
            color: #7f8c8d;
            margin: 0px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #e8f4f8;
            font-weight: bold;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
        
        /* O'zbek shrifti uchun */
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap');
        }
        
        /* Barcha matnlar uchun o'zbek shrifti */
        * {
            font-family: 'DejaVu Sans', 'Noto Sans', 'Arial Unicode MS', sans-serif !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sotuv maxsulotlari ro'yxati</h1>
        <p>Sana: {{ date('d.m.Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>Maxsulot nomi</th>
                <th>O'lcham</th>
                <th>Og'irlik (kg)</th>
                <th>Narx (so'm/kg)</th>
                <th>Jami narx (so'm)</th>
            </tr>
        </thead>
        <tbody>
            @php $total_price = 0; @endphp
            @foreach($res as $index => $row)
                @php $total_price += $row->cost * $row->weight; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td class="text-center">{{ $row->size_name }}</td>
                    <td class="text-center">{{ $row->weight }}</td>
                    <td class="text-right">{{ number_format($row->cost, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($row->cost * $row->weight, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>Umumiy summa:</strong></td>
                <td class="text-right"><strong>{{ number_format($total_price, 0, ',', ' ') }} so'm</strong></td>
            </tr>
        </tbody>
    </table>
    <!-- qrmanzil.jpg ni tablitsa tagiga chapga qo'yish kerak  -->
     <div>
        @php
            $qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
        @endphp
        <img src="data:image/jpeg;base64,{{ $qrImage }}" 
            style="width:18%; position:absolute; top:0px; left:10px;">
     </div>

    <div class="footer">
        <p>Ushbu hisobot {{ date('d.m.Y H:i') }} da tayyorlandi</p>
    </div>
</body>
</html> 