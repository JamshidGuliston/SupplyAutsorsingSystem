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
            font-size: 10px;
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
            border: 1px solid #000;        /* 1px → 1.5px */
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
        }
        
        .main-table th {
            font-weight: bold;
            font-size: 10px;       /* 2px → 1.5px (barcha borderlar bir xil) */
        }
        
        .main-table td {
            font-size: 10px;
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
			font-size: 8px;
            font-weight: bold;
            text-align: left;
            padding: 3px;
        }
        
        .meal-time {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-weight: bold;
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
        
        /* Mahsulot nomlari uchun - MARKAZGA joylashtirilgan */
        .vrt-header {
			vertical-align: middle;
			text-align: center;
			position: relative;
			padding: 5px 0;
		}
		
		.vrt-header span{
			display: inline-block;
			text-align: center;           /* MARKAZ! */
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);
			transform-origin: center center;  /* MARKAZ! */

			word-break: keep-all;
			line-height: 1.2;
			max-width: 95px;
			overflow: hidden;
			text-overflow: ellipsis;
			
			/* Markazga joylash uchun */
			position: relative;
			left: 0;
			right: 0;
			margin: auto;
		}
		.product-name-short {
			font-size: 9px;
			line-height: 1.2;
			font-weight: bold;
		}
		
		/* Maxsulot ustunlari uchun */
		.product-column {
			width: 2.5% !important;
			max-width: 2.5%;
			overflow: visible;
			height: 100px;
			min-height: 100px;
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
                        <th class='vrt-header product-column' style="padding: 0px; height: 100px">
                        <span class="product-name-short">{{ implode(' ', array_slice(explode(' ', $product['product_name']), 0, 6)) }}</span>
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
                        <th class="vrt-header meal-time" rowspan="{{ 2 * (count($row)-1) }}">
								<span class="product-name-short">{{ $row[0]['mealtime'] }}</span>
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
                <th class="vrt-header col-product" rowspan="5">
					<span class="product-name-short">
						Болалар
					</span>
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
                <th class="vrt-header col-product" rowspan="5">
					<span class="product-name-short">
						Ходимлар
					</span>
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