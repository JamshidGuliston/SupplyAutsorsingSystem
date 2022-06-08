
<table>
	<thead>
		<tr>
			<th>Махсулотлар</th>
			<th>...</th>
			<th><bold>Нарх ҚҚС билан</bold></th>
			@foreach($days as $day)
				<th>{{ $day->day_number; }}</th>
			@endforeach
			<th>Жами</th>
			<th>Сумма</th>
		</tr>
	</thead>
	<tbody>
	@foreach($nakproducts as $key => $row)
	<tr>
		<td>{{ $row['product_name'] }}</td>
		<td>{{ $row['size_name'] }}</td>
		<td>{{ $row[0] }}</td>
		@foreach($days as $day)
			@if(isset($row[$day['id']]))
				<td>
				@if($row['product_name'] == "Болалар сони")
					{{ $row[$day['id']]; }}
				@else
					{{ $row[$day['id']]; }}
				@endif
				</td>
			@else
				<td>
					{{ '0' }}
				</td>
			@endif
		@endforeach
		<td>{{ '0' }}</td>
		<td>{{ '0' }}</td>
	</tr>
	@endforeach
	<tr>
		<td>Жами:</td>
		<td></td>
		<td>{{ '0' }}</td>
		<td>{{ '0' }}</td>
	</tr>
	</tbody>
</table>        