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
		font-size:12px;
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
                            <th scope="col" style="width: 3%;">TR</th>
                            <th scope="col" style="width: 20%;">Maxsulotlar</th>
                            <th scope="col" style="width: 7%;">O'lcham</th>
							@foreach($days as $day)
                            	<th scope="col">{{ $day->day_number }}</th>
							@endforeach
                            <th scope="col" style="width: 10%;">Jami</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php 
							$tr =1; 
						?>
                        @foreach($products as $product)
							@if(isset($document[$product->id]))
							<tr>
								<th scope="row">{{ $tr++ }}</th>
								<td>{{ $product["product_name"] }}</td>
								<td>{{ $document[$product->id]["size_name"] }}</td>
								<?php 
									$total = 0;
								?>
								@foreach($days as $day)
									@if(isset($document[$product->id][$day->id]))
										<?php
											$total += $document[$product->id][$day->id]["weight"];
										?>
										<td>{{ $document[$product->id][$day->id]["weight"] }}</td>
									@else
										<td></td>
									@endif
								@endforeach
								<td>{{ $total }}</td>
							</tr>
							@endif
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