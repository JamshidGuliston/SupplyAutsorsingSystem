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
		/* Create two equal columns that floats next to each other */
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
		/* .row{
			display: flex;
			justify-content: space-between;
		} */
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
								$countch = array_fill(1, 10, 0);
							?>
							@foreach($menu as $row)
								<td style="text-align: left; border: none !important;">
								@if($loop->index == 0)
									Боғча номи: <b>{{ $row[0]['kingar_name']; }}</b><br/>sana: <b>{{ $day['day_number'].'.'.$day['month_name'].' '.$day['year_name'] }}й.</b><b>
									<?php
										$workers = $row[0]['workers_count'];
										echo "  ходимлар сони: <b>".$row[0]['workers_count'].";</b>  ";	
									?>
								@endif
								<?php
									$countch[$row[0]['king_age_name_id']] = $row[0]['kingar_children_number'];
									echo  $row[0]['age_name'] . "</b>ли болалар сони: <b>" . $row[0]['kingar_children_number'].";</b>";
									// if($row[0]['worker_age_id'] == $row[0]['king_age_name_id']){
									// 	$workers = $row[0]['workers_count'];
									// 	echo "  ходимлар сони: <b>".$row[0]['workers_count'].";</b>  ";	
									// }
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
								 <?php 
								 	$col++;
									$shortname=substr($product['product_name'],0,21);
								?>
                          	 		<th class='vrt-header' style="padding: 0px; width: 4%; height: 69px"><?php echo '<span>'.$shortname. '</span>';?></th>
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
								<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
								<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">Жами миқдор</td>
								<td style="padding: 0px; border-top: 2px solid black"></td>
								<?php
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
								<td scope="row" class="align-baseline" style="padding: 0px;">...</td>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Жами харажат</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Устама {{ $protsent['raise'] }} %</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Сумма устама билан</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">ҚҚС {{ $protsent['nds'] }} %</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Жами сумма</td>
								<?php if ($col - 5 * floor($col/5) > 0){ ?>
									<td colspan="<?= $col - 5 * floor($col/5) ?>"></td>
								<?php } ?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $chcost); ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $chcost * $protsent['raise'] / 100) ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $chcost + $chcost * $protsent['raise'] / 100) ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", ($chcost + $chcost * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><b><?php printf("%01.2f", $chcost + $chcost * $protsent['raise'] / 100 + ($chcost + $chcost * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></b></td>
								<?php if ($col - 5 * floor($col/5) > 0){ ?>
									<td colspan="<?= $col - 5 * floor($col/5) ?>"></td>
								<?php } ?>
							</tr>
							<tr style="border-top: 2px solid black;">
								<th scope="row" rowspan="4" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Ходимлар</span></th>
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
							<!-- <tr>
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
							</tr> -->
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;"><b>...</b></td>
								<td></td>
								<?php
								$xcost = 0;
								for($t = 0; $t < count($products); $t++){
									if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
										$xcost += ($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']];
								?>
									<!-- <td style="padding: 0px; font-size: 5px"> -->
									<?php round(($workers * $workerproducts[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']], 1); ?>
									<!-- </td> -->
								<?php	
									}
									elseif(isset($products[$t]['yes'])){
									?>
										<!-- <td style="padding: 0px;"></td> -->
									<?php	
									}
								}
								?>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Жами харажат</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Устама {{ $protsent['raise'] }} %</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Сумма устама билан</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">ҚҚС {{ $protsent['nds'] }} %</td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>">Жами сумма</td>
								<?php if ($col - 5 * floor($col/5) > 0){ ?>
									<td style="padding: 0px; font-size: 5px" colspan="<?= $col - 5 * floor($col/5) ?>"><b></b></td>
								<?php } ?>
							</tr>
							<tr>
								<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $xcost); ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $xcost * $protsent['raise'] / 100) ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $xcost + $xcost * $protsent['raise'] / 100) ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", ($xcost + $xcost * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><b><?php printf("%01.2f", $xcost + $xcost * $protsent['raise'] / 100 + ($xcost + $xcost * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></b></td>
								<?php if ($col - 5 * floor($col/5) > 0){ ?>
									<td style="padding: 0px; font-size: 5px" colspan="<?= $col - 5 * floor($col/5) ?>"></td>
								<?php } ?>
							</tr>
							@foreach($agesumm as $key => $row)
								<tr>
									<?php
										$all = 0;
										$tit = '';
										if($key == 1){
											$tit = "1 бола 4-7 ёш";
										}
										if($key == 2){
											$tit = "1 бола 3-4 ёш";
										}
										if($key == 3){
											$tit = "1 бола Қисқа";
										}
									?>
									<th scope="row" colspan="2" class="align-baseline" style="padding: 0px; font-size: 5px">{{ $tit }}</th>
									<td></td>
									@foreach($row as $m)
										<?php $all += $m; ?>
									@endforeach
									<?php if($countch[$key] != 0) {$all = $all / $countch[$key];}  ?>
									<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $all); ?></td>
									<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $all * $protsent['raise'] / 100) ?></td>
									<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $all + $all * $protsent['raise'] / 100) ?></td>
									<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", ($all + $all * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></td>
									<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col/5); ?>"><?php printf("%01.2f", $all + $all * $protsent['raise'] / 100 + ($all + $all * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></td>
									<?php if ($col - 5 * floor($col/5) > 0){ ?>
										<td style="padding: 0px; font-size: 5px" colspan="<?= $col - 5 * floor($col/5) ?>"></td>
									<?php } ?>
								</tr>	
							@endforeach
							<tr>
								<th scope="row" colspan="2" class="align-baseline" style="padding: 0px; font-size: 5px">{{ "Умумий сумма" }}</th>
								<td></td>
								<td style="padding: 0px; font-size: 5px" colspan="<?= floor($col); ?>"><b><?php printf("%01.2f", $chcost + $chcost * $protsent['raise'] / 100 + ($chcost + $chcost * $protsent['raise'] / 100) * $protsent['nds'] / 100 + $xcost + $xcost * $protsent['raise'] / 100 + ($xcost + $xcost * $protsent['raise'] / 100) * $protsent['nds'] / 100); ?></b></td>
							</tr>
                        </tbody>
                      </table>
                </div>
				<div class="row" style="margin-top: 15px;">
					
				    <div class="column">
						<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
					</div>
					<div class="column">
						<p style="text-align: center;"><strong> Бош ошпаз:</strong> __________________;</p>
					</div>
					<div class="column">
						<p style="text-align: right;"><strong>ДМТТ директори: </strong> __________________;</p>
					</div>
				</div>
            </div>
        </div>
    </div>
</body>
</html>