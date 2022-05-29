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
<title>Norma</title>
<style>
	 @page { margin: 0.2in 0.8in 0in 0.3in; }
	body{
		font-family: DejaVu Sans;
		font-size: 12px;
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
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">
					<div class="col-md-12">
                        <center>2022 йил Май ойида мактабгача таълим муассасаларида тарбияланувчиларнинг
 озиқ-овқат маҳсулотлари билан таъминланиши ҳақида маълумот</center>
                    	<br>
						<center>Поставщик:МЧЖ Нишон Инвест / {{ $kindgar->kingar_name." / ".$age->age_name }}</center>
						<center>Хисобот давридаги бола катнови: {{ $nakproducts[1]["children"] }} нафар</center>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 25%;">Махсулот номи</th>
                            <th style="width: 15%;">1 бола учун уртача кунлик меъёр (гр хисобида)</th>
                            <th style="width: 15%;">меъёр бўйича сарфланиши лозим булган махсулот микдори (кг хисобида)</th>
							<th style="width: 15%;">Хақиқий харажат (кг хисобида)</th>
							<th style="width: 15%;">Меъёрга нисбатан фарқи Кам(-) Ортиқча(+)</th>
							<th style="width: 15%;">таъминланиш	даражаси</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $ww = 0;
                        $www = 0;
                        $wwww = 0;
                    ?>
					@foreach($nakproducts as $key => $row)
					<tr>
						<td>{{ mb_strimwidth($row['product_name'], 0, 35) . '...' }}</td>
						<td>{{ $row['norm_weight'] }}</td>
						<td><?php
							if(mb_strimwidth($row['product_name'], 0, 3) == 'Тух')
								 printf("%01.1f", ($row['norm_weight'] * $nakproducts[1]["children"]));
							else
								printf("%01.1f", ($row['norm_weight'] * $nakproducts[1]["children"]) / $row['div']);
						?></td>
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
						<td><?php printf("%01.1f", $summ) ?></td>
						<td><?php
							if(mb_strimwidth($row['product_name'], 0, 3) == 'Тух')
								printf("%01.1f", $summ -$row['norm_weight'] * $nakproducts[1]["children"]);
							else
								printf("%01.1f", $summ - ($row['norm_weight'] * $nakproducts[1]["children"]) / $row['div']);
						?></td>
                        <?php
							if(mb_strimwidth($row['product_name'], 0, 3) == 'Тух')
                            	$ww += ($row['norm_weight'] * $nakproducts[1]["children"]);
							else
								$ww += ($row['norm_weight'] * $nakproducts[1]["children"]) / $row['div'];
							$www += $summ;
							$wwww += $summ - (($row['norm_weight'] * $nakproducts[1]["children"]) / $row['div']);
                        ?>
						<td><?php 
							if(mb_strimwidth($row['product_name'], 0, 3) == 'Тух')
								printf("%01.1f", $summ / (($row['norm_weight'] * $nakproducts[1]["children"]) / $row['div']) * 100);
							else
								printf("%01.1f", $summ / (($row['norm_weight'] * $nakproducts[1]["children"])) * 100);
						?></td>
					</tr>
					@endforeach
                    <tr>
                        <th scope="col" style="width: 25%;">Жами</th>
                        <th style="width: 30px;"></th>
                        <th><?php printf("%01.1f", $ww) ?></th>
                        <th><?php printf("%01.1f", $www) ?></th>
                        <th><?php printf("%01.1f", $wwww) ?></th>
                        <th><?php printf("%01.1f", $www / $ww * 100) ?></th>
                    </tr>
                    </tbody>
                </table>
                <div class="col-md-6">
                    <p>МЧЖ "НИШОН ИНВЕСТ" директори: _________________________ Қ.Нишонов</p>
                    <p>Технолог _________________</p>
                </div>
            </div>
        </div>
    </div>
</bod>
<html>  