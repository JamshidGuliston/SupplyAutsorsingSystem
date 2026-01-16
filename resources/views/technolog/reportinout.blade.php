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
		font-size:14px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		
	}
	table{
		border-collapse: collapse;
		width: 100%;
		border: 2px solid black;	
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

	td:nth-child(1) {
		position: sticky;
		border: 1px solid black;
		left: 0;
		background-color: #faffb3;
		z-index: 3;
	}
	tbody tr:hover {
		background-color: #c5effc; /* Light blue color for hover effect */
		cursor: pointer; /* Optional: Adds a pointer cursor to indicate interactivity */
	}
	thead th {
		position: sticky;
		top: 0; /* Sticks the header to the top of the table's scrolling container */
		z-index: 10; /* Ensures it appears above other content */
		background-color: #faffb3; /* Optional: Makes the header visually distinct */
		border: 2px solid black;
		padding: 5px; /* Adjust padding as needed */
		text-align: center;
	}
	td:nth-child(2) {
		position: sticky;
		border: 1px solid black;
		left: 20px; /* Birinchi ustun kengligiga teng bo'lishi kerak */
		background-color: #faffb3;
		z-index: 3;
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
						<span>{{ $days[0]['month_name']." oyi                      " }}</span>
						<form action="{{route('technolog.reportinoutpdf')}}" method="GET" target="_blank" style="display: inline-block; margin-right: 10px;">
							@csrf
							<input type="hidden" name="kindergarden_id" value="{{ $kind->id }}">
							<input type="hidden" name="month_id" value="{{ $days[0]['month_id'] }}">
							<button type="submit" class="btn add-age btn-primary text-white">PDF</button>
						</form>
						<form action="{{route('technolog.reportinoutexcel')}}" method="GET" target="_blank" style="display: inline-block;">
							@csrf
							<input type="hidden" name="kindergarden_id" value="{{ $kind->id }}">
							<input type="hidden" name="month_id" value="{{ $days[0]['month_id'] }}">
							<button type="submit" class="btn add-age btn-success text-white">Excel</button>
						</form>
					</div>
                </div>
                <hr>
                <table>
                    <thead>
                        <tr>
                            <th scope="col" class="three" rowspan="2">TR</th>
                            <th scope="col" class="three" rowspan="2">Maxsulotlar</th>
                            <th scope="col" class="three" rowspan="2">O'lcham</th>
							<th scope="col"  rowspan="2">O'tgan oydan</th>
							@foreach($days as $day)
                            	<th colspan="9">{{ $day->day_number.'-'.$day->month_name."-".$day->year_name }}</th>
							@endforeach
                            <th scope="col"  colspan="3">Jami farqlar</th>
                        </tr>
						<tr>
							@foreach($days as $day)
								<th>kirim</th>
								<th>chiqim</th>
								<th>chqiti</th>
								<th>Jami kirim</th>
								<th>Jami chiqim</th>
								<th>Farqi</th>
								<th>KG</th>
								<th>Farqi</th>
								<th>Qoldiq</th>
							@endforeach
							<th>Ortirma</th>
							<th>Yo'qolgan</th>
							<th>Chqiti</th>
						</tr>
                    </thead>
                    <tbody>
						<?php 
							$tr =1;
							$added = [];
							$losted = [];
							$trashed = [];
							foreach($products as $product){
								$added[$product->id] = 0;
								$losted[$product->id] = 0;
								$trashed[$product->id] = 0;
							}
							$plus = [];
							$minus = []; 
						?>
                        @foreach($products as $product)
							@if(!isset($plus[$product->id]))
								<?php
									$plus[$product->id] = 0;
								?>
							@endif
							@if(!isset($minus[$product->id]))
								<?php
									$minus[$product->id] = 0;
								?>
							@endif
							<tr>
								<td scope="row">{{ $tr++ }}</td>
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
									$total = 0;
								?>
								@foreach($days as $day)
									@if(isset($plusproducts[$product->id][$day->id]))
										<td>{{ $plusproducts[$product->id][$day->id] }}</td>
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
										<td>{{ $minusproducts[$product->id][$day->id] }}</td>
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
											$trashed[$product->id] += $takedproducts[$product->id][$day->id];
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
										<?php
											if(!isset($actualweights[$product->id][$day->id])){
												$actualweights[$product->id][$day->id] = 0;
											}
										?>
										<td>{{ sprintf('%0.3f',  $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id])) }}</td>
										<?php
											if($actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]) < 0){
												$losted[$product->id] = $losted[$product->id] + $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]);
											}
											else{
												$added[$product->id] = $added[$product->id] + $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]);
												$plus[$product->id] += $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]);
											}
										?>
									@else
										<td></td>
									@endif
									@php
										$minus[$product->id] = ($plus[$product->id] - $minus[$product->id] < 0) ? ($plus[$product->id] - $minus[$product->id]) + $minus[$product->id] : $minus[$product->id];
									@endphp
									@if(isset($isThisMeasureDay[$day->id]) and $plus[$product->id] - $minus[$product->id] < $actualweights[$product->id][$day->id])
										<td>{{ sprintf('%0.3f', $actualweights[$product->id][$day->id]) }}</td>
										<?php
											// $plus[$product->id] += sprintf('%0.3f',  $actualweights[$product->id][$day->id] - ($plus[$product->id] - $minus[$product->id]));
										?>
									@else
										<td>{{ sprintf('%0.3f', $plus[$product->id] - $minus[$product->id]) }}</td>
									@endif
								@endforeach
								<td>{{ sprintf('%0.3f', $added[$product->id]) }}</td>
								<td>{{ sprintf('%0.3f', $losted[$product->id]) }}</td>
								<td>{{ sprintf('%0.3f', $trashed[$product->id]) }}</td>
							</tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</bod>
<html>