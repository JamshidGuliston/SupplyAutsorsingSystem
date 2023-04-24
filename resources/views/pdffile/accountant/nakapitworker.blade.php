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
<title>Nakapit</title>
<style>
	 @page { margin: 0.2in 0.2in 0in 0.2in; }
	body{
		font-family: DejaVu Sans;
		font-size:8px;
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
		border-right: 1px solid black;
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
					<div class="col-md-3">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>Sana: "_____".<?php printf('%02d', $days[0]->month_id) ?>. 2022 / {{ $kindgar->kingar_name." / Ходимлар" }}</b>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 10%;">Махсулотлар</th>
                            <th style="width: 10px;">...</th>
                            <th style="width: 30px; font-size: 7px"><bold>Нарх</bold></th>
                            @foreach($days as $day)
								<th scope="col">{{ $day->day_number; }}</th>
							@endforeach
							<th>Жами</th>
							<th style="width: 7%;">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php 
						$kgsumm = 0;
						$costsumm = 0;
					?>
					@foreach($nakproducts as $key => $row)
					<?php 
						$str = $row['product_name'];
						if (strlen($str) > 15)
							$str = substr($row['product_name'], 0, 13);
					?>
					<tr>
						<td>{{ $row['product_name'] }}</td>
						<td>{{ $row['size_name'] }}</td>
						<td>{{ $row[0] }}</td>
						<?php 
							$summ = 0;
						?>
						@foreach($days as $day)
							@if(isset($row[$day['id']]))
								<td>
								@if($row['product_name'] == "Ходимлар сони")
									<strong>{{ $row[$day['id']]; }}</strong>
								<?php  
									$summ += $row[$day['id']];
								?>
								@else
                                	@if($row['size_name'] == "дона")
                                  		<?php  
                                            echo round($row[$day['id']], 0); 
                                            $summ += round($row[$day['id']], 0);
                                        ?>
                                	@else
                                		<?php  
                                            echo round($row[$day['id']], 3); 
                                            $summ += round($row[$day['id']], 3);
                                        ?>
                                  	@endif
								@endif
								</td>
							@else
								<td>
									{{ '0' }}
								</td>
							@endif
						@endforeach
                        @if($row['product_name'] != "Ходимлар сони")
                      		<?php $kgsumm += $summ; ?>
                        @endif
						<td style="width: 6%;"><?php printf("%01.3f", $summ) ?></td>
						<td ><?php $costsumm += $summ*$row[0]; printf("%01.2f", $summ*$row[0]) ?></td>
					</tr>
					@endforeach
					<tr>
						<td colspan="3">Жами:</td>
						<td colspan="{{ count($days) }}"></td>
						<td><?php printf("%01.3f", $kgsumm); ?></td>
						<td><?php printf("%01.3f", $costsumm); ?></td>
					</tr>
                    </tbody>
                </table>
				<div class="row">
                  <div class="column">
                    <h4>МЧЖ "НИШОН ИНВЕСТ" директори ___________________________</h4>
                  </div>
                  <div class="column">
                    <h4>Бош. Хисобчи ____________________</h4>
                  </div>
                </div>
            </div>
        </div>
    </div>
</bod>
<html>