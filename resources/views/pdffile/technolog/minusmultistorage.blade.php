<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ombor chiqim hisoboti</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h3 {
            margin: 5px 0;
        }
        .product-name {
            text-align: left;
            font-weight: bold;
        }
        .total {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        .separator {
            border-top: 1px solid #ccc;
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>Ombor chiqim hisoboti</h3>
        <p><strong>Боғча:</strong> {{ $kingar->kingar_name }}</p>
        <p><strong>Ой:</strong> {{ $month->month_name }} {{ $year->year_name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 150px;">Махсулотлар</th>
                @foreach($days as $day)
                    <th>{{ $day->day_number }}</th>
                @endforeach
                <th style="width: 60px;">Жами:</th>
            </tr>
        </thead>
        <tbody>
            @foreach($minusproducts as $key => $row)
                @if(is_numeric($key))
                    <?php $all = 0; ?>
                    <tr>
                        <td class="product-name">{{ $row['productname'] }}</td>
                        @foreach($days as $day)
                            @php
                                $plusValue = isset($row[$day->id."+"]) ? $row[$day->id."+"] : 0;
                                $minusValue = isset($row[$day->id."-"]) ? $row[$day->id."-"] : 0;
                                $dayTotal = $plusValue + $minusValue;
                                $all += $dayTotal;
                            @endphp
                            <td>
                                @if($dayTotal > 0)
                                    {{ round($plusValue, 2) }}
                                    @if($minusValue > 0)
                                        <div class="separator"></div>
                                        {{ round($minusValue, 2) }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                        <td class="total">{{ round($all, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>

