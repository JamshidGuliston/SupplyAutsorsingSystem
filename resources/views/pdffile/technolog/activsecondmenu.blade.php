<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="Description" content="Enter your description here"/>
	 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
	<title>Title</title>
	<style>
		@page { margin: 0.3in 0.1in 0in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size: 8.5px;
			background-color: white;
			background-image: url(images/bg.jpg);
			background-position: top left;
			background-repeat: no-repeat;
			background-size: 100%;
			/* padding: 300px 100px 10px 100px; */
			width:100%;
		}
		/* Create two equal columns that floats next to each other */
		.column {
			float: left;
			text-align: center;
			width: 33%;
		}

		.tabassumfooter{
			float: left;
			text-align: center;
			width: 50%;
		}

		/* Clear floats after the columns */
		.row:after {
			content: "";
			display: table;
			clear: both;
		}
		/* .row{
			display: flex;
			justify-content: space-between;
		} */
		table{
			border-collapse: collapse;
			border: 1px solid black;
			width: 100%;
			background-color: white;
		}
		thead{
			border: 1px solid black;
			background-color: white;
		}
		td {
			text-align: center;
			width: auto;
			overflow: hidden;
			word-wrap: break-word;
			background-color: white;
		}
		th{
			border: 1px solid black;
			padding: 0px;
			background-color: white;
		}
		td{
			border: 1px solid black;
			padding: 0px;
			background-color: white;
		}
		.vrt-header span{
			display: inline-block;
			text-align: center;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);
			white-space: nowrap;
			font-size: 5px;
			line-height: 1.1;
		}
		
		/* Ikki qatorlik yozish uchun */
		.vrt-header .two-line {
			display: inline-block;
			text-align: center;
			transform: rotate(-90deg);
			white-space: nowrap;
			width: 100%;
		}
		
		.vrt-header .two-line .line1 {
			font-size: 5px;
			line-height: 1.0;
			display: block;
			margin-bottom: 1px;
			white-space: nowrap;
		}

		.vrt-header .two-line .line2 {
			font-size: 5px;
			line-height: 1.0;
			display: block;
			white-space: nowrap;
		}
		
		/* Yacheyka balandligini oshirish */
		.vrt-header {
			height: 40px !important;
			min-height: 40px;
			vertical-align: middle;
		}
		.vrt-header-weight span{
			display: inline-block;
			text-align: center;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);
			white-space: nowrap;
			font-size: 5px;
			line-height: 1.0;
			display: block;
			white-space: nowrap;
		}
		.vrt-header-weight {
			height: 20px !important;
			width: 10px !important;
			min-height: 15px;
			vertical-align: middle;
		}
	</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
				<table style="border: none !important; width: 98%;">
					<tbody>
						<tr>
							<td style="text-align: left; border: none !important; width: 33%;">
								<div style="font-size: 12px; font-weight: bold; margin-bottom: 5px;">
									Боғча номи: <span style="color: #2c3e50;">{{ $menu[0][0]['kingar_name'] }}</span>
								</div>
								<div style="font-size: 11px;">
									Сана: <span style="color: #2c3e50; font-weight: bold;">{{ $day['day_number'].'.'.$day['month_name'].' '.$day['year_name'] }}й.</span>
								</div>
							</td>
							<td style="text-align: center; border: none !important; width: 33%;">
								<div style="font-size: 11px; margin-bottom: 3px;">
								<b>Ташкилот:</b> <span style="color: #2c3e50;"><b>{{ env('COMPANY_NAME') }}</b></span>
								</div>
							</td>
							<td style="text-align: right; border: none !important; width: 33%;">
								<?php
									$workers = 0;
									$countch = [];
									$workers = $menu[0][0]['workers_count'];
									
									foreach($menu as $row) {
										if(!isset($countch[$row[0]['king_age_name_id']])){
											$countch[$row[0]['king_age_name_id']] = 0;
										}
										$countch[$row[0]['king_age_name_id']] = $row[0]['kingar_children_number'];
									}
								?>
								<div style="font-size: 11px; margin-bottom: 3px;">
									Ходимлар сони: <span style="color: #e74c3c; font-weight: bold;">{{ $workers }}</span>
								</div>
								@foreach($menu as $row)
									<div style="font-size: 11px; margin-bottom: 2px;">
										{{ $row[0]['age_name'] }}ли болалар сони: <span style="color: #27ae60; font-weight: bold;">{{ $row[0]['kingar_children_number'] }}</span>
									</div>
								@endforeach
							</td>
						</tr>
					</tbody>
				</table>
                <div class="table" id="table_with_data">
                    <table style="width:100%; table-layout: auto; border: 1px solid black !important;">
                        <thead>
                          <tr>
                          	 <th style="width:3px; height: 3px;"></th>
                          	 <th style="width:100px; min-width: 80px;">Махсулотлар номи</th>
                          	 <th class='vrt-header' style="width:2px; height: 2px"><?php echo '<span>Таом вазни</span>';?></th>
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 <?php 
								 	$col++;
									$shortname=substr($product['product_name'],0,21);
								?>
                          	 		<th class='vrt-header' style="padding: 0px; max-width: 20px; height: 40px">
                          	 	<?php 
                          	 		$productName = $product['product_name'];
                          	 		$words = explode(' ', $productName);
                          	 		if (count($words) > 2) {
                          	 			// So'zlarni ikki qatorga taqsimlash
                          	 			$mid = ceil(count($words) / 2);
                          	 			$line1 = implode(' ', array_slice($words, 0, $mid));
                          	 			$line2 = implode(' ', array_slice($words, $mid));
                          	 			echo '<span class="two-line"><div class="line1">'.$line1.'</div><div class="line2">'.$line2.'</div></span>';
                          	 		} else {
                          	 			echo '<span>'.$productName.'</span>';
                          	 		}
                          	 	?>
                          	 </th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							<?php
								$oneEater = [];
							?>
							
                        	@foreach($menuitem as $mkey => $mealtime)
								@if($mkey == "mealtime")
									@continue;
								@endif
								@foreach($mealtime as $fkey => $food)
									@if($fkey == "rows" or $fkey == "mealtime")
										@continue;
									@endif
									<tr style="background-color: rgb(236, 243, 243)">
										@if($loop->index == 0)
										<td scope="row" rowspan="<?php echo $mealtime['rows']; ?>" class='vrt-header'  style="padding: 0px; height: 15px;"><?php echo '<span><b>'. $mealtime['mealtime'] .'</b></span>'; ?></td>
										@endif
										<td class="" style="padding-left: 4px; text-align:left"><?php echo $food['foodname'] ?></td>
										<td rowspan="{{ count($food)-2 }}" class='vrt-header-weight'  style="padding: 0px; font-size: 5px"><?php echo '<span>'.$food['foodweight'].'</span>'; ?></td>
										<?php
										for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($food['product'][$products[$t]['id']])){
										?>
												<td style="padding: 0px;">{{ round($food['product'][$products[$t]['id']], 2); }}</td>
										<?php
											}
											elseif(isset($products[$t]['yes'])){
										?>
												<td style="padding: 0px;"></td>
										<?php	
											}
										}
										?>
									</tr>
									@foreach($food as $akey => $age)
										@if (is_numeric($akey))
										<tr>
											<td scope="row" class="align-baseline" style="padding: 0px;"><?php echo $age['age_name'] ?></td>
											<!-- <td scope="row" class="align-baseline" style="padding: 0px;"></td> -->
											@foreach($products as $product)
											@if(isset($product['yes']) and isset($age[$product['id']]))
											<?php
													if(!isset($oneEater[$akey])){
														$oneEater[$akey] = array_fill(1, 500, 0);
														$oneEater[$akey]['age_name'] = $age['age_name'];
														$oneEater[$akey]['number'] = $countch[$akey];
													}
													$oneEater[$akey][$product['id']] += $age[$product['id']]['one'];
											?>	
													<td style="padding: 0px;">{{ $age[$product['id']]['one'] }}</td>
												@elseif(isset($product['yes']))
													<td style="padding: 0px;"></td>
											@endif
											@endforeach
										</tr>
										@endif
									@endforeach
								@endforeach
							@endforeach
							@foreach($oneEater as $key => $value)
							<tr>
                                <th scope="row" rowspan="8" class='vrt-header' style="padding: 0px; border-top: solid black"><span>{{ $value['age_name'] }}</span></th>
								<td style="padding: 0px; font-size: 7px;">1 та бола учун гр</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								@foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
											<td style="padding: 0px; font-size: 7px;"><?= round($value[$product['id']], 2); ?></td>
								@endif
								@endforeach
							</tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Жами микдори</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								@foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
												<td style="padding: 0px; font-size: 7px;"><?= round(($value[$product['id']] * $value['number']) / $product['div'], 2); ?></td>
								@endif
								@endforeach
							</tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Нархи</td>
                                <td></td>
                                @foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
											<td style="padding: 0px; font-size: 7px;"><?= round($narx[$product['id']], 2); ?></td>
								@endif
								@endforeach
                            </tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Суммаси</td>
								<td></td>
								@foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
											<td style="padding: 0px; font-size: 7px;"><?= round(($value[$product['id']] * $value['number']) / $product['div'] * $narx[$product['id']], 2); ?></td>
								@endif
								@endforeach
                            </tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Хокимят нархида жами харажат</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								<?php
									$summa = 0;
								?>
								@foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
								<?php
									$summa += ($value[$product['id']] * $value['number']) / $product['div'] * $narx[$product['id']];
								?>
								@endif
								@endforeach
								 <td colspan="{{ $col }}" style="padding: 0px; font-size: 7px;"><?= round($summa, 2); ?></td>
                            </tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Хокимят нархида 1 бола харажати</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								<?php
									$summa = 0;
								?>
								@foreach($products as $product)
							 	@if(isset($product['yes']) and isset($value[$product['id']]))
								<?php
									$summa += ($value[$product['id']] * $value['number']) / $product['div'] * $narx[$product['id']];
								?>
								@endif
								@endforeach
								 <td colspan="{{ $col }}" style="padding: 0px; font-size: 7px;"><?= round($value['number'] > 0 ? $summa / $value['number'] : 0, 2); ?></td>
                            </tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Шартнома бўйича тасдиқланган 1 бола харажати</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								 <td colspan="{{ $col }}" style="padding: 0px; font-size: 7px;"><?= round($protsent->where('age_range_id', $key)->first()->eater_cost ?? 0, 2); ?></td>
                            </tr>
							<tr>
								<td style="padding: 0px; font-size: 7px;">Шартнома бўйича жами сарфланган маблаг</td>
								<td style="padding: 0px; font-size: 7px;"></td>
								 <td colspan="{{ $col }}" style="padding: 0px; font-size: 7px;"><?= round(($protsent->where('age_range_id', $key)->first()->eater_cost ?? 0) * $value['number'], 2); ?></td>
                            </tr>
							@endforeach
							<tr style="border-top: 2px solid black;">
								<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Ходимлар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та ходимга</td>
								<td style="padding: 0px; border-top: 2px solid black"></td>
								<?php
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"><?= $workerproducts[$products[$t]['id']]; ?></td>
								<?php	
									}
									elseif(isset($products[$t]['yes'])){
									?>
										<td style="padding: 0px;"></td>
									<?php	
									}
								}
								?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Жами миқдори</td>
								<td></td>
								<?php
								$chcost = 0;
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
										$chcost += ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'];
								?>
									<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f", ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div']); ?></td>
								<?php	
									}
									elseif(isset($products[$t]['yes'])){
									?>
										<td style="padding: 0px;"></td>
									<?php	
									}
								}
								?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Нархи</td>
								<td></td>
								<?php
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"><?php printf($narx[$products[$t]['id']]); ?></td>
								<?php	
									}
									elseif(isset($products[$t]['yes'])){
									?>
										<td style="padding: 0px;"></td>
									<?php	
									}
								}
								?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Суммаси</td>
								<td></td>
								<?php
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f", ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']]); ?></td>
								<?php	
									}
									elseif(isset($products[$t]['yes'])){
									?>
										<td style="padding: 0px;"></td>
									<?php	
									}
								}
								?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Cарфланган маблаг</td>
								<td></td>
								<?php
								$chcost = 0;
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
										$chcost += ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']];
									}
								}
								?>
								<td colspan="{{ $col }}" style="padding: 0px;"><?php printf("%01.3f", $chcost); ?></td>
							</tr>
                        </tbody>
                      </table>
                </div>
				<div class="row" style="margin-top: 15px;">
						<div class="column">
						@php
							$qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
						@endphp
						<img src="data:image/jpeg;base64,{{ $qrImage }}" 
							style="width:120; position:absolute; left:10px;">
						</div>
						<div class="column">
							<p style="text-align: center;"><strong> Бош ошпаз:</strong> __________________;</p>
						</div>
						<div class="column">
							@if(env('MENU_SIGN'))
								<p style="text-align: right;"><strong>ДМТТ директори: </strong> __________________;</p>
							@endif
						</div>
				</div>
            </div>
        </div>
    </div>
</body>
</html>