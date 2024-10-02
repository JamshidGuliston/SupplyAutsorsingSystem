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
		font-size:7px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		
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
	.head{
		font-size:5px;
	}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="table" id="table_with_data">
					<div class="col-md-6">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>{{ "№ ".$days[0]['month_id'].'-'.$kind->id }}</b>
                        <i class="fas fa-store-alt" style="font-size: 14px;"> {{ $kind->kingar_name." /" }}</i>
						<span>{{ $days[0]['month_name']." oyi" }}</span>
					</div>
                </div>
                <hr>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 1%;" rowspan="2">TR</th>
                            <th scope="col" style="width: 3%;" rowspan="2">Maxsulotlar</th>
                            <th scope="col" style="width: 1%;" rowspan="2">..</th>
							<th scope="col" style="width: 1%;" rowspan="2">O'tgan oydan</th>
							@foreach($days as $day)
                            	<th colspan="9">{{ $day->day_number.'-'.$day->month_name."-".$day->year_name }}</th>
							@endforeach
                            <!-- <th scope="col" style="width: 3%;" rowspan="2">Jami</th> -->
                        </tr>
						<tr>
						@foreach($days as $day)
							<th class="head">kirim</th>
							<th class="head">chiqim</th>
							<th class="head">chqiti</th>
							<th class="head">Jami kirim</th>
							<th class="head">Jami chiqim</th>
							<th class="head">Farqi</th>
							<th class="head">KG</th>
							<th class="head">Farqi</th>
							<th class="head">Qoldiq</th>
						@endforeach
						</tr>
                    </thead>
                    <tbody>
						<?php 
							$tr =1;
							$plus = [];
							$minus = []; 
						?>
                        @foreach($products as $product)
							@if(!isset($plus[$product->id]))
								<?php
									$plus[$product->id] = 0;
									$minus[$product->id] = 0;
								?>
							@endif
							<tr>
								<th scope="row">{{ $tr++ }}</th>
								<td>{{ $product["product_name"] }}</td>
								<td>{{ "kg" }}</td>
								@if(isset($prevmods[$product->id]))
									<td>{{ $prevmods[$product->id] }}</td>
									<?php
										$plus[$product->id] += $prevmods[$product->id];
									?>
								@else
									<td>0</td>
								@endif
								<?php 
									$total =0;
								?>
								@foreach($days as $day)
									@if(isset($plusproducts[$product->id][$day->id]))
										<td class="head">{{ $plusproducts[$product->id][$day->id] }}</td>
										<?php
											$plus[$product->id] += $plusproducts[$product->id][$day->id];
										?>
									@else
										<?php
											$plusproducts[$product->id][$day->id] = 0;
										?>
										<td></td>
									@endif
									@if(isset($minusproducts[$product->id][$day->id]))
										<td class="head">{{ $minusproducts[$product->id][$day->id] }}</td>
										<?php
											$minus[$product->id] += $minusproducts[$product->id][$day->id];
										?>
									@else
										<?php
											$minusproducts[$product->id][$day->id] = 0;
										?>
										<td></div>
									@endif
									@if(isset($takedproducts[$product->id][$day->id]))
										<td>{{ $takedproducts[$product->id][$day->id] }}</td>
										<?php
											$minus[$product->id] += $takedproducts[$product->id][$day->id];
										?>
									@else
										<td></td>
									@endif
									@if(isset($plus[$product->id]))
										<td>{{ $plus[$product->id] }}</td>
									@else
										<td></td>
									@endif
									@if(isset($minus[$product->id]))
										<td>{{ $minus[$product->id] }}</td>
									@else
										<td></td>
									@endif
									<td>{{ sprintf('%0.3f', $plus[$product->id] - $minus[$product->id]) }}</td>
									@if(isset($actualweights[$product->id][$day->id]))
										<td>{{ $actualweights[$product->id][$day->id] }}</td>
									@else
										<td></td>
									@endif
									@if(isset($isThisMeasureDay[$day->id]))
										<td>{{ sprintf('%0.3f',  $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id])) }}</td>
									@else
										<td></td>
									@endif
									@if(isset($isThisMeasureDay[$day->id]) and $plus[$product->id] - $minus[$product->id] < $actualweights[$product->id][$day->id])
										<td>{{ sprintf('%0.3f', $actualweights[$product->id][$day->id]) }}</td>
										<?php
											$plus[$product->id] += sprintf('%0.3f',  $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]))
										?>
									@else
										<td>{{ sprintf('%0.3f', $plus[$product->id] - $minus[$product->id]) }}</td>
									@endif
								@endforeach
								<!-- <td>{{ $total }}</td> -->
							</tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top: 15px;">
				<div class="column">
					<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
				</div>
				<div class="column">
					<p style="text-align: right;"><strong>Қабул қилувчи: </strong> __________________;</p>
				</div>
			</div>
        </div>
    </div>
</bod>
<html>