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
		@page { margin: 0.3in 0.3in 0in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size: 8.5px;
			background-image: url(images/bg.jpg);
			background-position: top left;
			background-repeat: no-repeat;
			background-size: 100%;
			/* padding: 300px 100px 10px 100px; */
			width:100%;
		}
		.row{
			display: flex;
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
			border-right: 1px dashed black;
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
		}
	</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
				<table style="border: none !important;">
					<tbody>
						<tr>
							<td style="text-align: left; border: none !important;">
							<?php
								echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b><br/>";
								echo  'sana: <b>"'.$day['day_number'].'".'.$day['month_name'].' 2022-й;</b>    <b>           ' . $menu[0]['age_name'] . "</b>ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
								if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
									echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
								}
							?>
							</td>
							<td style="text-align: right; border: none !important;">
								<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
							</td>
						</tr>
					</tbody>
				</table>
                <div class="table" id="table_with_data">
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;"></th>
                          	 <th style="width:12%;">Махсулотлар номи</th>
                          	 <th class='vrt-header' style="width:2%;"><?php echo '<span>Таом вазни</span>';?></th>
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 <?php $col++; ?>
                          	 		<th class='vrt-header' style="padding: 0px; width: 3%; height: 69px"><?php echo '<span>'.$product['product_name']. '</span>';?></th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							$boolmeal = [];
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
										<th scope="row" rowspan="<?php echo $mealtime['rows']; ?>" class='vrt-header' style="padding: 0px; height: 60px;"><?php echo '<span>'. $mealtime['mealtime'] .'</span>'; ?></th>
										@endif
										<th class="" style="padding: 0px; text-align:left"><?php echo $food['foodname'] ?></td>
										<td scope="row" class="align-baseline" style="padding: 0px;"><?php echo $food['foodweight'] ?></td>
										<?php
										for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and 0){
										?>
												<td style="padding: 0px;">{{ '.' }}</td>
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
										@if (is_numeric($akey)){
										<tr>
											<td scope="row" class="align-baseline" style="padding: 0px;"><?php echo $age['age_name'] ?></td>
											<td scope="row" class="align-baseline" style="padding: 0px;"></td>
											<?php
											for($t = 0; $t < count($products); $t++){
												if(isset($products[$t]['yes']) and isset($age[$products[$t]['id']])){
											?>	
													<td style="padding: 0px;">{{ $age[$products[$t]['id']]['one'] }}</td>
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
									@endforeach
								@endforeach
							@endforeach
							<tr>
								<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та бола учун гр</td>
								<td style="padding: 0px; border-top: 2px solid black"></td>
								<?php
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
								<!---->
									<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
								<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та бола учун гр</td>
								<td style="padding: 0px; border-top: 2px solid black"></td>
								<?php
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
								<!---->
									<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px;"></td>
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
                </div>
            </div>
        </div>
    </div>
</body>
</html>