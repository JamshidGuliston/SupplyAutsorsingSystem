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
	 @page { margin: 0.2in 0.2in 0in 0.6in; }
	body{
		font-family: DejaVu Sans;
		font-size:10px;
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
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="table" id="table_with_data">
					<div class="col-md-6">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>{{ "№ "."______"}}</b>
						<b>{{ " / Cана: " }}</b>
					</div>
                </div>
                <hr>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 4%;">ТР</th>
                            <th scope="col" style="width: 20%;">Махсулотлар</th>
                            <th scope="col" style="width: 5%;">Ўлчов</th>
                            @foreach($regions as $key => $region)
                                <th scope="col" style="width: 8%;">{{ $region['short_name'] }}</th>
                            @endforeach
                            <th scope="col" style="width: 8%;">Миқдори</th>
                            <th scope="col" style="width: 8%;">Қолдиқ</th>
                            <th scope="col" style="width: 8%;">Керак<br/>бўлади</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $tr =1; 
                            $allm = array();
                            $counts = [];
                        ?>
                        @foreach($items as $product_id => $row)
                        @php $summ = 0; @endphp
                        <tr>
							<th scope="row">{{ $tr++ }}</th>
                            <!-- maxsulotni nomi -->
                            <td style="text-align: left; padding-left: 3px;">{{ mb_substr($row['product_name'], 0, 25) }}</td>
							<td>{{ $row['size_name'] }}</td>
                            @foreach($regions as $key => $region)
                                @php
                                    $weight = isset($row[$key]['product_weight']) ? $row[$key]['product_weight'] : 0;
                                    $summ += $weight;
                                @endphp
                                <td>{{ $weight > 0 ? number_format($weight, 1, '.', '') : '' }}</td>
                            @endforeach
							<td><b>{{ number_format($row['total_weight'] ?? $summ, 1, '.', '') }}</b></td>
							<td>{{ number_format($row['qoldiq'] ?? 0, 1, '.', '') }}</td>
							<td style="{{ ($row['farq'] ?? 0) > 0 ? 'color: red; font-weight: bold;' : '' }}">
                                {{ number_format($row['farq'] ?? 0, 1, '.', '') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top: 15px;">
				<div class="column">
					<img src="images/qrmanzil.jpg" alt="QR-code" width="140">
				</div>
				<div class="column">
					<p style="text-align: right;"><strong>Қабул қилувчи: </strong> __________________;</p>
				</div>
			</div>
        </div>
    </div>
</bod>
<html>