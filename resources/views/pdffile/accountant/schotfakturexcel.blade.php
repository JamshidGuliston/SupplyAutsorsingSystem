<table>
	<tr>
		<td colspan="10"><b>Поставщик:МЧЖ Нишон Инвест / {{ $kindgar->kingar_name." / ".$age->age_name }}</b></td>
	</tr>
	<tr>
		<th><b>Махсулот номи</b></th>
		<th><b>Ед.м</b></th>
		<th><b>калич</b></th>
		<th><b>цена</b></th>
		<th>Сумма</th>
		<th>Устама {{ $ust }}%</th>
		<th>Сумма</th>
		<th>ҚҚС {{ $nds }}%</th>
		<th>Сумма жами</th>
	</tr>
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
				@if($row['size_name'] == "дона")
                  <?php  
                  	$summ += round($row[$day['id']], 0);
                  ?>
                @else
                  <?php  
                  	$summ += $row[$day['id']];
                  ?>
                @endif
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
	<td><?php printf("%01.3f", $costsumm); ?></td>
	<td><?php printf("%01.3f", ($costsumm * $ust)/100); ?></td>
	<td><?php printf("%01.3f", $costsumm + ($costsumm * $ust)/100); ?></td>
	<td><?php printf("%01.3f", ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
	<td><?php printf("%01.3f", $costsumm + ($costsumm * $ust)/100 + ($costsumm + ($costsumm * $ust)/100)*$nds/100); ?></td>
	</tr>
	<tr>
		<td>Всего к оплата</td>
		<td colspan="8"></td>
	</tr>
</table>