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
                		echo  $menu[0]['age_name'] . "ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
                		if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
                			echo "  ходимлар сони: <b>".$menu[0]['worker_count'].";</b>  ";	
                		}
						echo "          КЕЙИНГИ ИШ КУНИ УЧУН ТАХМИНИЙ ТАОМНОМА!"
                	?>
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;"></th>
                          	 <th style="width:8%;">Махсулотлар номи</th>
                          	 <?php  
                          	 $products = array();
                          	 $tree = array();
                          	 $prid = array();
                          	 $lo = 0;
                          	 $ind = -1;
							 $onew = [];
							 $__worker = [];

                          	 foreach($menuitem as $items){
                          		if(empty($food1[$items->meal_time_name.$items->food_name])){
                          			  $ind++;
                          		}
                          		if(empty($mealtime[$items->meal_time_name])){ 
                          			$mealtime[$items->meal_time_name] = 0;
                          			$tree[$ind]['time'] = $items->meal_time_name;
                          		}
                          		if(empty($food1[$items->meal_time_name.$items->food_name])){
                          			$mealtime[$items->meal_time_name]++;
                          			$food1[$items->meal_time_name.$items->food_name] = true;
                          			$tree[$ind]['food_name'] = $items->food_name;
									foreach($workerfood as $worf){
										if($worf->food_id == $items->menu_food_id){
											$__worker[$ind] = 1;
										}
									}
                          		}
                          		if(empty($buling[$items->product_name])){
                          			array_push($products, $items->product_name);
                          			$buling[$items->product_name]=true;
                          			$prid[$items->product_name] = $lo;
									$div[$lo] = $items->div;
                          			// jami miqdorlar uchun
									$onew[$lo] = 0;
									$allw[$lo] = 0;
									$oneworker[$lo]=0;
									$lo++;
                          			?>
                          			<th class='vrt-header' style="padding: 0px; width: 3%; height: 95px"><?php echo '<span>'.$items->product_name. '</span>';?></th>
                          			<?php
                          		}
                          		$tree[$ind][$prid[$items->product_name]] = $items->weight;
                          	 }
                          	 //dd($tree[0]['food_name']);
                          	 ?>
                          </tr>
                        </thead>
                        <tbody>
                        	<?php
                        		for($i = 0; $i < count($tree); $i++){
                        	?>
			                        <tr>
			                        	<?php if(!empty($tree[$i]['time'])){ ?>
												<th scope="row" rowspan="<?php echo 2*$mealtime[$tree[$i]['time']]; ?>" class='vrt-header' style="padding: 0px; height: 60px;"><?php echo '<span>'.$tree[$i]['time'].'</span>'; ?></th>
			                            <?php } ?>
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 2px;"><?php echo $tree[$i]['food_name'] ?></td>
			                            <?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(!empty($tree[$i][$t])){
			                            		if(isset($__worker[$i])){
			                            			$oneworker[$t] += $tree[$i][$t]; 	
			                            		}
			                            ?>
			                            		<td style="padding: 0px;"><?php $onew[$t] += $tree[$i][$t]; echo $tree[$i][$t]; ?></td>
			                            <?php
				                            }
			                            	else{
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
			                            	if(!empty($tree[$i][$t])){
			                            ?>	
			                            		<td style="padding: 0px;"><?php $allw[$t] += (($menu[0]['kingar_children_number'])*$tree[$i][$t])/ $div[$t]; printf("%01.3f", (($menu[0]['kingar_children_number'])*$tree[$i][$t]) / $div[$t] ); ?></td>
			                            	
			                            <?php
				                            }
			                            	else{
			                            ?>
			                            		<td style="padding: 0px;"></td>
			                            <?php	
			                            	}
                    					}
			                            ?>
                    				</tr>
                    	<?php		
                        		}
                        	?>
									<tr>
										<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
										<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та бола учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"><?= $onew[$t]; ?></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами миқдори</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"><?= $allw[$t]; ?></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Нархи</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px">1</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами:</b></td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px">0</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= count($products); ?>">1</td>
									</tr>
									<tr style="border-top: 2px solid black;">
										<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Ходимлар</span></th>
										<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">1 та ходим учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 2px solid black"><?= $oneworker[$t]; ?></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами миқдори</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"><?php printf("%01.3f",   $menu[0]['worker_count'] * $oneworker[$t] / $div[$t]) ?></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Нархи</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px"></td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;"><b>Сумма жами</b></td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px">0</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">Жами харажат</td>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= count($products); ?>">1</td>
									</tr>
									<tr style="border-top: 2px solid black;">
										<th scope="row" colspan="2" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><b>Жами махсулот оғирлиги</b></th>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px; border-top: 2px solid black">1</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">Жами сарфланган маблағ</th>
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= count($products); ?>">0</td>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 нафар бола учун</th>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px">0</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<tr>
										<th scope="row" colspan="2" class="align-baseline" style="padding: 0px;">1 нафар ходим учун</th>
										<?php
			                            for($t = 0; $t < count($products); $t++){
			                            ?>
			                            	<td style="padding: 0px; font-size: 5px">1</td>
			                            <?php	
                    					}
			                            ?>
									</tr>
									<!-- <tr>
										<th style="border: 0px"></th>
										<td style="border: 0px"></td>
										<td colspan="" style="border: 0px;">	
										</td>
										<td colspan="7" style="border: 0px; padding-top: 4px">
										<img src="images/qrcode.jpg" alt="QR-code" width="150">	
										</td>
									</tr> -->
                        </tbody>
                      </table>
                      <div style="text-align: end; width: 100%; ">
					  	<!-- <img src="images/qrcode.jpg" alt="QR-code" width="150"> -->
					  </div>
                </div>
            </div>
        </div>
    </div>
 

</body>
</html>