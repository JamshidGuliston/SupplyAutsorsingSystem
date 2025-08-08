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
	 @page { margin: 0.1in 0.2in 0.2in 0.3in; }
	body{
		font-family: DejaVu Sans;
		font-size:10px;
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
		text-align: left;
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
		padding-left: 5px;
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
	.page-break {
		page-break-after: always;
	}
	/* Create two equal columns that floats next to each other */
	.column {
		float: left;
		text-align: center;
		width: 50%;
	}

	/* Clear floats after the columns */
	.row:after {
		content: "";
		display: table;
		clear: both;
	}
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">
					<div class="col-md-12">
						<?php
							if($days[0]->month_id % 12 == 0){
								$mth = 12;
							}else{
								$mth = $days[0]->month_id % 12;
							}
						?>
                        <center>Ҳисоб-фактура № <?php printf('%02d', $costs[0]->year_name) ?>-<?php printf('%02d', $mth) ?>/{{ $kindgar->id }}</center>
                        <center> " <?php echo $days->last()->day_number ?> ".<?php printf('%02d', $mth) ?>. <?php printf('%02d', $costs[0]->year_name) ?>й</center>
                        <center>Шартнома хужжатлари №____ " ____ . _____" <?php printf('%02d', $costs[0]->year_name) ?> йил</center><br>
						<div class="row">
							<div class="column">
								Етказиб берувчи: <strong> {{ env('COMPANY_NAME') }} </strong>
							</div>
							<div class="column">
								Буюртмачи: <strong>{{ $kindgar->kingar_name }}</strong>
							</div>
						</div>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col">Махсулот номи</th>
                            <th>Ўл.бир</th>
                            <th>Сони</th>
							<th>Нархи</th>
							<th>Суммаси</th>
							<th>Устама {{ $ust }}%</th>
							<th>Етказиб бериш суммаси</th>
							<th>ҚҚС {{ $nds }}%</th>
							<th>Сумма жами</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $costsumm = 0;
                    ?>
					@foreach($nakproducts as $key => $row)
					<tr>
						<td>{{ $row['product_name'] }}</td>
						<td>{{ $row['size_name'] }}</td>
						<?php 
							$summ = 0;
						?>
						@foreach($days as $day)
							@if(isset($row[$day['id']]))
							<?php  
								$summ += $row[$day['id']];
							?>
							@endif
						@endforeach
						<td><?php printf("%01.3f", $summ) ?></td>
                        <td>{{ $row[0] }}</td>
						<td ><?php $costsumm += $summ*$row[0]; printf("%01.2f", $summ*$row[0]) ?></td>
						<td ><?php printf("%01.2f", ($summ*$row[0]*$ust)/100) ?></td>
						<td ><?php printf("%01.2f", $summ*$row[0] + ($summ*$row[0]*$ust)/100) ?></td>
						<td ><?php printf("%01.2f", (($summ*$row[0] + ($summ*$row[0]*$ust)/100)*$nds)/100) ?></td>
						<td ><?php printf("%01.2f", $summ*$row[0] + ($summ*$row[0]*$ust)/100 + (($summ*$row[0] + ($summ*$row[0]*$ust)/100)*$nds)/100) ?></td>
					</tr>
					@endforeach
                    <tr>
						<th scope="col" style="width: 25%;">Жами</th>
                        <th style="width: 7px;"></th>
                        <th style="width: 30px;"></th>
                        <th style="width: 8%;"></th>
                        <td><?php printf("%01.2f", $costsumm); ?></td>
						<td><?php printf("%01.2f", ($costsumm * $ust)/100); ?></td>
						<td><?php printf("%01.2f", $costsumm + ($costsumm * $ust)/100); ?></td>
						<td><?php printf("%01.2f", ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
						<td><?php printf("%01.2f", $costsumm + ($costsumm * $ust)/100 + ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
                    </tr>
                    </tbody>
                </table>
                <div class="row">
					<div class="column">
						<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
					</div>
					<div class="column">
						<h4>ДМТТ рахбари________________</h4>
					</div>
                </div>
            </div>
        </div>
    </div>
</bod>
<html>  