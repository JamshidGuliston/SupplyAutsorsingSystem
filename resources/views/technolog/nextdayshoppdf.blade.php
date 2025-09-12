<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="Description" content="Enter your description here"/>
<title>Shop PDF</title>
<style>
    @page { 
        margin: 0.2in 0.8in 0in 0.3in; 
    }
    
    body {
        font-family: DejaVu Sans;
        font-size: 12px;
        width: 100%;
    }
    
    .page-break {
        page-break-before: always;
    }
    
    .region-header {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    .shop-header {
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    table {
        border-collapse: collapse;
        border: 1px solid black;
        width: 100%;
        margin-bottom: 30px;
    }
    
    thead {
        border: 1px solid black;
    }
    
    td, th {
        text-align: center;
        border: 1px solid black;
        padding: 5px;
        word-wrap: break-word;
    }
    
    th {
        font-weight: bold;
    }
    
    .kindergarten-name {
        text-align: left;
        font-weight: bold;
    }
    
    .total-row {
        font-weight: bold;
    }
    
    .total-row td {
        border-top: 1px solid black;
    }
</style>
</head>
<body>
    @foreach($groupedByRegions as $regionId => $regionData)
        @if(!$loop->first)
            <div class="page-break"></div>
        @endif
        
        <div class="region-header">
            {{ $regionData['region_name'] }}
        </div>
        
        <div class="shop-header">
            <i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
            <b>{{ $shop['shop_name']."     sana: ".$day->day_number."-".$day->month_name }}</b>
        </div>
        
        <hr>
        
        <table>
            <thead>
                <tr>
                    <th scope="col" style="width: 6%;">ID</th>
                    <th scope="col" style="width: 35%;">MTT-номи</th>
                    @foreach($shop->product as $product)
                        <th scope="col" style="font-size: 12px;">{{ $product->product_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <?php 
                    $tr = 1; 
                    $counts = [];
                ?>
                
                @foreach($regionData['kindergartens'] as $kindergartenId => $kindergartenData)
                    <tr>
                        <th scope="row">{{ $tr++ }}</th>
                        <td class="kindergarten-name">{{ $kindergartenData['name'] }}</td>
                        @foreach($shop->product as $product)
                            @if(!isset($counts[$product->id]))
                                <?php $counts[$product->id] = 0; ?>
                            @endif
                            
                            <?php
                            $result = 0;
                            if(isset($kindergartenData[$product->id]) && $kindergartenData[$product->id] > 0){
                                $result = $kindergartenData[$product->id];
                                if($product->size_name_id == 3 || $product->size_name_id == 2){ 
                                    $result = round($result);
                                } else {
                                    $result = round($result, 1);
                                }
                            }
                            ?>
                            
                            <td scope="col"><?php echo $result; ?></td>
                            <?php
                            $counts[$product->id] += $result; 
                            ?>
                        @endforeach
                    </tr>
                @endforeach
                
                <!-- Region uchun jami qator -->
                <tr class="total-row">
                    <th scope="row"></th>
                    <td><b>{{ $regionData['region_name'] }} ХУДУДИ ЖАМИ:</b></td>
                    @foreach($shop->product as $product)
                        <td><b><?php printf("%01.0f", $counts[$product->id]); ?></b></td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>