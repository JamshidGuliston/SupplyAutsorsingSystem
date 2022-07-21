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
	 @page { margin: 0.2in 0.1in 0in 0.1in; }
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
		word-wrap: break-word;
	}
	th{
		border: 1px solid black;
		padding: 0px;
	}
	td{
		border-right: 1px solid black;
		border-bottom: 1px solid black;
		padding-left: 2px;
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
						<!-- <b>{{ "Text" }}</b> -->
					</div>
                </div>
                <table style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col">Махсулот</th>
                            <th>Ўлчов</th>
                            <th><bold>Нарх</bold></th>
                            @foreach($kindgardens as $day)
								<th scope="col" colspan="2">{{ $day->kingar_name; }}</th>
							@endforeach
							<th>КГ</th>
							<th>Сумма</th>
							<th>Устама {{ $over }}%</th>
							<th>Жами сумма</th>
							<th>НДС {{ $nds }}%</th>
							<th>Хаммаси жами</th>
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
						<td>{{ $row['product_name'] }}</td>
						<td>{{ $row['size_name'] }}</td>
						<td>{{ $row[0] }}</td>
						<?php 
							$summ = 0;
						?>
						@foreach($kindgardens as $day)
							@if(!isset($regionsumm[$day['id']]))
								<?php $regionsumm[$day['id']] = 0; ?>
							@endif
							@if(isset($row[$day['id']]))
                          		@if($row['size_name'] == "дона")
                                  <td style="width: 40px;">
                                  <?php  
                                      echo round($row[$day['id']], 0); 
                                      $summ += round($row[$day['id']], 0);
                                  ?>
                                  </td>
                                  <td style="font-size: 6px;">
                                      <?php  
                                          echo round(round($row[$day['id']], 3) * $row[0], 2);
                                          $regionsumm[$day['id']] += round(round($row[$day['id']], 3) * $row[0], 2);
                                      ?>
                                  </td>
                                @else
                                	<td style="width: 40px;">
                                    <?php  
                                        echo round($row[$day['id']], 3); 
                                        $summ += round($row[$day['id']], 3);
                                    ?>
                                    </td>
                                    <td style="font-size: 6px;">
                                        <?php  
                                            echo round(round($row[$day['id']], 3) * $row[0], 2);
                                            $regionsumm[$day['id']] += round(round($row[$day['id']], 3) * $row[0], 2);
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
						<td><?php echo round($summ, 3) ?></td>
						<td style="font-size: 6px;"><?php $summa += round($summ * $row[0], 3); echo round($summ * $row[0], 3); ?></td>
						<td style="font-size: 6px;"><?php $ustsumma += round(($summ * $row[0])/100 * $over, 3); echo round(($summ * $row[0])/100 * $over, 3) ?></td>
						<td style="font-size: 6px;"><?php $allsumma += round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3); echo round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) ?></td>
						<td style="font-size: 6px;"><?php $ndssumma += round(round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) / 100 * $nds, 3); echo round(round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) / 100 * $nds, 3) ?></td>
						<td style="font-size: 6px;"><?php $jamisumma += round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) + round(round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) / 100 * $nds, 3); echo round(round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) + round(round($summ * $row[0] + ($summ * $row[0])/100 * $over, 3) / 100 * $nds, 3), 3) ?></td>
					</tr>
					@endforeach
					<tr>
						<td><bold>Jami:</bold></td>
						<td></td>
						<td></td>
						@foreach($kindgardens as $day)
							<td></td>
							<td><?php printf("%01.1f", $regionsumm[$day['id']]) ?></td>
						@endforeach
						<td><?php printf("%01.1f", 0) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $summa) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $ustsumma) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $allsumma) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $ndssumma) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $jamisumma) ?></td>
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