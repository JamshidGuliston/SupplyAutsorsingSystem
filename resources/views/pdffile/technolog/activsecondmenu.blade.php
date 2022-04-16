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
		.row{
			display: flex;
			flex-wrap: nowrap;
			justify-content: space-between;
		}
		.row > div{
			margin-left: 50px;
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
                			echo "  ходимлар сони: <b>".$menu[0]['workers_count'].";</b>  ";	
                		}
                	?>
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <!-- <th style="width:2%;"></th> -->
                          	 <th style="width:12%;">Махсулотлар номи</th>
                          	 <th class='vrt-header' style="width:2%;"><?php echo '<span>Таом вазни</span>';?></th>
							   <?php $col = 0; ?>
							 @foreach($products as $product)
							 	@if(isset($product['yes']))
								 <?php $col++; ?>
                          	 		<th class='vrt-header' style="padding: 0px; width: 3%; height: 85px"><?php echo '<span>'.$product['product_name']. '</span>';?></th>
								@endif
							 @endforeach
                          </tr>
                        </thead>
                        <tbody>
							$boolmeal = [];
                        	@foreach($menuitem as $foods)
								@foreach($foods as $fkey => $food)
									<tr>
										<td scope="row" class="align-baseline" style="padding: 2px;"><?php echo $food['foodname'] ?></td>
										<td scope="row" class="align-baseline" style="padding: 0px;"><?php echo $food['foodweight'] ?></td>
										<?php
										for($t = 0; $t < count($products); $t++){
											if(isset($products[$t]['yes'])){
										?>
												<td style="padding: 0px;">{{ '*' }}</td>
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
											<td scope="row" class="align-baseline" style="padding: 2px;"><?php echo $age['age_name'] ?></td>
											<td scope="row" class="align-baseline" style="padding: 0px;"></td>
											<?php
											for($t = 0; $t < count($products); $t++){
												if(isset($products[$t]['yes']) and isset($age[$products[$t]['id']])){
											?>	
													<td style="padding: 0px;">{{ '1' }}</td>
												
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
									
                        </tbody>
                      </table>
                      <div class="row">
                      	<div>
					  		<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
					  	</div>
					  	<div>
					  		<p style="text-align: center;">Технолог __________________</p>
					  	</div>
					  	<div>
					  		<p style="text-align: right;">Бош ошпаз __________________</p>
					  	</div>
					  </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>