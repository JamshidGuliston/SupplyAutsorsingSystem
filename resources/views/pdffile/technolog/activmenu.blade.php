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
		@page { margin: 0.3in 0.8in 0in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size: 7.5px;
			background-image: url(images/bg.jpg);
			background-position: top left;
			background-repeat: no-repeat;
			background-size: 100%;
			/* padding: 300px 100px 10px 100px; */
			width:100%;
		}
		.column {
			float: left;
			text-align: center;
			width: 33%;
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
			text-align: center;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			max-width: 90px;
		}
		
		/* Maxsulot nomlari uchun */
		.product-name-short {
			font-size: 8px;
			line-height: 1.2;
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
		
		/* Qatorlarni ajratish uchun ranglar - faqat ma'lumot qatorlari uchun */
		tbody tr:nth-child(odd) {
			background-color: #f5f5f5; /* Och kulrang */
		}
		tbody tr:nth-child(even) {
			background-color: #ffffff; /* Oq rang */
		}
		
		/* Maxsulotlar va taom nomlari uchun oq rang */
		thead tr,
		thead th,
		thead td {
			background-color: #ffffff !important; /* Oq rang */
		}
		
		/* Ma'lumot qatorlarida maxsulot nomlari ustunini oq qoldirish */
		td:first-child,
		td:nth-child(2),
		td:nth-child(3) {
			background-color: #ffffff !important; /* Oq rang */
		}
		
		/* Xulosa qatorlari uchun oq rang */
		tr:has(th[scope="row"]) {
			background-color: #ffffff !important;
		}
		
		tr:has(th[scope="row"]) td {
			background-color: #ffffff !important;
		}
	</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">
                	<?php
						echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b><br/>";
                		echo  'Cана: <b>'.$day['day_number'].'.'.$day['month_name'].' '.$day['year_name'].'й.</b>    <b>           ' . $menu[0]['age_name'] . "</b>ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
                		if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
                			echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
                		}
                	?>
                    <table style="width:100%; table-layout: fixed;">
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
                          	 			<span class="product-name-short"><?php echo $first.' '.$second.' '.$third; ?></span>
                          	 		</th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							$boolmeal = [];
                        	@foreach($menuitem as $row)
								@foreach($row as $item)
								@if($loop->index == 0)
									@continue;
									<?php $time = $item['mealtime']; ?>
								@endif
			                        <tr>
			                        	@if($loop->index == 1)
												<th scope="row" rowspan="<?php echo 2 * (count($row)-1); ?>" class='vrt-header' style="padding: 0px; height: 60px;"><?php echo '<span>'. $row[0]['mealtime'] .'</span>'; ?></th>
			                            @endif
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 2px;"><?php echo $item['foodname'] ?></td>
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 0px;"><?php echo $item['foodweight'] ?></td>
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
                    				<tr>
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
									<tr>
										<!-- <th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th> -->
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px; border-top: 1px solid black">{{ $menu[0]['age_name'].'ли'  }} бир бола учун гр</td>
										<?php
										$total_weight = [];

			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 1px solid black"><?= $productallcount[$products[$t]['id']]; ?></td>
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
			                            	<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f", (($menu[0]['kingar_children_number'])*$productallcount[$products[$t]['id']]) / $products[$t]['div'] ); ?></td>
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

									<tr style="border-top: 1px solid black;">
										<td scope="row" colspan="3" class="align-baseline" style="padding: 0px; border-top: 1px solid black">1 та ходим учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 1px solid black"><?= $workerproducts[$products[$t]['id']]; ?></td>
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
			                            	<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f", (($menu[0]['workers_count'])*$workerproducts[$products[$t]['id']]) / $products[$t]['div']); ?></td>
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
												<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f", $total_weight[$products[$t]['id']]); ?></td>
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
							<div class="column">
								<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
							</div>
							<div class="column">
								<p style="text-align: center;"><strong> Технолог:</strong> __________________;</p>
							</div>
							<div class="column">
								<p style="text-align: right;"><strong>Бош ошпаз: </strong> __________________;</p>
							</div>
					   </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>