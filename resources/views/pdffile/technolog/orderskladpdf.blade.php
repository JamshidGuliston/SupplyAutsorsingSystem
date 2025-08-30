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
		font-size:11px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width: 45%;
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
		width: 50%;
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
						<center>
							<b>Юк хати    &nbsp;&nbsp;&nbsp;&nbsp;    № {{ $document->docid }}</b>
							@php
							  if(env('WITHOUTDATE') == 'false'){
								 echo  "   Cана: ".explode(' ', $document->order_title)[0];
							  }else{
								 echo  "   Cана: ____-____-".substr(explode(' ', $document->order_title)[0], -4);
							  }
							@endphp
						</center>
						Кимдан:<b> {{ env('COMPANY_NAME') }} </b><br>  
						Кимга: <b> {{ $document->kingar_name }}</b>
					</div>
                </div>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 6%;">TR</th>
                            <th scope="col" style="width: 35%;">Maxsulotlar</th>
                            <th scope="col" style="width: 15%;">O'lcham</th>
                            <th scope="col" style="width: 15%;">Miqdori</th>
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
                            <td style="text-align:  left; padding-left: 2px">{{ $row->product_name }}</td>
							<td>{{ $row->size_name }}</td>
							<td><?php printf("%01.2f", $row->product_weight); ?></td>
							<td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top: 15px;">
				<div class="column">
				@php
					$qrImage = base64_encode(file_get_contents(public_path('images/qrmanzil.jpg')));
				@endphp
				<img src="data:image/jpeg;base64,{{ $qrImage }}" 
					style="width:120; position:absolute; left:10px;">
				</div>
				<div class="column">
					<p style="text-align: right;">Қабул қилувчи: __________________;</p>
				</div>
			</div>
        </div>
    </div>
</bod>
<html>