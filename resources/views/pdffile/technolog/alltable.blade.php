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
		@page { margin: 0.2in 0.3in 0in 0.3in; }
		body{
			font-family: DejaVu Sans;
			font-size:9px;
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
			border-right: 1px solid black;
			border-bottom: 1px solid black;
			padding: 0px;
		}
		.vrt-header span{
			display: inline-block;
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
			-ms-transform: rotate(-90deg);
			-o-transform: rotate(-90deg);
			transform: rotate(-90deg);
			/* white-space: nowrap; */
		}
		
		/* Maxsulot nomlari uchun ikki qatorlik ko'rsatish */
		.product-name-header {
			hyphens: auto;
			line-height: 1.8;
			max-width: 100%;
		}
		
		.product-name-header span {
			display: inline-block;   /* block emas */
			line-height: 1.2;        /* qator balandligi */
			font-size: 9px;
			padding: 2px;
			white-space: normal;     /* kerak bo‘lsa so‘z bo‘linadi */
			word-break: break-word;  /* so‘z sig‘masa qator bo‘lib ketadi */
		}
	</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">
                	<?php
					// dd($day);
						echo "Боғча номи: <b>".$menu[0]['kingar_name']."</b>    Таомнома: <b>".$taomnoma['menu_name']."</b> <br/>";
                		echo  'Cана: <b>'.$day['day_number'].'.'.$day['month_name'].'.'.$day['year_name'].'-й учун;<br>' . $menu[0]['age_name'] . "</b>ли болалар сони: <b>" . $menu[0]['kingar_children_number'].";</b>";
                		// if($menu[0]['worker_age_id'] == $menu[0]['king_age_name_id']){
                			echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
                		// }
						echo "          <b style='color:red;'>КЕЙИНГИ ИШ КУНИ УЧУН ТАХМИНИЙ ТАОМНОМА!</b>";
                	?>
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;" rowspan="3"></th>
                          	 <th style="width:11%;" rowspan="3">Махсулотлар номи</th>
                          	 <!--<th style="width:8%;">Таом вазни</th>-->
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 <?php $col++; ?>
								 <th class='vrt-header product-name-header' style="padding: 0px; width: 15px; height: 90px"><?php 
									$words = explode(' ', $product['product_name']);
									$firstThreeWords = array_slice($words, 0, 2);
									echo '<span>'.implode(' ', $firstThreeWords).'</span>';
									?></th>
								@endif
							 @endforeach
                          </tr>
						  <tr>
							@foreach($products as $product)
								@if(isset($product['yes']))
									<td scope="row" class="align-baseline" style="padding: 0px; font-size: 7px;">{{ $product['size_name'] }}</td>
								@endif
							@endforeach
						   </tr>
						   <tr>
							@foreach($products as $product)
								@if(isset($product['yes']))
									<td scope="row" class="align-baseline" style="padding: 0px; font-size: 7px;">{{ $product['div'] }}</td>
								@endif
							@endforeach
						   </tr>
                        </thead>
                        <tbody>
							@php
								$boolmeal = [];
							@endphp
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
			                            <?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(isset($products[$t]['yes']) and isset($item[$products[$t]['id']])){
			                            ?>
			                            		<td style="padding: 0px; background-color: #e6f3ff;"><?php printf("%01.2f", (($menu[0]['kingar_children_number'])*$item[$products[$t]['id']]) / $products[$t]['div']); ?></td>
			                            <?php
				                            }
			                            	elseif(isset($products[$t]['yes'])){
			                            ?>
			                            		<td style="padding: 0px; background-color: #e6f3ff;"></td>
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
								
								@endforeach
							@endforeach
									<tr>
									<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Болалар</span></th>
									<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">Жами миқдори</td>
										<?php
										
										$narx = [];
										$narx[1] = 0; $narx[2] = 8000; $narx[3] = 0; $narx[4] = 9000; $narx[10] = 58000; $narx[12] = 9500; $narx[14] = 2000; $narx[15] = 0; $narx[16] = 10000; $narx[17] = 0;
										$narx[18] = 0; $narx[21] = 69000; $narx[22] = 25000; $narx[23] = 2400; $narx[24] = 32000; $narx[25] = 0; $narx[26] = 23000; $narx[27] = 6000; $narx[28] = 25000; $narx[29] = 2800;
										$narx[30] = 1300; $narx[31] = 15000; $narx[32] = 45000; $narx[33] = 9000; $narx[34] = 39000; $narx[35] = 18000; $narx[36] = 22000; $narx[37] = 27000; $narx[38] = 44000; $narx[39] = 17000;
										$narx[40] = 0; $narx[41] = 13000; $narx[42] = 28000; $narx[43] = 3000; $narx[44] = 4000; $narx[45] = 12000; $narx[46] = 39000; $narx[47] = 49000; $narx[48] = 13000; $narx[49] = 3000;
										$narx[50] = 28000; $narx[51] = 22000; $narx[52] = 4000; $narx[53] = 9000; $narx[54] = 8000; $narx[55] = 18000; $narx[56] = 9000; $narx[57] = 13000; $narx[58] = 17000; $narx[59] = 28000;
										$narx[60] = 2400; $narx[61] = 0; $narx[62] = 0; $narx[63] = 0; $narx[64] = 0;
										$narx[65] = 2400; $narx[66] = 0; $narx[67] = 0; $narx[68] = 0; $narx[69] = 0;
										$narx[70] = 2400; $narx[71] = 0; $narx[72] = 0; $narx[73] = 0; $narx[74] = 0;
										$narx[75] = 2400; $narx[76] = 0; $narx[77] = 0; $narx[78] = 0; $narx[79] = 0;
										$narx[80] = 2400; $narx[81] = 0; $narx[82] = 0; $narx[83] = 0; $narx[84] = 0;
										$narx[85] = 2400; $narx[86] = 0; $narx[87] = 0; $narx[88] = 0; $narx[89] = 0;
										$narx[90] = 2400; $narx[91] = 0; $narx[92] = 0; $narx[93] = 0; $narx[94] = 0;
										$narx[95] = 2400; $narx[96] = 0; $narx[97] = 0; $narx[98] = 0; $narx[99] = 0;
										$narx[100] = 2400; $narx[101] = 0; $narx[102] = 0; $narx[103] = 0; $narx[104] = 0;
										$narx[105] = 2400; $narx[106] = 0; $narx[107] = 0; $narx[108] = 0; $narx[109] = 0;
										$narx[110] = 2400; $narx[111] = 0; $narx[112] = 0; $narx[113] = 0; $narx[114] = 0;
										$narx[115] = 2400; $narx[116] = 0; $narx[117] = 0; $narx[118] = 0; $narx[119] = 0;
										$narx[120] = 2400; $narx[121] = 0; $narx[122] = 0; $narx[123] = 0; $narx[124] = 0;
										$narx[125] = 2400; $narx[126] = 0; $narx[127] = 0; $narx[128] = 0; $narx[129] = 0;
										$narx[130] = 2400; $narx[131] = 0; $narx[132] = 0; $narx[133] = 0; $narx[134] = 0;
										$narx[135] = 2400; $narx[136] = 0; $narx[137] = 0; $narx[138] = 0; $narx[139] = 0;
										$narx[140] = 2400; $narx[141] = 0; $narx[142] = 0; $narx[143] = 0; $narx[144] = 0;
										$narx[145] = 2400; $narx[146] = 0; $narx[147] = 0; $narx[148] = 0; $narx[149] = 0;
										$narx[150] = 2400; $narx[151] = 0; $narx[152] = 0; $narx[153] = 0; $narx[154] = 0;
										$narx[155] = 2400; $narx[156] = 0; $narx[157] = 0; $narx[158] = 0; $narx[159] = 0;
										$narx[160] = 2400; $narx[161] = 0; $narx[162] = 0; $narx[163] = 0; $narx[164] = 0;
										$narx[165] = 2400; $narx[166] = 0; $narx[167] = 0; $narx[168] = 0; $narx[169] = 0;
										$narx[170] = 2400; $narx[171] = 0; $narx[172] = 0; $narx[173] = 0; $narx[174] = 0;
										$narx[175] = 2400; $narx[176] = 0; $narx[177] = 0; $narx[178] = 0; $narx[179] = 0;
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 8px; border-top: 2px solid black; background-color: #e6f3ff;"><?php printf("%01.2f", (($menu[0]['kingar_children_number'])*$productallcount[$products[$t]['id']]) / $products[$t]['div'] ); ?></td>
			                            <?php	
											}
											elseif(isset($products[$t]['yes'])){
											?>
												<td style="padding: 0px; background-color: #e6f3ff;"></td>
											<?php	
											}
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">1 та бола учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            <!---->
			                            	<td style="padding: 0px; font-size: 8px"><?= $productallcount[$products[$t]['id']]; ?></td>
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
										<?php
										for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
										    <!-- <?= $narx[$products[$t]['id']]; ?> -->
			                            	<td style="padding: 0px; font-size: 8px"></td>
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
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            <!--<?php printf("%01.2f", (($menu[0]['kingar_children_number'])*$productallcount[$products[$t]['id']]) / $products[$t]['div'] * $narx[$products[$t]['id']]); ?>-->
			                            	<td style="padding: 0px; font-size: 8px"></td>
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
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>">0</td>
									</tr>
									<tr style="border-top: 2px solid black;">
										<th scope="row" rowspan="5" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><span>Ходимлар</span></th>
										<td scope="row" class="align-baseline" style="padding: 0px; border-top: 2px solid black">Жами миқдори</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 8px; border-top: 2px solid black; background-color: #e6f3ff;"><?php printf("%01.2f", (($menu[0]['workers_count'])*$workerproducts[$products[$t]['id']]) / $products[$t]['div']); ?></td>
			                            <?php	
											}
											elseif(isset($products[$t]['yes'])){
											?>
												<td style="padding: 0px; background-color: #e6f3ff;"></td>
											<?php	
											}
                    					}
			                            ?>
									</tr>
									<tr>
										<td scope="row" class="align-baseline" style="padding: 0px;">1 та ходим учун гр</td>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($workerproducts[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 8px"><?= $workerproducts[$products[$t]['id']]; ?></td>
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
			                            <td style="padding: 0px; font-size: 5px" colspan="<?= $col; ?>">0</td>
									</tr>
									<tr style="border-top: 2px solid black;">
										<th scope="row" colspan="2" class='vrt-header' style="padding: 0px; border-top: 2px solid black"><b>Жами махсулот оғирлиги</b></th>
										<?php
			                            for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes']) and isset($productallcount[$products[$t]['id']])){
			                            ?>
			                            	<td style="padding: 0px; font-size: 8px; border-top: 2px solid black"><?php printf("%01.2f", ($productallcount[$products[$t]['id']]*$menu[0]['kingar_children_number']+$workerproducts[$products[$t]['id']]*$menu[0]['workers_count'])/$products[$t]['div']); ?></td>
			                            <?php
											}
										}
			                            ?>
									</tr>
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