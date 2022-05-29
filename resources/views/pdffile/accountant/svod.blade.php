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
						<b>{{ "Text" }}</b>
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
							<th>Устама 20%</th>
							<th>Жами сумма</th>
							<th>НДС 15%</th>
							<th>Хаммаси жами</th>
                        </tr>
                    </thead>
                    <tbody>
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
							@if(isset($row[$day['id']]))
								<td style="width: 40px;">
								@if($row['product_name'] == "Болалар сони")
									<strong>{{ $row[$day['id']]; }}</strong>
								<?php  
									$summ += $row[$day['id']];
								?>
								@else
								<?php  
									printf("%01.2f", $row[$day['id']]); 
									$summ += $row[$day['id']];
								?>
								@endif
								</td>
								<td style="font-size: 6px;">
									<?php  
										printf("%01.0f", $row[$day['id']] * $row[0]);
									?>
								</td>
							@else
								<td>
									{{ '0' }}
								</td>
								<td>
									{{ '0' }}
								</td>
							@endif
						@endforeach
						<td><?php printf("%01.1f", $summ) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", $summ * $row[0]) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", ($summ * $row[0])/100 * 20) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", ($summ * $row[0] + $summ * $row[0])/100 * 20) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", (($summ * $row[0] + $summ * $row[0])/100 * 20) / 100 * 15) ?></td>
						<td style="font-size: 6px;"><?php printf("%01.1f", ($summ * $row[0] + $summ * $row[0])/100 * 20 + (($summ * $row[0] + $summ * $row[0])/100 * 20) / 100 * 15) ?></td>
					</tr>
					@endforeach
                    </tbody>
                </table>
				<br>
				<span>МЧЖ "НИШОН ИНВЕСТ" директори Қ.Нишонов.  Ташкилот рахбари____________________          Бош. Хисобчи ____________________	</span>
            </div>
        </div>
    </div>
</bod>
<html>