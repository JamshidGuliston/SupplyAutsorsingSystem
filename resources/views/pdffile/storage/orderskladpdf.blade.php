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
	 @page { margin: 0.2in 0.2in 0in 0.3in; }
	body{
		font-family: 'DejaVu Sans', 'Arial Unicode MS', 'Arial', sans-serif;
		font-size:12px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width: 100%;
	}
	.header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .header p {
            color: #7f8c8d;
            margin: 0px 0;
        }
		table {
			border-collapse: collapse;
			border: 2px solid black;
			width: 100%;    
			table-layout: auto; /* fixed emas */
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
		border-right: 1px dashed black;
		border-bottom: 1px solid black;
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
			<!-- <div class="header">
				<h1>Qabul qilingan maxsulotlari ro'yxati</h1>
				<p>Sana: {{ $document[0]['created_at'] }}</p>
			</div> -->
            <div class="col-md-6">
                <div class="table" id="table_with_data">
					<div class="col-md-6">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>{{ "№ ".$document[0]['add_group_id'] }}</b>
					</div>
                </div>
                <hr>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 6%;">TR</th>
                            <th scope="col" style="width: 30%;">Maxsulotlar</th>
                            <th scope="col" style="width: 10%;">O'lcham</th>
                            <th scope="col" style="width: 10%;">Miqdori</th>
                            <th scope="col" style="width: 10%;">Narx (so'm)</th>
                            <th scope="col" style="width: 15%;">Jami (so'm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $tr =1; 
                            $allm = array();
                            $counts = [];
                        ?>
                        @php $total_sum = 0; @endphp
                        @foreach($document as $row)
                        @php $total_sum += $row["cost"] * $row["weight"]; @endphp
                        <tr>
                            <th scope="row">{{ $tr++ }}</th>
                            <td>{{ $row["product_name"] }}</td>
							<td>{{ $row["size_name"] }}</td>
							<td>{{ $row["weight"] }}</td>
							<td>{{ number_format($row["cost"], 0, ',', ' ') }}</td>
							<td>{{ number_format($row["cost"] * $row["weight"], 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                        <tr style="border-top: 2px solid black;">
                            <td colspan="4" style="text-align: right;"><strong>Jami summa:</strong></td>
                            <td colspan="2" style="text-align: center;"><strong>{{ number_format($total_sum, 0, ',', ' ') }} so'm</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top: 15px;">
				<div class="column">
					@php
						$qrImagePath = public_path('images/qrmanzil.jpg');
					@endphp
					@if(file_exists($qrImagePath))
						@php
							$qrImage = base64_encode(file_get_contents($qrImagePath));
						@endphp
						<img src="data:image/jpeg;base64,{{ $qrImage }}" alt="QR-code" width="140">
					@else
						<div style="width: 140px; height: 140px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
							<span style="font-size: 12px; color: #6c757d;">QR kod</span>
						</div>
					@endif
				</div>
				<div class="column">
					<p style="text-align: right;"><strong>Қабул қилувчи: </strong> __________________;</p>
				</div>
			</div>
        </div>
    </div>
</bod>
<html>