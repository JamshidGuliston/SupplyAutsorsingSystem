<table>
  <tr>
	  <td colspan="4"><b>Sana: "___".<?php printf('%02d', $days[0]->month_id % 12) ?>. <?php printf('%02d', $costs[0]->year_name) ?> / {{ $kindgar->kingar_name." / ".$age->age_name }}</b></td>
  </tr>
  <tr>
	<th><b>Махсулотлар</b></th>
	<th><b>...</b></th>
	<th><b>Нарх</b></th>
	@foreach($days as $day)
		<th><b>{{ $day->day_number; }}</b></th>
	@endforeach
	<th>Жами</th>
	<th>Сумма</th>
	<th>Устама {{ $ust }}%</th>
	<th>Сумма</th>
	<th>ҚҚС {{ $nds }}%</th>
	<th>Сумма жами</th>
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
		@if($row['product_name'] != "Болалар сони")
			<?php $kgsumm += $summ; ?>
		@endif
		<td ><?php printf("%01.3f", $summ) ?></td>
		<td ><?php $costsumm += $summ*$row[0]; printf("%01.2f", $summ*$row[0]) ?></td>
		<td ><?php printf("%01.2f", ($summ*$row[0]*$ust)/100) ?></td>
		<td ><?php printf("%01.2f", $summ*$row[0] + ($summ*$row[0]*$ust)/100) ?></td>
		<td ><?php printf("%01.2f", (($summ*$row[0] + ($summ*$row[0]*$ust)/100)*$nds)/100) ?></td>
		<td ><?php printf("%01.2f", $summ*$row[0] + ($summ*$row[0]*$ust)/100 + (($summ*$row[0] + ($summ*$row[0]*$ust)/100)*$nds)/100) ?></td>
	</tr>
	@endforeach
	<tr>
	<tr>
		<td colspan="3">Жами:</td>
		<td colspan="{{ count($days) }}"></td>
		<td><?php printf("%01.3f", $kgsumm); ?></td>
		<td><?php printf("%01.3f", $costsumm); ?></td>
		<td><?php printf("%01.3f", ($costsumm * $ust)/100); ?></td>
		<td><?php printf("%01.3f", $costsumm + ($costsumm * $ust)/100); ?></td>
		<td><?php printf("%01.3f", ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
		<td><?php printf("%01.3f", $costsumm + ($costsumm * $ust)/100 + ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
	</tr>
</table>     