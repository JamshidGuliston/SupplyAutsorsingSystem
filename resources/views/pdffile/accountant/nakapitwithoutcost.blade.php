<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nakapit without cost</title>
<style>
	@page { 
		size: A4 landscape;
		margin: 5mm 5mm 5mm 5mm;
	}
	
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}
	
	body {
		font-family: 'DejaVu Sans', Arial, sans-serif;
		font-size: 8px;
		width: 100%;
		padding: 5px;
	}
	
	.header-text {
		text-align: center;
		font-weight: bold;
		font-size: 10px;
		margin-bottom: 10px;
		line-height: 1.4;
	}
	
	table {
		border-collapse: collapse;
		border: 2px solid #000;
		width: 100%;
		table-layout: auto;
		font-size: 7px;
	}
	
	thead {
		border: 2px solid #000;
	}
	
	th {
		border: 1px solid #000;
		padding: 3px 2px;
		text-align: center;
		vertical-align: middle;
		font-weight: bold;
		background-color: #f0f0f0;
		white-space: nowrap;
	}
	
	td {
		border: 1px solid #000;
		padding: 2px 3px;
		text-align: center;
		vertical-align: middle;
		white-space: nowrap;
	}
	
	td.product-name {
		text-align: left;
		padding-left: 5px;
		white-space: normal;
		word-wrap: break-word;
		max-width: 150px;
	}
	
	tr:first-child th {
		font-size: 8px;
		font-weight: bold;
	}
	
	.footer-section {
		margin-top: 20px;
		width: 100%;
		display: table;
	}
	
	.footer-column {
		display: table-cell;
		width: 33.33%;
		text-align: center;
		padding: 5px;
		font-size: 9px;
	}
	
	.page-break {
		page-break-after: always;
	}
	
	/* Print optimizations */
	@media print {
		body {
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}
		
		table {
			page-break-inside: auto;
		}
		
		tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}
	}
</style>
</head>
<body>
	<?php
		$numberofchildren = 0;
		if($days[0]->month_id % 12 == 0){
			$mth = 12;
		}else{
			$mth = $days[0]->month_id % 12;
		}
		$month = App\Models\Month::where('id', $days[0]->month_id)->first();
	?>
	
	<div class="header-text">
		{{ $kindgar->kingar_name }} да <?php printf('%04d', $days->first()->year_name) ?> йил <?php echo $days->first()->day_number."-".$days->last()->day_number ?> <?php echo $month->month_name ?> кунлари {{ $age->description }} учун сарфланган озиқ-овқат маҳсулотлар тўғрисида маълумот
	</div>
	
	<table>
		<thead>
			<tr>
				<th rowspan="2">№</th>
				<th colspan="2">Махсулотлар номи ва Ўлчов бирлиги</th>
				<!-- <th rowspan="2">сана</th> -->
				@foreach($days as $day)
					<?php
						if($day->month_id % 12 == 0){
							$month_id = 12;
						}else{
							$month_id = $day->month_id % 12;
						}
					?>
					<th>{{ sprintf("%02d.%02d.%d", $day->day_number, $month_id, $day->year_name) }}</th>
				@endforeach
				<th rowspan="2">Жами</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$kgsumm = 0;
				$costsumm = 0;
				$ndssumm = 0;
				$row_number = 0;
			?>
			@foreach($nakproducts as $key => $row)
				<tr>
					@if($row['product_name'] != "Болалар сони")
						<td>{{ ++$row_number }}</td>
						<td class="product-name">{{ $row['product_name'] }}</td>
						<td>{{ $row['size_name'] ?? '' }}</td>
					@else
						<td colspan="3"><strong>{{ $row['product_name'] }}</strong></td>
					@endif
					
					<?php $summ = 0; ?>
					
					@foreach($days as $day)
						@if(isset($row[$day['id']]))
							<td>
							@if($row['product_name'] == "Болалар сони")
								<strong>{{ $row[$day['id']] }}</strong>
								<?php  
									$summ += $row[$day['id']];
									$numberofchildren += $row[$day['id']];
								?>
							@else
								<?php  
									printf("%01.3f", $row[$day['id']]); 
									$summ += $row[$day['id']];
								?>
							@endif
							</td>
						@else
							<td>0</td>
						@endif
					@endforeach
					
					@if($row['product_name'] != "Болалар сони")
						<?php $kgsumm += $summ; ?>
					@endif
					
					@if($row['product_name'] == "Болалар сони")
						<td><strong>{{ $summ }}</strong></td>
					@else
						<td><?php printf("%01.3f", $summ) ?></td>
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>
	
	<div class="footer-section">
		<div class="footer-column">
			<strong>Аутсорсер директори:</strong> ____________________
		    <br/>
			<br/>
			<strong>Бухгалтер:</strong> ____________________
		</div>
		<div class="footer-column">
			<strong>ДМТТ рахбари:</strong> ____________________
			<br/>
			<br/>
			<strong>Ошпаз:</strong> ____________________
		</div>
		<div class="footer-column">
			<strong>Хамшира:</strong> ____________________
		</div>
	</div>
</body>
</html>