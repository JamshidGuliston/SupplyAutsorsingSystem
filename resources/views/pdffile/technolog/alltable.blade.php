<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Taxminiy Taomnoma</title>
    <style>
        @page { 
            margin: 0.2in 0.1in 0.1in 0.1in; 
            size: A4 landscape;
        }
        
        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            width: 100%;
            color: #000;
            background-color: white;
        }
        
        .header-info {
            margin-bottom: 8px;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            table-layout: fixed;
            background-color: white;
        }
        
        .main-table th,
        .main-table td {
            border: 0.5px solid #ccc;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            background-color: white;
        }
        
        .main-table th {
            font-weight: bold;
            font-size: 8px;
        }
        
        .main-table td {
            font-size: 8px;
        }
        
        /* Vertikal matn uchun (Bolalar/Xodimlar ustunlari) */
        .vertical-text {
            writing-mode: vertical-rl;
            -webkit-writing-mode: vertical-rl;
            -ms-writing-mode: tb-rl;
            transform: rotate(180deg);
            transform-origin: center center;
            text-align: center;
            white-space: nowrap;
            display: inline-block;
        }
        
        .product-name {
			font-size: 7px;
            font-weight: bold;
            text-align: left;
            padding: 2px;
        }
        
        .meal-time {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-weight: bold;
            font-size: 7px;
        }
        
        .food-name {
			width: 70px;
            text-align: left;
            padding: 2px;
            font-size: 8px;
        }
        
        .summary-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
            border-top: 1px solid #000;
        }
        
        .children-section {
            background-color: #f8f9fa;
        }
        
        .workers-section {
            background-color: #fff3cd;
        }
        
        .final-total {
            background-color: #d4edda;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        
        /* Fixed column widths */
        .col-1 { width: 2%; }
        .col-2 { width: 8%; font-size: 8px; }
        .col-product { width: 18px; }
		
        .vrt-header {
            text-align: center !important;
            vertical-align: middle !important;
            writing-mode: vertical-rl;
            -webkit-writing-mode: vertical-rl;
            -ms-writing-mode: tb-rl;
            text-orientation: mixed;
            white-space: normal;
            word-wrap: break-word;
            overflow: hidden;
            font-size: 6px;
            line-height: 1.1;
            padding: 1px;
        }
		
		/* Maxsulot ustunlari uchun */
		.product-column {
			width: 2.5% !important;
			max-width: 2.5%;
			overflow: hidden;
			height: 100px;
			min-height: 100px;
		}


    </style>
</head>
<body>
    <div class="header-info">
        <strong>ДМТТ номи: {{ $menu[0]['kingar_name'] }}</strong> | 
        <strong>Таомнома: {{ $taomnoma['menu_name'] }}</strong><br>
        <strong>Сана: {{ $day['day_number'] }}.{{ $day['month_name'] }}.{{ $day['year_name'] }}-й учун</strong> | 
        <strong>{{ $menu[0]['age_name'] }}ли болалар сони: {{ $menu[0]['kingar_children_number'] }}</strong> | 
        <strong>Ходимлар сони: {{ $menu[0]['workers_count'] }}</strong><br>
        <strong style="color:red;">КЕЙИНГИ ИШ КУНИ УЧУН ТАХМИНИЙ ТАОМНОМА!</strong>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th class="col-1" rowspan="3"></th>
                <th class="col-2" rowspan="3">Махсулотлар номи</th>
                @php $col = 0; @endphp
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        @php $col++; @endphp
                        <th class='vrt-header product-column' style="height: 100px;">{{ implode(' ', array_slice(explode(' ', $product['product_name']), 0, 6)) }}</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <th style="font-size: 5px;">{{ $product['size_name'] }}</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <th style="font-size: 5px;">{{ $product['div'] }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $boolmeal = []; @endphp
            @foreach($menuitem as $row)
                @foreach($row as $item)
                    @if($loop->index == 0)
                        @continue
                        @php $time = $item['mealtime']; @endphp
                    @endif
                    <tr>
                        @if($loop->index == 1)
                        <th class="vrt-header meal-time" rowspan="{{ 2 * (count($row)-1) }}">{{ $row[0]['mealtime'] }}</th>
                        @endif
                        <td class="food-name" rowspan="2" style="width: 60px; font-size: 7px;">{{ $item['foodname'] }}</td>
                        @foreach($products as $product)
                            @if(isset($product['yes']) && isset($item[$product['id']]))
                                <td style="background-color: #e6f3ff;">
                                    {{ number_format((($menu[0]['kingar_children_number'])*$item[$product['id']]) / $product['div'], 2) }}
                                </td>
                            @elseif(isset($product['yes']))
                                <td style="background-color: #e6f3ff;"></td>
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($products as $product)
                            @if(isset($product['yes']) && isset($item[$product['id']]))
                                <td>{{ $item[$product['id']] }}</td>
                            @elseif(isset($product['yes']))
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
            
            <!-- Bolalar bo'limi -->
            <tr class="children-section">
                <th class="vrt-header col-product" rowspan="5">Болалар</th>
                <td class="summary-row">Жами миқдори</td>
                @foreach($products as $product)
                    @if(isset($product['yes']) && isset($productallcount[$product['id']]))
                        <td class="summary-row" style="background-color: #e6f3ff;">
                            {{ number_format((($menu[0]['kingar_children_number'])*$productallcount[$product['id']]) / $product['div'], 2) }}
                        </td>
                    @elseif(isset($product['yes']))
                        <td class="summary-row" style="background-color: #e6f3ff;"></td>
                    @endif
                @endforeach
            </tr>
            <tr class="children-section">
                <td>1 та бола учун гр</td>
                @foreach($products as $product)
                    @if(isset($product['yes']) && isset($productallcount[$product['id']]))
                        <td>{{ $productallcount[$product['id']] }}</td>
                    @elseif(isset($product['yes']))
                        <td></td>
                    @endif
                @endforeach
            </tr>
            <tr class="children-section">
                <td>Нархи</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td></td>
                    @endif
                @endforeach
            </tr>
            <tr class="children-section">
                <td class="summary-row"><strong>Сумма жами:</strong></td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="summary-row"></td>
                    @endif
                @endforeach
            </tr>
            <tr class="children-section">
                <td>Жами харажат</td>
                <td colspan="{{ $col }}">0</td>
            </tr>
            
            <!-- Xodimlar bo'limi -->
            <tr class="workers-section">
                <th class="vrt-header col-product" rowspan="5">Ходимлар</th>
                <td class="summary-row">Жами миқдори</td>
                @foreach($products as $product)
                    @if(isset($product['yes']) && isset($workerproducts[$product['id']]))
                        <td class="summary-row" style="background-color: #e6f3ff;">
                            {{ number_format((($menu[0]['workers_count'])*$workerproducts[$product['id']]) / $product['div'], 2) }}
                        </td>
                    @elseif(isset($product['yes']))
                        <td class="summary-row" style="background-color: #e6f3ff;"></td>
                    @endif
                @endforeach
            </tr>
            <tr class="workers-section">
                <td>1 та ходим учун гр</td>
                @foreach($products as $product)
                    @if(isset($product['yes']) && isset($workerproducts[$product['id']]))
                        <td>{{ $workerproducts[$product['id']] }}</td>
                    @elseif(isset($product['yes']))
                        <td></td>
                    @endif
                @endforeach
            </tr>
            <tr class="workers-section">
                <td>Нархи</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td></td>
                    @endif
                @endforeach
            </tr>
            <tr class="workers-section">
                <td class="summary-row"><strong>Сумма жами</strong></td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="summary-row"></td>
                    @endif
                @endforeach
            </tr>
            <tr class="workers-section">
                <td>Жами харажат</td>
                <td colspan="{{ $col }}">0</td>
            </tr>
            
            <!-- Umumiy jami -->
            <tr class="final-total">
                <th colspan="2"><strong>Жами махсулот миқдори</strong></th>
                @foreach($products as $product)
                    @if(isset($product['yes']) && isset($productallcount[$product['id']]))
                        <td class="final-total">
                            {{ number_format(($productallcount[$product['id']]*$menu[0]['kingar_children_number']+$workerproducts[$product['id']]*$menu[0]['workers_count'])/$product['div'], 2) }}
                        </td>
                    @elseif(isset($product['yes']))
                        <td class="final-total"></td>
                    @endif
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>