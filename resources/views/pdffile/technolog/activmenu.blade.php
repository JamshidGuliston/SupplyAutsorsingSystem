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
		@page { margin: 0.2in 0.8in 0in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size:8px;
			background-image: url(images/bg.jpg);
			background-position: top left;
			background-repeat: no-repeat;
			background-size: 100%;
			/* padding: 300px 100px 10px 100px; */
			width:100%;
		}
		table{
			border-collapse: collapse;
			border: 2px solid black;
			width: 100%;	
		}
		thead{
			border: 2px solid black;
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
                <div class="table" id="table_with_data">
                	<?php
						echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b><br/>";
                		echo  'sana: <b>"'.$day['day_number'].'".'.$day['month_name'].' 2022-й;</b>    <b>           ' . $menu[0]['age_name'] . "</b>ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
                		if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
                			echo "  ходимлар сони: <b>".$menu[0]['worker_count'].";</b>  ";	
                		}
                	?>
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;"></th>
                          	 <th style="width:8%;">Махсулотлар номи</th>
                          	 <th class='vrt-header' style="width:2%;"><?php echo '<span>Таом вазни</span>';?></th>
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 <?php $col++; ?>
                          	 		<th class='vrt-header' style="padding: 0px; width: 3%; height: 95px"><?php echo '<span>'.$product['product_name']. '</span>';?></th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							$boolmeal = [];
                        	@foreach($menuitem as $row)
								@foreach($row as $item)
								@if($loop->index == 0))
									@continue;
									<?php $time = $item['mealtime']; ?>
								@endif
			                        <tr>
			                        	@if($loop->index == 1))
												<th scope="row" rowspan="<?php echo 2 * (count($row)-1); ?>" class='vrt-header' style="padding: 0px; height: 60px;"><?php echo '<span>'. $row[0]['mealtime'] .'</span>'; ?></th>
			                            @endif
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 2px;"><?php echo $item['foodname'] ?></td>
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 0px;"><?php echo '' ?></td>
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
										<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
										<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та бола учун гр</td>
										<td style="padding: 0px; border-top: 2px solid black"></td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"><?= $productallcount[$products[$t]['id']]; ?></td>
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
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
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
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Нархи</td>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами:</b></td>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
										<td></td>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>">0</td>
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
										<td scope="row" class="align-baseline" style="padding: 0px;">Нархи</td>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами</b></td>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
										<td></td>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>">0</td>
									</tr>
									<tr style="border-top: 2px solid black;">
										<th scope="row" colspan="2" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><b>Жами махсулот оғирлиги</b></th>
										<td style="padding: 0px; border-top: 2px solid black"></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">Жами сарфланган маблағ</th>
										<td></td>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>">0</td>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 нафар бола учун</th>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 нафар ходим учун</th>
										<td></td>
										<?php
			                            for($t = 0; $t < $col; $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php
                    					}
			                            ?>
									</tr>
                        </tbody>
                      </table>
                      <div style="text-align: end; width: 100%; ">
					  	<img src="images/qrmanzil.jpg" alt="QR-code" width="150">
					  </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>