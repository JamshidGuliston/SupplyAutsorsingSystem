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
	 @page { margin: 0.2in 0.8in 0in 0.3in; }
	body{
		font-family: DejaVu Sans;
		font-size:12px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width: 100%;
	}
	table{
		border-collapse: collapse;
		border: 2px solid black;
		width: 100%;	
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
            <div class="col-md-6">
                <div class="table" id="table_with_data">
					<div class="col-md-6">
						<a href="#">
							<i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
						</a>
						<b>{{ "№ ".time() % 100000 }}</b>
						<b>{{ " / Cана: ".date('m/d/Y', time()) }}</b>
					</div>
                </div>
                <hr>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 6%;">TR</th>
                            <th scope="col" style="width: 30%;">Махсулотлар</th>
                            <th scope="col" style="width: 15%;">Ўлчам</th>
							<!-- o'tgan oydan qoldiq -->
							<th scope="col" style="width: 15%;">Ўтган ойдан қолдиқ</th>
                            <th scope="col" style="width: 15%;">Юборилган</th>
                            <th scope="col" style="width: 15%;">Сарфланган</th>
                            <th scope="col" style="width: 10%;">Фарқи</th>
                            <th scope="col" style="width: 10%;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $tr =1; 
                            $allm = array();
                            $counts = [];
                        ?>
                        @foreach($items as $row)
                        <tr>
							<th scope="row">{{ $tr++ }}</th>
                            <td>{{ $row['product_name'] }}</td>
							<td>{{ $row['size_name'] }}</td>
							<td><?php if(!isset($prevmods[$row["id"]])){
									$prevmods[$row["id"]] = 0;
								} 
								printf("%01.3f", $prevmods[$row["id"]]) 
								?>
							</td>
							<td><?php printf("%01.3f", $row['product_weight']); ?></td>
							<td><?php if(!isset($productscount[$row["id"]])){
										$productscount[$row["id"]] = 0;
									} 
								printf("%01.3f", $productscount[$row["id"]]); ?>
							</td>
							<td><?php printf("%01.3f", $prevmods[$row["id"]] + $row['product_weight'] - $productscount[$row["id"]]); ?></td>
							<td></td>
							<td></td>
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