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
	 @page { margin: 0.2in 0.2in 0.2in 0.2in; }
	body{
		font-family: DejaVu Sans;
		font-size:6px;
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
						<?php

						    $numberofchildren = 0;

							if($days[0]->month_id % 12 == 0){
								$mth = 12;
							}else{
								$mth = $days[0]->month_id % 12;
							}
						?>
						<center><b>{{ env('COMPANY_NAME') }} томонида Аутсорсинг хизмати кўрсатилаётган {{ $region->region_name }} {{ $kindgar->number_of_org }}-сонли ДМТТнинг <?php echo $days->first()->day_number  ?>.<?php printf('%02d', $mth) ?>.<?php printf('%02d', $costs[0]->year_name) ?> йилдан <?php echo $days->last()->day_number ?>.<?php printf('%02d', $mth) ?>.<?php printf('%02d', $costs[0]->year_name) ?> йилгача  {{ $age->age_name }}ли гуруҳи учун НАКАПИТЕЛ</b></center>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 10%;" rowspan="2">Махсулотлар</th>
                            <th style="width: 10px;">Сана</th>
                            <!-- <th style="width: 30px; font-size: 7px"><bold>Нарх</bold></th> -->
                            @foreach($days as $day)
								<th scope="col">{{ $day->day_number; }}</th>
							@endforeach
							<th>Жами</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php 
						$kgsumm = 0;
						$costsumm = 0;
						$ndssumm = 0;
					?>
					@foreach($nakproducts as $key => $row)
					<tr>
						<td style="text-align: left; padding-left: 2px">{{ implode(' ', array_slice(explode(' ', $row['product_name']), 0, 3)) }}</td>
						@if($row['product_name'] != "Болалар сони")
							<td>{{ $row['size_name'] }}</td>
						@endif
						<?php 
							$summ = 0;
						?>
						@foreach($days as $day)
							@if(isset($row[$day['id']]))
								<td>
								@if($row['product_name'] == "Болалар сони")
									<strong>{{ $row[$day['id']]; }}</strong>
								<?php  
									$summ += $row[$day['id']];
									$numberofchildren += $row[$day['id']];
								?>
								@else
                                	@if($row['size_name'] == "дона")
                                  		<?php  
                                            printf("%01.3f", $row[$day['id']]); 
                                            $summ += $row[$day['id']];
                                        ?>
                                	@else
                                		<?php  
                                            printf("%01.3f", $row[$day['id']]); 
                                            $summ += $row[$day['id']];
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
                        @if($row['product_name'] != "Болалар сони")
                      		<?php $kgsumm += $summ; ?>
                        @endif
						@if($row['product_name'] == "Болалар сони")
							<td>{{ $summ }}</td>
						@else
							<td ><?php printf("%01.3f", $summ) ?></td>
						@endif
						
					</tr>
					@endforeach
					<tr>
						 <td colspan="2" style="border-top: 1px solid black;">1 болани бир куник харажати</td> 
						<td colspan="{{ count($days) }}"></td>
						<td>{{ number_format($protsent->eater_cost, 2); }}</td>
					</tr>
					<tr>
						 <td colspan="2">Ko'rsatilgan xizmat summasi QQS bilan</td> 
						<td colspan="{{ count($days) }}"></td>
						<td>{{ number_format($protsent->eater_cost*$numberofchildren, 2); }}</td>
					</tr>
					<tr>
						<td colspan="2">Белгиланган устама {{ $protsent->raise }}%</td> 
						<td colspan="{{ count($days) }}"></td>
						<td>{{ number_format($protsent->eater_cost*$numberofchildren*$protsent->raise/100, 2); }}</td>
					</tr>
					<tr>
						<td colspan="2">1 ойлик жами харажат</td> 
						<td colspan="{{ count($days) }}"></td>
						<td>{{ number_format($protsent->eater_cost*$numberofchildren*$protsent->raise/100 + $protsent->eater_cost*$numberofchildren, 2); }}</td>
					</tr>
				</tbody>
			</table>
					<div class="column">
					    @php
							$qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
						@endphp
						<img src="data:image/jpeg;base64,{{ $qrImage }}" 
							style="width:120; position:absolute; left:10px;">
					</div>
					<div class="column">
						<p></p>
						<p style="text-align: center;"><strong>ДМТТ рахбари: </strong> __________________;</p>
					</div>
                </div>
            </div>
        </div>
    </div>
</bod>
<html>