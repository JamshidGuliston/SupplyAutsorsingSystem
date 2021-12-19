<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="Description" content="Enter your description here"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<title>Title</title>
<style>
	/*th { */
 /*         color: SaddleBrown; */
 /*         width:50px;  */
	/*}*/
	td {
		text-align: center;
	}
	.vrt-header span{
	  writing-mode: vertical-rl;
	  transform: rotate(180deg);
	  text-align: left;
	  min-width: 50px; /* for firefox */
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
                    <table class="table table-bordered" id="tabsle_with_data">
                        <thead>
                          <tr>
                          	 <th scope="col" ></th>
                          	 <th scope="col" >Mahsulotlar nomi</th>
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
                          			<th scope="col" class='vrt-header'><?php echo '<span>'.$items->product_name. '</span>';?></th>
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
			                        				<th scope="row" rowspan="<?php echo 2*$mealtime[$tree[$i]['time']]; ?>" class='vrt-header'><?php echo '<span>'.$tree[$i]['time']. '</span>'; ?></th>
			                            <?php } ?>
			                            <th scope="row" rowspan="2" class="align-baseline" style="padding: 2px;"><?php echo $tree[$i]['food_name'] ?></th>
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
                      <br>
                      <input type="button" onclick="javascript:htmltopdf()" value="Export">
                      </br>
                      <!--<button onclick="generateExcel()">Export to Excel</button>-->
                </div>
            </div>
        </div>
    </div>
 

</body>
</html>