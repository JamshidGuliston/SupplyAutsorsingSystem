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
		/* padding: 300px 100px 10px 100px; */
		width: 100%;
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
					<div class="col-md-3">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>{{ $kindgar->kingar_name." / ".$age->age_name }}</b>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 10%;">Махсулотлар</th>
                            <th style="width: 30px;"><bold>Нарх</bold></th>
                            @foreach($days as $day)
								<th scope="col">{{ $day->day_number; }}</th>
							@endforeach
							<th>Жами</th>
							<th style="width: 15%;">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
					@foreach($nakproducts as $key => $row)
					<tr>
						<td>{{ $row['product_name'] }}</td>
						<td>{{ $row[0] }}</td>
						<?php 
							$summ = 0;
							$t = 0;
						?>
						@foreach($days as $day)
							@if(isset($row[$day['id']]))
								<td>
								@if($row['product_name'] == "Болалар сони")
									<strong>{{ $row[$day['id']]; }}</strong>
								<?php  
									$summ += $row[$day['id']];
								?>
								@else
								<?php  
									printf("%01.1f", $row[$day['id']]); 
									$summ += $row[$day['id']];
								?>
								@endif
								</td>
							@else
								<td>
									{{ '0' }}
								</td>
							@endif
						@endforeach
						<td><?php printf("%01.1f", $summ) ?></td>
						<td ><?php printf("%01.1f", $summ*$row[0]) ?></td>
					</tr>
					@endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</bod>
<html>