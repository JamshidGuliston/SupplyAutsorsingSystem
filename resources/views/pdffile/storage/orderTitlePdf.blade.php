<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Title</title>
<style>
	@page { margin: 0.1in 0.2in 0.2in 0.1in; }
	body{
		font-family: DejaVu Sans;
		font-size:6px;
		width: 100%;
	}
	table{
		border-collapse: collapse;
		border: 0.5px solid black;
		width: 100%;	
	}
	thead{
		border: 0.5px solid black;
		background-color: #f0f0f0;
	}
	td, th{
		text-align: center;
		border: 0.5px solid black;
		padding: 2px;
	}
	/* Qatorlarni navbat bilan ranglash */
	tbody tr:nth-child(odd) {
		background-color: #ffffff; /* oq */
	}
	tbody tr:nth-child(even) {
		background-color: #e6f2ff; /* och ko'k */
	}
	.vrt-header span{
		display: inline-block;
		transform: rotate(-90deg);
		white-space: nowrap;
	}
	.row{
		display: flex;
		flex-wrap: nowrap;
		justify-content: space-between;
	}
</style>
</head>
<body>
	@foreach($regions as $key => $region)
		@if(!$loop->first)
			<div style="page-break-before: always;"></div>
		@endif

		<div class="container-fluid">
			<div class="row mt-5">
				<div class="col-md-6">
					<div class="table" id="table_with_data">
						<div class="col-md-12">
							<h2 style="text-align: center; background-color: #f0f0f0; padding: 1px; border-radius: 5px;">
								{{ $region }} {{ $orderTitle }}
							</h2>
						</div>
					</div>
					<table style="width:100%; table-layout: fixed;">
						<thead>
							<tr>
								<th style="width: 5%;">ТР</th>
								<th style="width: 20%;">Махсулотлар</th>
								<th style="width: 7%;">шт</th>
								@foreach($kindergartens as $kID => $kindergarten)
									@if($kindergarten['region_id'] == $key)
										<th style="width: 9%;">{{ $kindergarten['number_of_org'] }}</th>
									@endif
								@endforeach
								<th style="width: 10%;">Жами</th>
							</tr>
						</thead>
						<tbody>
							@php 
								$tr = 1; 
								$counts = [];
							@endphp

							@foreach($productData as $row)
								@php 
								$summ = 0; 
								$package_size = $row['package_size'] ?? 0;
								@endphp
								<tr>
									<td>{{ $tr++ }}</td>
									<td><b>{{ mb_substr($row['name'], 0, 15) }}</b></td>
									<td>{{ ($package_size == 0) ? $row['unit'] : "Дона"  }}</td>

									@foreach($kindergartens as $kID => $kindergarten)
										@if($kindergarten['region_id'] == $key)
											@if(isset($counts[$kindergarten['id']]) && ($row['unit_id'] ?? null) != 3)
												@php $counts[$kindergarten['id']] += $row['kindergartens'][$kindergarten['id']] ?? 0; @endphp
											@else
												@if(($row['unit_id'] ?? null) != 3)
													@php $counts[$kindergarten['id']] = ($package_size == 0) ? $row['kindergartens'][$kindergarten['id']] : $row['kindergartens'][$kindergarten['id']] / $package_size; @endphp
												@endif
											@endif

											<td>{{ $package_size == 0 ? $row['kindergartens'][$kindergarten['id']] : $row['kindergartens'][$kindergarten['id']] / $package_size }}</td>
											@php $summ += ($package_size == 0) ? $row['kindergartens'][$kindergarten['id']] : $row['kindergartens'][$kindergarten['id']] / $package_size; @endphp
										@endif
									@endforeach

									<td><b>{{ number_format($summ, 0) }}</b></td>
								</tr>
							@endforeach

							<tr style="background-color: #d9edf7; font-weight: bold;">
								<td colspan="3">Jami</td>
								@foreach($kindergartens as $kID => $kindergarten)
									@if($kindergarten['region_id'] == $key)
										<td>{{ number_format($counts[$kindergarten['id']] ?? 0, 0) }}</td>
									@endif
								@endforeach
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@endforeach
</body>
</html>
