<table>
  <tr>
	  <td colspan="4"><b>{{ $kindgar->kingar_name." / ".$age->age_name }}</b></td>
  </tr>
  <tr>
	<th><b>Махсулотлар</b></th>
	<th><b>...</b></th>
	<th><b>Нарх ҚҚС билан</b></th>
	@foreach($days as $day)
		<th><b>{{ $day->day_number; }}</b></th>
	@endforeach
	<th><b>Жами</b></th>
	<th><b>Сумма</b></th>
  </tr>
  <?php 
		$kgsumm = 0;
		$costsumm = 0;
	?>
	@foreach($nakproducts as $key => $row)
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
				@if($row['product_name'] == "Болалар сони")
					{{ $row[$day['id']]; }}
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
			@else
				<td>
					{{ '0' }}
				</td>
			@endif
		@endforeach
		<td style="width: 6%;"><?php $kgsumm += $summ; printf("%01.3f", $summ) ?></td>
		<td ><?php $costsumm += $summ*$row[0]; printf("%01.1f", $summ*$row[0]) ?></td>
	</tr>
	@endforeach
	<tr>
		<td colspan="3"><b>Жами:</b></td>
		<td colspan="{{ count($days) }}"></td>
		<td><?php printf("%01.3f", $kgsumm); ?></td>
		<td><?php printf("%01.3f", $costsumm); ?></td>
	</tr>
</table>     