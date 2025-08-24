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
	 @page { margin: 0.1in 0.1in 0in 0.1in; }
	body{
		font-family: DejaVu Sans;
		font-size:8px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width: 100%;
	}
	table{
		border-collapse: collapse;
		border: 0.5px solid black;
		width: 100%;	
	}
	thead{
		border: 0.5px solid black;
	}
	td {
		text-align: center;
		width: auto;
		overflow: hidden;
		word-wrap: break-word;
	}
	th{
		border: 0.5px solid black;
		padding: 0px;
	}
	td{
		border-right: 0.5px solid black;
		border-bottom: 0.5px solid black;
		padding: 0px;
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
	.column {
		float: left;
		text-align: center;
		width: 33%;
	}

	/* Clear floats after the columns */
	.row:after {
		content: "";
		display: table;
		clear: both;
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
					<h2 style="text-align: center; background-color: #f0f0f0; padding: 1px; border-radius: 5px;"> {{ $region }} {{ $orderTitle }}</h2>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">ТР</th>
                            <th scope="col" style="width: 25%;">Махсулотлар</th>
                            <th scope="col" style="width: 7%;">шт</th>
                            @foreach($kindergartens as $kID => $kindergarten)
								@if($kindergarten['region_id'] == $key)
                                <th scope="col" style="width: 5%;">{{ $kindergarten['number_of_org'] }}</th>
								@endif
                            @endforeach
                            <th scope="col" style="width: 15%;">Жами</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $tr =1; 
                            $allm = array();
                            $counts = [];
                        ?>
                        @foreach($productData as $row)
                        @php $summ = 0; @endphp
                        <tr>
							<th scope="row">{{ $tr++ }}</th>
                            <!-- maxsulotni nomi boshlanish uchta so'zini ko'rsatish -->
                            <td><b>{{ mb_substr($row['name'], 0, 15) }}</b></td>
							<td>{{ $row['unit'] }}</td>
                            @foreach($kindergartens as $kID => $kindergarten)
							@if($kindergarten['region_id'] == $key)
								@if(isset($counts[$kID]) && $row['unit_id'] != 3)
									@php $counts[$kID] += $row['kindergartens'][$kID] ?? 0; @endphp
								@elseif($row['unit_id'] != 3)
									@php $counts[$kID] = $row['kindergartens'][$kID] ?? 0; @endphp
								@endif
                                <td><?php printf("%01.1f", $row['kindergartens'][$kID] ?? 0); ?></td>
                                @php $summ += $row['kindergartens'][$kID] ?? 0; @endphp
							@endif
                            @endforeach
							<td><b><?php printf("%01.1f", $summ); ?></b></td>
                        </tr>
                        @endforeach
						<tr>
							<td colspan="3">Jami</td>
							@foreach($kindergartens as $kID => $kindergarten)
								@if($kindergarten['region_id'] == $key)
								<td><?php printf("%01.1f", $counts[$kID] ?? 0); ?></td>
							@endif
						</tr>
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top: 15px;">
			
			</div>
        </div>
    </div>
	@endforeach
</bod>
<html>