<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="Description" content="Enter your description here"/>
	 <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
	<title>Title</title>
	<style>
		@page { margin: 0.3in 0.3in 0.2in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size: 7.5px;
			background-position: top left;
			background-repeat: no-repeat;
			background-size: 100%;
			/* padding: 300px 100px 10px 100px; */
			width:100%;
		}
		.column {
			float: left;
			text-align: center;
			width: 25%;
		}
		.column_top {
			float: left;
			text-align: center;
			width: 33.33%;
		}

		/* Clear floats after the columns */
		.row:after {
			content: "";
			display: table;
			clear: both;
		}
		.row{
			display: flex;
			flex-wrap: nowrap;
			justify-content: space-between;
		}
		table{
			border-collapse: collapse;
			border: 1px solid black;
			width: 100%;	
		}
		thead{
			border: 1px solid black;
		}
		td {
			text-align: center;
			width: auto;
			overflow: hidden;
			word-wrap: break-word;
		}
		th{
			border: 1px solid black;
			padding: 0px;
		}
		td{
			border-right: 1px solid black;
			border-bottom: 1px solid black;
			padding: 0px;
		}
		.vrt-header span{
			display: inline-block;
			text-align: left;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);

			white-space: nowrap;       /* so‘zlarni bir qatorda ushlab turadi */
			word-break: keep-all;      /* so'zlarni bo'lib tashlamaydi */
			line-height: 1;            /* ixchamlashtiradi */
			max-width: 90px;           /* kerak bo'lsa cheklash */
			overflow: hidden;          /* sig'masa kesadi */
			text-overflow: ellipsis;   /* sig'masa "..." qo'yadi */
		}
		
		/* Maxsulot nomlari uchun */
		.product-name-short {
			font-size: 6px;
			line-height: 1.0;
			
		}
		
		/* Qator balandligini kamaytirish */
		tr {
			height: 20px;
		}
		
		/* Maxsulot ustunlari uchun */
		.product-column {
			width: 2.5% !important;
			max-width: 2.5%;
			overflow: hidden;
		}
		
		/* Maxsulotlar va taom nomlari uchun oq rang */
		thead tr,
		thead th,
		thead td {
			background-color: #ffffff !important; /* Oq rang */
		}
	</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">
				@if(env('WORKERSFORMENU') == "true")
					<div class="row">
							<div class="column_top">
								<!-- ТАСДИҚЛАЙМАН -->
								<h5><b>ТАСДИҚЛАЙМАН</b></h5>
								<p style="text-align: center;">{{ $menu[0]['kingar_name'] }}</p>
								<p>Рахбари ______________________</p>
							</div>
							<div class="column_top">
							<?php
								echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b><br/>";
								echo "Таомнома: <b>".$menu[0]['menu_name']."</b><br/>";
								echo  'Cана: <b>'.$day['day_number'].'.'.$day['month_name'].' '.$day['year_name'].'й.</b><br/>  ' . $menu[0]['age_name'] . "ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
								if($workerfood[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
									echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
								}
							?>
							</div>
							<div class="column_top">
								<!-- ТАСДИҚЛАЙМАН -->
								<h5><b>ТАСДИҚЛАЙМАН</b></h5>
								<p style="text-align: center;">{{ env('company_name') }}</p>
								<p>Рахбари ______________________</p>
							</div>
					</div>
				@else
					<?php
						echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b><br/>";
						echo "Таомнома: <b>".$menu[0]['menu_name']."</b><br/>";
						echo  'Cана: <b>'.$day['day_number'].'.'.$day['month_name'].' '.$day['year_name'].'й.</b><br/>  ' . $menu[0]['age_name'] . "ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
						if(isset($workerfood[0]['worker_age_id']) and $workerfood[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
							echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
						}
					?>
				@endif
                    <table style="width:100%; table-layout: fixed; margin-top: 25px;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;"></th>
                          	 <th style="width:12%;">Махсулотлар номи</th>
                          	 <th class='vrt-header' style="width:2%;"><?php echo '<span>Таом вазни</span>';?></th>
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 @php
									$col++;
									$parts = explode(' ', $product['product_name']);
									$first = $parts[0];
									$second = isset($parts[1]) ? $parts[1] : '';
									$third = isset($parts[2]) ? $parts[2] : '';
									$fourth = isset($parts[3]) ? $parts[3] : '';
									$fifth = isset($parts[4]) ? $parts[4] : '';
									
									// Maxsulot nomini qisqartirish
									$shortName = $first;
									if($second && strlen($shortName . ' ' . $second) <= 16) {
										$shortName .= ' ' . $second;
									}
									if($third && strlen($shortName . ' ' . $third) <= 16) {
										$shortName .= ' ' . $third;
									}
									
									// Agar juda uzun bo'lsa, faqat birinchi so'zni olish
									if(strlen($shortName) > 8) {
										$shortName = $first;
									}
								@endphp
                          	 		<th class='vrt-header product-column' style="padding: 0px; height: 100px">
                          	 			<span class="product-name-short"><?php echo $first.' '.$second.' '.$third.' '.$fourth.' '.$fifth; ?></span>
                          	 		</th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							<?php $row_counter = 0; ?>
                        	@foreach($menuitem as $row)
								@foreach($row as $item)
								@if($loop->index == 0)
									@continue;
									<?php $time = $item['mealtime']; ?>
								@endif
								<?php 
									$row_counter++; 
									$bg_color = ($row_counter % 2 == 1) ? 'background-color: rgb(226, 253, 255);' : 'background-color: #ffffff;';
								?>
			                        <tr style="{{ $bg_color }}">
			                        	@if($loop->index == 1)
												<th scope="row" rowspan="<?php echo 2 * (count($row)-1); ?>" class='vrt-header' style="padding: 0px; height: 60px; background-color: #ffffff;"><?php echo '<span>'. $row[0]['mealtime'] .'</span>'; ?></th>
			                            @endif
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 2px; background-color: #ffffff;"><?php echo $item['foodname'] ?></td>
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 0px; background-color: #ffffff;"><?php echo $item['foodweight'] ?></td>
			                            <?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(isset($products[$t]['yes']) and isset($item[$products[$t]['id']])){
			                            ?>
			                            		<td style="padding: 0px;">{{ $item[$products[$t]['id']] }}</td>
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
								<?php 
									$row_counter++; 
									$bg_color = ($row_counter % 2 == 1) ? 'background-color: rgb(226, 253, 255);' : 'background-color: #ffffff;';
								?>
                    				<tr style="{{ $bg_color }}">
			                        	<?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(isset($products[$t]['yes']) and isset($item[$products[$t]['id']])){
			                            ?>	
			                            		<td style="padding: 0px;"><?php printf("%01.3f", (($menu[0]['kingar_children_number'])*$item[$products[$t]['id']]) / $products[$t]['div']); ?></td>
			                            	
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
								
								@endforeach
							@endforeach
									<tr style="border-top: 2px solid black;">
										<!-- <th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th> -->
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px; border-top: 1px solid black">{{ $menu[0]['age_name'].'ли'  }} бир бола учун гр</td>
										<?php
										$total_weight = [];

			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; border-top: 1px solid black"><?= $productallcount[$products[$t]['id']]; ?></td>
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
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px;">Жами миқдори(кг,хис)</td>
										<?php
										for($t = 0; $t < count($products); $t++){
											if(!isset($total_weight[$products[$t]['id']])){
												$total_weight[$products[$t]['id']] = 0;
											}
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
												$total_weight[$products[$t]['id']] += (($menu[0]['kingar_children_number'])*$productallcount[$products[$t]['id']]) / $products[$t]['div'];
			                            ?>
			                            <!---->
			                            	<td style="padding: 0px"><?php printf("%01.3f", (($menu[0]['kingar_children_number'])*$productallcount[$products[$t]['id']]) / $products[$t]['div'] ); ?></td>
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
								@if(env('WORKERSFORMENU') == "true")
									<tr style="border-top: 1px solid black;">
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px; border-top: 1px solid black">1 та ходим учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; border-top: 1px solid black"><?= $workerproducts[$products[$t]['id']]; ?></td>
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
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px;">Жами миқдори (кг.хис)</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(!isset($total_weight[$products[$t]['id']])){
												$total_weight[$products[$t]['id']] = 0;
											}
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
												$total_weight[$products[$t]['id']] += (($menu[0]['workers_count'])*$workerproducts[$products[$t]['id']]) / $products[$t]['div'];
											}
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px"><?php printf("%01.3f", (($menu[0]['workers_count'])*$workerproducts[$products[$t]['id']]) / $products[$t]['div']); ?></td>
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
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px;">Жами сарфланган махулот миқдори</td>
										<?php
										for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($total_weight[$products[$t]['id']])){
											?>
												<td style="padding: 0px"><?php printf("%01.3f", $total_weight[$products[$t]['id']]); ?></td>
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
								@endif
                        </tbody>
                      </table>
					  <div class="row" style="margin-top: 15px;">
							<div class="column">
								<p style="text-align: center;"><strong> Бир нафар {{ $menu[0]['age_name'] }}ли бола учун:</strong> <?php echo number_format($protsent->eater_cost, 0, ',', ' '); ?> so'm</p>
							</div>
							<div class="column">
								<p style="text-align: center;"><strong> Жами сарфланган сумма:</strong> {{  number_format($menu[0]['kingar_children_number']*$protsent->eater_cost, 0, ',', ' '); }}</p>
							</div>
							<div class="column">
								
							</div>
					   </div>
                       <div class="row" style="margin-top: 15px;">
					   @if(env('WORKERSFORMENU') == "true")
					   		<div class="column">
								<p style="text-align: center;"><strong> Технолог:</strong> __________________;</p>
							</div>
							<div class="column">
								<p style="text-align: center;"><strong> Бухгалтер:</strong> __________________;</p>
							</div>
						@else
							<div class="column">
								@php
									$qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
								@endphp
								<img src="data:image/jpeg;base64,{{ $qrImage }}" 
									style="width:120; position:absolute; left:10px;">
							</div>
						@endif
							<div class="column">
								<p style="text-align: center;"><strong> Хамшира:</strong> __________________;</p>
							</div>
							<div class="column">
								<p style="text-align: right;"><strong>{{ env('MENU_SIGNATURE') }}: </strong> __________________;</p>
							</div>
					   </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>