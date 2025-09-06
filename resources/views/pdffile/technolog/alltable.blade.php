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
        }
        
        .header-info {
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        table {
            border-collapse: collapse;
            border: 2px solid black;
            width: 100%;
            table-layout: fixed;
        }
        
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        /* Mahsulotlar nomi ustuni uchun - 30% qisqartirilgan */
        .product-name-column {
            width: 56px; /* 80px dan 56px ga (30% qisqartirilgan) */
            font-size: 7px;
            text-align: left;
            padding: 2px;
            word-wrap: break-word;
            word-break: break-word;
        }
        
        /* Vertikal matn uchun maxsus CSS */
        .vertical-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            height: 120px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            line-height: 1.0;
            word-break: break-all;
            overflow: hidden;
        }
        
        /* Snappy uchun alternativ yechim */
        .vertical-text-alt {
            transform: rotate(-90deg);
            transform-origin: center;
            white-space: nowrap;
            height: 120px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            line-height: 1.0;
            word-break: break-all;
            overflow: hidden;
        }
        
        /* Maxsulot nomlari uchun */
        .product-header {
            width: 25px;
            height: 120px;
            padding: 0;
            vertical-align: middle;
        }
        
        .product-name {
            font-size: 6px;
            line-height: 1.0;
            word-break: break-word;
            hyphens: auto;
            text-align: center;
            padding: 1px;
        }
        
        /* Ovqat vaqti uchun vertikal matn */
        .meal-time-header {
            width: 30px;
            height: 60px;
            padding: 0;
            vertical-align: middle;
        }
        
        .meal-time-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            height: 60px;
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Bolalar va xodimlar uchun */
        .section-header {
            width: 30px;
            height: 100px;
            padding: 0;
            vertical-align: middle;
        }
        
        .section-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            height: 100px;
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Jadval qatorlari */
        .food-name {
            width: 56px; /* 80px dan 56px ga (30% qisqartirilgan) */
            font-size: 7px;
            text-align: left;
            padding: 2px;
            word-wrap: break-word;
            word-break: break-word;
        }
        
        .quantity-cell {
            width: 25px;
            font-size: 6px;
            text-align: center;
            padding: 1px;
        }
        
        /* Qalin chiziqlar */
        .thick-border-top {
            border-top: 2px solid black;
        }
        
        .thick-border-bottom {
            border-bottom: 2px solid black;
        }
    </style>
</head>
<body>
    <div class="header-info">
        <strong>Боғча номи: {{ $menu[0]['kingar_name'] }}</strong><br>
        <strong>Сана: {{ $day['day_number'] }}.{{ $day['month_name'] }} {{ $day['year_name'] }}й</strong><br>
        <strong>{{ $menu[0]['age_name'] }}ли болалар сони: {{ $menu[0]['kingar_children_number'] }}</strong>
        @if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id'])
            <strong>Ходимлар сони: {{ $menu[0]['worker_count'] }}</strong>
        @endif
        <br><strong>КЕЙИНГИ ИШ КУНИ УЧУН ТАХМИНИЙ ТАОМНОМА!</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;"></th>
                <th class="product-name-column">Махсулотлар номи</th>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <th class="product-header">
                            <div class="vertical-text-alt">
                                {{ $product['product_name'] }}
                            </div>
                        </th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($menuitem as $mealTimeId => $mealTimeData)
                @foreach($mealTimeData as $foodId => $foodData)
                    @if($loop->index == 0)
                        @continue
                    @endif
                    
                    @if($loop->index == 1)
                        <tr>
                            <th class="meal-time-header" rowspan="{{ 2 * (count($mealTimeData) - 1) }}">
                                <div class="meal-time-text">
                                    {{ $mealTimeData[0]['mealtime'] }}
                                </div>
                            </th>
                            <td class="food-name" rowspan="2">{{ $foodData['foodname'] }}</td>
                            @foreach($products as $product)
                                @if(isset($product['yes']))
                                    <td class="quantity-cell">
                                        @if(isset($foodData[$product['id']]))
                                            {{ $foodData[$product['id']] }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($products as $product)
                                @if(isset($product['yes']))
                                    <td class="quantity-cell">
                                        @if(isset($foodData[$product['id']]))
                                            {{ number_format(($menu[0]['kingar_children_number'] * $foodData[$product['id']]) / $product['div'], 3) }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @else
                        <tr>
                            <td class="food-name" rowspan="2">{{ $foodData['foodname'] }}</td>
                            @foreach($products as $product)
                                @if(isset($product['yes']))
                                    <td class="quantity-cell">
                                        @if(isset($foodData[$product['id']]))
                                            {{ $foodData[$product['id']] }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($products as $product)
                                @if(isset($product['yes']))
                                    <td class="quantity-cell">
                                        @if(isset($foodData[$product['id']]))
                                            {{ number_format(($menu[0]['kingar_children_number'] * $foodData[$product['id']]) / $product['div'], 3) }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            @endforeach
            
            <!-- Bolalar bo'limi -->
            <tr class="thick-border-top">
                <th class="section-header" rowspan="5">
                    <div class="section-text">Болалар</div>
                </th>
                <td class="food-name thick-border-top">1 та бола учун гр</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell thick-border-top">
                            @if(isset($productallcount[$product['id']]))
                                {{ $productallcount[$product['id']] - $workerproducts[$product['id']] }}
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Жами миқдори</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell">
                            @if(isset($productallcount[$product['id']]))
                                {{ number_format(($menu[0]['kingar_children_number'] * $productallcount[$product['id']]) / $product['div'], 3) }}
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Нархи</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name"><strong>Сумма жами:</strong></td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Жами харажат</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <!-- Xodimlar bo'limi -->
            <tr class="thick-border-top">
                <th class="section-header" rowspan="5">
                    <div class="section-text">Ходимлар</div>
                </th>
                <td class="food-name thick-border-top">1 та ходим учун гр</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell thick-border-top">
                            @if(isset($workerproducts[$product['id']]))
                                {{ $workerproducts[$product['id']] }}
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Жами миқдори</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell">
                            @if(isset($workerproducts[$product['id']]))
                                {{ number_format(($menu[0]['workers_count'] * $workerproducts[$product['id']]) / $product['div'], 3) }}
                            @endif
                        </td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Нархи</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name"><strong>Сумма жами</strong></td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <td class="food-name">Жами харажат</td>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <!-- Yakuniy qatorlar -->
            <tr class="thick-border-top">
                <th colspan="2" class="food-name thick-border-top"><strong>Жами махсулот оғирлиги</strong></th>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell thick-border-top"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <th colspan="2" class="food-name">Жами сарфланган маблағ</th>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell">0</td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <th colspan="2" class="food-name">1 нафар бола учун</th>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
            
            <tr>
                <th colspan="2" class="food-name">1 нафар ходим учун</th>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <td class="quantity-cell"></td>
                    @endif
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>