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
            font-size: 10px;
            line-height: 1.3;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            table-layout: fixed;
        }
        
        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }
        
        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }
        
        .main-table td {
            font-size: 10px;
        }
        
        /* Vertikal matn uchun Snappy-optimized CSS */
        .vertical-text {
            display: block;
            width: 20px;
            height: 80px;
            font-size: 10px;
            line-height: 1.1;
            text-align: center;
            word-wrap: break-word;
            overflow: hidden;
            white-space: normal;
        }
        
        .vertical-text span {
            display: block;
            transform: rotate(-360deg);
            transform-origin: center;
            width: 80px;
            height: 20px;
            margin-top: 30px;
            font-size: 10px;
            line-height: 1.1;
            text-align: center;
        }
        
        .product-name {
			font-size: 8px;
            font-weight: bold;
            padding: 3px;
        }
        
        .meal-time {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            font-weight: bold;
            background-color: #e0e0e0;
        }
        
        .food-name {
			width: 80px;
            text-align: left;
            padding: 2px;
            font-size: 10px;
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
        .col-2 { width: 8%; font-size: 10px; }
        .col-product { width: 20px; }
        
        /* Alternativ vertikal matn usuli */
        .vertical-header {
			width: 20px;      /* ustun eni */
			height: 80px;     /* ustun balandligi */
			position: relative;
		}

		.vertical-header-content {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%) rotate(-90deg);
			transform-origin: center;
			white-space: nowrap;
			text-align: center;
		}

		.vertical-text {
			writing-mode: vertical-rl;   /* matnni vertikal qiladi (yuqoridan pastga) */
			  /* teskari qilib pastdan tepaga chiqaradi */
			transform-origin: center center;
			text-align: center;
			white-space: nowrap;
			display: inline-block;
		}


    </style>
</head>
<body>
    <div class="header-info">
        <strong>Боғча номи: {{ $menu[0]['kingar_name'] }}</strong> | 
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
                        <th class="col-product">
							<div class="vertical-text">
								{{ implode(' ', array_slice(explode(' ', $product['product_name']), 0, 2)) }}
							</div>
						</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <th style="font-size: 6px;">{{ $product['size_name'] }}</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                @foreach($products as $product)
                    @if(isset($product['yes']))
                        <th style="font-size: 6px;">{{ $product['div'] }}</th>
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
                            <th class="col-product meal-time" rowspan="{{ 2 * (count($row)-1) }}">
								<div class="vertical-text">
									{{ $row[0]['mealtime'] }}
								</div>
							</div>
						</th>
                        @endif
                        <td class="food-name" rowspan="2" style="width: 40px;">{{ $item['foodname'] }}</td>
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
                <th class="meal-time col-product" rowspan="5">
					<div class="vertical-text">
						Болалар
					</div>
				</th>
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
                <th class="meal-time col-product" rowspan="5">
					<div class="vertical-text">
						Ходимлар
					</div>
				</th>
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