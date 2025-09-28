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
	 @page { margin: 0.1in 0.1in 0in 0.1in; }
	body{
		font-family: DejaVu Sans;
		font-size:7px;
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
		/* text-align: center; */
		overflow: hidden;
	}
	th{
		border: 1px solid black;
		padding: 0px;
	}
	td{
		border-right: 1px solid black;
		border-bottom: 1px solid black;
		padding-left: 0.5px;
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
						 <center><b>{{ $regions->find($kindgardens[0]->region_id)->region_name." мттларнинг ". $days[0]->year_name ." йил ". $days[0]->month_name ." ойида берилган озиқ овқат махсулотларининг хисоб-китоби" }}</b></center> 
					</div>
                </div>
                <table style="table-layout: fixed; width: 99%;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 7.5%;">Махсулот</th>
                            <th style="width: 1.5%;">Ўл<br>бир</th>
                            <th style="width: 2.5%;"><bold>Нарх</bold></th>
                            @foreach($kindgardens as $day)
								<th scope="col" colspan="2">{{ $day->kingar_name; }}</th>
							@endforeach
							<th style="width: 3%;">КГ</th>
							<th style="width: 4%;">Сумма</th>
							<!--<th style="width: 4%;">Устама {{ $over }}%</th>-->
							<!--<th style="width: 4%;">Жами сумма</th>-->
							<!--<th style="width: 4%;">НДС {{ $nds }}%</th>-->
							<!--<th style="width: 4%;">Хаммаси жами</th>-->
                        </tr>
                    </thead>
                    <tbody>
					<?php 
						$regionsumm = [];
						$summa = 0;
						$ustsumma = 0;
						$allsumma = 0;
						$ndssumma = 0;
						$jamisumma = 0;
						
					?>
					@foreach($nakproducts as $key => $row)
					@if($loop->index % 2 == 0)
						<tr style="background-color: #dfe4e3;">
					@else
						<tr>
					@endif
						<?php 
							$summ = 0;
							$row[0] = $row[0] ?? 0;
						?>
						<td>{{ $row['product_name'] }}</td>
						<td>{{ $row['size_name'] }}</td>
						<td>{{ $row[0] }}</td>
						@foreach($kindgardens as $day)
							@if(!isset($regionsumm[$day['id']]))
								<?php $regionsumm[$day['id']] = 0; ?>
							@endif
							@if(isset($row[$day['id']]))
                          		@if($row['size_name'] == "дона")
                                  <td>
                                  <?php  
                                      printf("%01.3f", $row[$day['id']]); 
                                      $summ += $row[$day['id']];
                                  ?>
                                  </td>
                                  <td style="font-size: 6px;"  style="width: 5%">
                                      <?php  
                                          printf("%01.2f", $row[$day['id']] * $row[0]);
                                          $regionsumm[$day['id']] += $row[$day['id']] * $row[0];
                                      ?>
                                  </td>
                                @else
                                	<td>
                                    <?php  
                                        printf("%01.3f", $row[$day['id']]); 
                                        $summ += $row[$day['id']];
                                    ?>
                                    </td>
                                    <td style="font-size: 6px;"  style="width: 5%">
                                        <?php  
                                            printf("%01.2f", $row[$day['id']] * $row[0]);
                                            $regionsumm[$day['id']] += $row[$day['id']] * $row[0];
                                        ?>
                                    </td>
                                @endif
								
							@else
								<td>
									{{ '0' }}
								</td>
								<td>
									{{ '0' }}
								</td>
							@endif
						@endforeach
						<td><?php printf("%01.3f", $summ) ?></td>
						<td style="font-size: 6px;"><?php $summa += $summ * $row[0]; printf("%01.2f",$summ * $row[0]); ?></td>
						<!--<td style="font-size: 6px;"><?php $ustsumma += ($summ * $row[0])/100 * $over; printf("%01.2f", ($summ * $row[0])/100 * $over) ?></td>-->
						<!--<td style="font-size: 6px;"><?php $allsumma += $summ * $row[0] + ($summ * $row[0])/100 * $over; printf("%01.2f", $summ * $row[0] + ($summ * $row[0])/100 * $over) ?></td>-->
						<!--<td style="font-size: 6px;"><?php $ndssumma += ($summ * $row[0] + ($summ * $row[0])/100 * $over) / 100 * $nds; printf("%01.2f", ($summ * $row[0] + ($summ * $row[0])/100 * $over) / 100 * $nds) ?></td>-->
						<!--<td style="font-size: 6px;"><?php $jamisumma += $summ * $row[0] + ($summ * $row[0])/100 * $over + ($summ * $row[0] + ($summ * $row[0])/100 * $over) / 100 * $nds; printf("%01.2f", $summ * $row[0] + ($summ * $row[0])/100 * $over + ($summ * $row[0] + ($summ * $row[0])/100 * $over) / 100 * $nds) ?></td>-->
					</tr>
					@endforeach
					<tr>
						<td colspan="3"><bold>Jami:</bold></td>
						@foreach($kindgardens as $day)
							<td colspan="2" style="text-align: right"><?php printf("%01.2f", $regionsumm[$day['id']]) ?></td>
						@endforeach
						<td colspan="2" style="text-align: right"><?php printf("%01.2f", $summa) ?></td>
					</tr>
					<tr>
						<td colspan="3">Устама {{ $over }}%</td>
						@foreach($kindgardens as $day)
							<td  colspan="2" style="text-align: right"><?php printf("%01.2f", $regionsumm[$day['id']]/100 * $over) ?></td>
						@endforeach
						<td colspan="2" style="text-align: right"><?php printf("%01.2f", $summa/100 * $over) ?></td>
					</tr>
					<tr>
						<td colspan="3">Сумма Устама билан</td>
						@foreach($kindgardens as $day)
							<td  colspan="2" style="text-align: right"><?php printf("%01.2f", $regionsumm[$day['id']] + $regionsumm[$day['id']]/100 * $over) ?></td>
						@endforeach
						<td colspan="2" style="text-align: right"><?php printf("%01.2f", $summa + $summa/100 * $over) ?></td>
					</tr>
					<tr>
						<td colspan="3">НДС {{ $nds }}%</td>
						@foreach($kindgardens as $day)
							<td  colspan="2" style="text-align: right"><?php printf("%01.2f", ($regionsumm[$day['id']] + $regionsumm[$day['id']]/100 * $over)/100 * $nds) ?></td>
						@endforeach
						<td colspan="2" style="text-align: right"><?php printf("%01.2f", ($summa + $summa/100 * $over)/100 * $nds) ?></td>
					</tr>
					<tr>
						<td colspan="3">Жами сумма НДС билан</td>
						@foreach($kindgardens as $day)
							<td  colspan="2" style="text-align: right"><?php printf("%01.2f", $regionsumm[$day['id']] + $regionsumm[$day['id']]/100 * $over + ($regionsumm[$day['id']] + $regionsumm[$day['id']]/100 * $over)/100 * $nds) ?></td>
						@endforeach
						<td colspan="2" style="text-align: right"><?php printf("%01.2f", $summa + $summa/100 * $over + ($summa + $summa/100 * $over)/100 * $nds) ?></td>
					</tr>
                    </tbody>
                </table>
                <div class="row">
                  <div class="column">
                    <h4>МЧЖ "НИШОН ИНВЕСТ" директори:  _________________    _________________________ </h4>
                    <h4>Бош. Хисобчи: _________________   ___________________________</h4>
                  </div>
                  <div class="column">
                    <h4>Ташкилот рахбари: ______________    ________________________</h4>
                    <h4>Бош. Хисобчи: _________________  __________________________</h4>
                  </div>
                </div>
            </div>
        </div>
    </div>
</bod>
<html>