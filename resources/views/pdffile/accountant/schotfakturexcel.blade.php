<table>
	<tr>
		<td colspan="10"><b>Поставщик:МЧЖ Нишон Инвест / {{ $kindgar->kingar_name." / ".$age->age_name }}</b></td>
	</tr>
	<tr>
		<th><b>Махсулот номи</b></th>
		<th><b>Ед.м</b></th>
		<th><b>калич</b></th>
		<th><b>цена</b></th>
		<th><b>Стоимость паставка</b></th>
		<th><b>Надбавка ставка сумма</b></th>
		<th><b>С тоимость поставка с учетом Надбавка</b></th>
	</tr>
	<?php
		$ww = 0;
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
		<td><?php printf("%01.4f", $summ) ?></td>
		<td>{{ $row[0] }}</td>
		<td ><?php printf("%01.2f", $summ*$row[0]) ?></td>
		<?php
			$ww += $summ*$row[0];
		?>
		<td><?php printf("%01.2f", ($summ*$row[0]/100)*15) ?></td>
		<td><?php printf("%01.2f", $summ*$row[0] + ($summ*$row[0]/100)*15) ?></td>
	</tr>
	@endforeach
	<tr>
		<th><b>Жами</b></th>
		<th></th>
		<th></th>
		<th></th>
		<th><b><?php printf("%01.1f", $ww) ?></b></th>
		<th><b><?php printf("%01.1f", $ww/100*15) ?></b></th>
		<th><b><?php printf("%01.1f", $ww + $ww/100*15) ?></b></th>
	</tr>
	<tr>
		<td>Всего к оплата</td>
		<td colspan="6"></td>
	</tr>
</table>