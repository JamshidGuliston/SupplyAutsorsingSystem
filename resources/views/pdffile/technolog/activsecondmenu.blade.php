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
							<?php
								$workers = 0;
							?>
							@foreach($menu as $row)
								<td style="text-align: left; border: none !important;">
								@if($loop->index == 0)
									Боғча номи: <b>{{ $row[0]['kingar_name']; }}</b><br/>sana: <b>{{ $day['day_number'].'.'.$day['month_name'] }}.2022-й;</b><b>
								@endif
								<?php
									echo  $row[0]['age_name'] . "</b>ли болалар сони: <b>" . $row[0]['kingar_children_number'].";</b>";
									if($row[0]['worker_age_id'] == $row[0]['king_age_name_id']){
										$workers = $row[0]['workers_count'];
										echo "  ходимлар сони: <b>".$row[0]['workers_count'].";</b>  ";	
									}
								?>
							</td>
							@endforeach
							<!-- <td style="text-align: right; border: none !important;">
								<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
							</td> -->
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
                          	 		<th class='vrt-header' style="padding: 0px; width: 4%; height: 69px"><?php echo '<span>'.$product['product_name']. '</span>';?></th>
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
										<th class="" style="padding-left: 4px; text-align:left"><?php echo $food['foodname'] ?></td>
										<th class='vrt-header' rowspan="{{ count($food)-2 }}" style="padding: 0px; font-family: 5px"><?php echo '<span>'.$food['foodweight'].'</span>'; ?></td>
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
										@if (is_numeric($akey)){
										<tr>
											<td scope="row" class="align-baseline" style="padding: 0px;"><?php echo $age['age_name'] ?></td>
											<!-- <td scope="row" class="align-baseline" style="padding: 0px;"></td> -->
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
								<th scope="row" rowspan="4" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">Жами миқдор</td>
								<td style="padding: 0px; border-top: 2px solid black"></td>
								<?php
								// $narx = [];
								// $narx[1] = 0; $narx[2] = 12000; $narx[3] = 0; $narx[4] = 14000; $narx[10] = 60000; $narx[12] = 11000; $narx[13] = 0; $narx[14] = 2000; $narx[15] = 0; $narx[16] = 9000; $narx[17] = 0;
								// $narx[18] = 0; $narx[19] = 0; $narx[21] = 69000; $narx[22] = 20000; $narx[23] = 2400; $narx[24] = 50000; $narx[25] = 0; $narx[26] = 23000; $narx[27] = 6000; $narx[28] = 30000; $narx[29] = 5000;
								// $narx[30] = 1300; $narx[31] = 15000; $narx[32] = 60000; $narx[33] = 9000; $narx[34] = 100000; $narx[35] = 19000; $narx[36] = 25000; $narx[37] = 18000; $narx[38] = 44000; $narx[39] = 17000;
								// $narx[40] = 22000; $narx[41] = 13000; $narx[42] = 28000; $narx[43] = 5000; $narx[44] = 5000; $narx[45] = 12000; $narx[46] = 70000; $narx[47] = 55000; $narx[48] = 13000; $narx[49] = 5000;
								// $narx[50] = 20000; $narx[51] = 22000; $narx[52] = 5000; $narx[53] = 8000; $narx[54] = 8000; $narx[55] = 19000; $narx[56] = 9000; $narx[57] = 14000; $narx[58] = 15000; $narx[59] = 20000;
								// $narx[60] = 2400; $narx[61] = 0; 
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
								?>
									<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"><?= round($productallcount[$products[$t]['id']], 2); ?></td>
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
								<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами:</b></td>
								<td></td>
								<?php
								$chcost = 0;
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
										$chcost += $productallcount[$products[$t]['id']] * $narx[$products[$t]['id']];
								?>
									<td style="padding: 0px; font-size: 5px"><?= round($productallcount[$products[$t]['id']] * $narx[$products[$t]['id']]); ?></td>
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
								<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>"><?php printf("%01.2f", $chcost); ?></td>
							</tr>
							<tr style="border-top: 2px solid black;">
								<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Ходимлар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та ходим учун гр</td>
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
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
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
								<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами</b></td>
								<td></td>
								<?php
								$xcost = 0;
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
										$xcost += ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']];
								?>
									<td style="padding: 0px; font-size: 5px"><?php round(($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']], 1); ?></td>
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
								<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>"><?php printf("%01.2f",$xcost); ?></td>
							</tr>
							<tr>
								<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 та бола учун</th>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>"><?php printf("%01.2f", $chcost / 1); ?></td>
							</tr>
							<tr>
								<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 та ходим учун</th>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>"><?php if($workers > 0) printf("%01.2f", $xcost / $workers); else printf("%01.2f", 0);?></td>
							</tr>
							<tr>
								<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">Умумий маблағ</th>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>"><?php printf("%01.2f", $chcost + $xcost); ?></td>
							</tr>	
                        </tbody>
                      </table>
                </div>
				<div class="row" style="margin-top: 15px;">
				    <div>
						<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
					</div>
					<div>
						<p style="text-align: center;"><strong> Бош ошпаз:</strong> __________________;</p>
					</div>
					<div>
						<p style="text-align: right;"><strong>Танишдим ДМТТ рахбари: </strong> __________________;</p>
					</div>
				</div>
            </div>
        </div>
    </div>
</body>
</html>