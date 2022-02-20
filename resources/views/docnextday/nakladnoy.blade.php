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
		font-size:16px;
		/* background-image: url(images/bg.jpg); */
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width:100%;
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
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="table" id="table_with_data">

                <div class="col-md-6">
                    <a href="#">
                        <i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
                    </a>
                    <b>{{ $king['kingar_name'] }}</b>
                </div>
                <div class="col-md-3">
                    
                </div>
                <div class="col-md-3" style="text-align: center;">
                    
                </div>
                </div>
                <hr>
                <table style="width:100%; table-layout: fixed;">
                    <thead>
                        <tr style="width: 15%;">
                            <th scope="col" style="width: 6%;">ID</th>
                            <th scope="col" style="width: 35%;">Махсулотлар</th>
                            @foreach($ages as $age)
                            <th scope="col">{{ $age->age_name }}</th>
                            @endforeach
							<th>Ходимлар</th>
                            <th>Жами</th>
                        </tr>
                    </thead>
                    <?php $tr = 1; ?>
                    <tbody>
                    	@foreach($productscount as $key => $row)
                    	@if(isset($row['product_name']))
                        <tr>
                            <td scope="row">{{ $tr++ }}</td>
                    		<td>{{ $row['product_name'] }}</td>
                    		<?php $summ = 0; ?>
                    		@foreach($ages as $age)
                    			@if(isset($row[$age['id'].'-children']))
								<!-- $row[$age['id']].'*'.$row[$age['id'].'-children'].'='. -->
                            	<td scope="col">{{ ($row[$age['id']]*$row[$age['id'].'-children']) / $row[$age['id'].'div']  }}</td>
                            	<?php $summ += ($row[$age['id']]*$row[$age['id'].'-children']) / $row[$age['id'].'div']; ?>
                            	@else
                            	<td></td>
                            	@endif
                            @endforeach
							<td>
								@if(isset($workproduct[$key.'wcount']))
									<!-- $workproduct[$key].'*'.$workproduct[$key.'wcount'].'='. -->
									{{ ($workproduct[$key]*$workproduct[$key.'wcount']) / $workproduct[$key.'div'] }}
									<?php $summ += ($workproduct[$key]*$workproduct[$key.'wcount']) / $workproduct[$key.'div']; ?>
							    @endif
							</td>
                            <td>{{ $summ }}</td>
                        </tr>
                        @endif
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</bod>
<html>