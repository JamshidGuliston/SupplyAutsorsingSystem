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
	 @page { margin: 1.1in 0.8in 0in 0.2in; }
	body{
		font-family: DejaVu Sans;
		font-size:8px;
		background-image: url(images/bg.jpg);
		background-position: top left;
		background-repeat: no-repeat;
		background-size: 100%;
		/* padding: 300px 100px 10px 100px; */
		width:100%;
		height:100%;
	}
	table{
		border-collapse: collapse;
		width: 100%;
	}
	td {
		text-align: center;
		width: auto;
		overflow: hidden;
		word-wrap: break-word;
	}
	th{
		border: 1px solid #444;
		padding: 0px;
	}
	td{
		border-right: 1px dashed black;
		border-bottom: 1px solid #444;
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
                	<?php
                		echo (($menu[0]['king_age_name_id'] == 1)? "3-4 ": "4-7 "). "ёшли болалар сони: " . $menu[0]['kingar_children_number'];
                	?>
                    <table style="width:100%; table-layout: fixed;">
                        <thead>
                          <tr>
                          	 <th style="width:2%;"></th>
                          	 <th style="width:8%;">Mahsulotlar nomi</th>
                          	 <?php  
                          	 $products = array();
                          	 $tree = array();
                          	 $prid = array();
                          	 $lo = 0;
                          	 $ind = -1;
                          	 foreach($menuitem as $items){
                          		if(empty($food1[$items->meal_time_name.$items->food_name])){
                          				$ind++;
                          		}
                          		if(empty($mealtime[$items->meal_time_name])){ 
                          			$mealtime[$items->meal_time_name] = 0;
                          			$tree[$ind]['time'] = $items->meal_time_name;
                          		}
                          		if(empty($food1[$items->meal_time_name.$items->food_name])){
                          			$mealtime[$items->meal_time_name]++;
                          			$food1[$items->meal_time_name.$items->food_name] = true;
                          			$tree[$ind]['food_name'] = $items->food_name;
                          		}
                          		if(empty($buling[$items->product_name])){
                          			array_push($products, $items->product_name);
                          			$buling[$items->product_name]=true;
                          			$prid[$items->product_name] = $lo;
                          			$lo++;
                          			?>
                          			<th class='vrt-header' style="padding: 0px; width: 3%; height: 80px"><?php echo '<span>'.$items->product_name. '</span>';?></th>
                          			<?php
                          		}
                          		$tree[$ind][$prid[$items->product_name]] = $items->product_weight;
                          	 }
                          	 //dd($tree[0]['food_name']);
                          	 ?>
                          </tr>
                        </thead>
                        <tbody>
                        	<?php
                        		for($i = 0; $i < count($tree); $i++){
                        	?>
			                        <tr>
			                        	<?php if(!empty($tree[$i]['time'])){ ?>
			                        				<th scope="row" rowspan="<?php echo 2*$mealtime[$tree[$i]['time']]; ?>" class='vrt-header' style="padding: 0px; height: 60px"><?php echo '<span>'.$tree[$i]['time']. '</span>'; ?></th>
			                            <?php } ?>
			                            <td scope="row" rowspan="2" class="align-baseline" style="padding: 2px;"><?php echo $tree[$i]['food_name'] ?></td>
			                            <?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(!empty($tree[$i][$t])){
			                            ?>
			                            		<td style="padding: 2px;"><?php echo $tree[$i][$t]; ?></td>
			                            <?php
				                            }
			                            	else{
			                            ?>
			                            		<td style="padding: 2px;"></td>
			                            <?php	
			                            	}
                    					}
			                            ?>
                    				</tr>
                    				<tr>
			                        	<?php
			                            for($t = 0; $t < count($products); $t++){
			                            	if(!empty($tree[$i][$t])){
			                            ?>
			                            		<td style="padding: 2px;"><?php echo ($menu[0]['kingar_children_number']*$tree[$i][$t])/1000.; ?></td>
			                            <?php
				                            }
			                            	else{
			                            ?>
			                            		<td style="padding: 2px;"></td>
			                            <?php	
			                            	}
                    					}
			                            ?>
                    				</tr>
                    	<?php		
                        		}
                        	?>
                     
                        </tbody>
                      </table>
                      <!-- <button><a href="/downloadPDF/1/4/1">to PDF</a></button> -->
                </div>
            </div>
        </div>
    </div>
 

</body>
</html>