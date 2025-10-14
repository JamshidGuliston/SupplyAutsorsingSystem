<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ombor kirim-chiqim hisoboti</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
        }
        h3 {
            text-align: center;
            font-size: 12px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 7px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }
        .product-name {
            text-align: left;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <h3>Ombor kirim-chiqim hisoboti</h3>
    <p style="text-align: center; margin: 5px 0;">
        <strong>Боғча:</strong> {{ $kingar->kingar_name }}<br>
        <strong>Ой:</strong> {{ $month->month_name }} {{ $year->year_name }}
    </p>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 100px;">Махсулотлар</th>
                <th rowspan="2" style="width: 40px;">O'tgan oydan Qoldiq</th>
                @foreach($days as $day)
                <th colspan="2" style="width: 30px;">{{ $day->day_number }}</th>
                @endforeach
                <th rowspan="2" style="width: 50px;">Jami kiritilgan</th>
                <th rowspan="2" style="width: 50px;">Jami sarflangan</th>
                <th rowspan="2" style="width: 50px;">Farqi</th>
            </tr>
            <tr>
                @foreach($days as $day)
                <th style="width: 15px;">-</th>
                <th style="width: 15px;">+</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($plusproducts as $key => $row)
            <?php 
                $totalMinus = 0;
                $totalPlus = 0;
                $residualWeight = isset($residualProducts[$key]) ? $residualProducts[$key]['weight'] : 0;
                $totalPlus += $residualWeight;
            ?>
            <tr>
                <td class="product-name">{{ $row['productname'] }}</td>
                <td>{{ $residualWeight > 0 ? $residualWeight : '' }}</td>
                @foreach($days as $day)
                    <?php
                        $minusValue = isset($minusproducts[$key][$day['id']]) ? $minusproducts[$key][$day['id']] : 0;
                        $plusValue = isset($row[$day['id']."+"], $row[$day['id']."-"]) ? $row[$day['id']."+"] : 0;
                        $totalMinus += $minusValue;
                        $totalPlus += $plusValue;
                    ?>
                    <td>{{ $minusValue > 0 ? $minusValue : '' }}</td>
                    <td>{{ $plusValue > 0 ? $plusValue : '' }}</td>
                @endforeach
                <td><strong>{{ round($totalPlus, 2) }}</strong></td>
                <td><strong>{{ round($totalMinus, 2) }}</strong></td>
                <td><strong>{{ round($totalPlus - $totalMinus, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

