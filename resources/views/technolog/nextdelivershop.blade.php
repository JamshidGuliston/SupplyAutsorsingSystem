@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <a href="#">
                <i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
            <b>{{ $shop['shop_name'] }}</b>
        </div>
        <div class="col-md-3">
            <b>Telegram orqali yuborish</b>
            <a href="/technolog/sendordertooneshop/{{ $shop['id'] }}">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
        </div>
        <div class="col-md-3" style="text-align: center;">
            <b>PDF </b>
            <a href="/technolog/nextdayshoppdf/{{ $shop['id'] }}" target="_blank">
                <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                @foreach($shop->product as $age)
                <th scope="col" colspan="2">{{ $age->product_name}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php 
                $tr =1; 
                $allm = array();
            ?>
            @foreach($shopproducts as $row)
            <tr>
                <th scope="row">{{ $tr++ }}</th>
                <td>{{ $row['name'] }}</td>
                @foreach($shop->product as $age)
                <?php
                    if($row[$age->id] > 0){
                        $weight = $row[$age->id];
                        $decimalPart = $weight - floor($weight);
                        // agar kasr qismi 0.45 yoki undan katta bo'lsa, yuqoriga olinsin
                        $result  = ($decimalPart >= 0.4444444)
                                    ? ceil($weight)
                                            : floor($weight);
                        $result = $result > 0 ? $result : 1;
                    }else{
                        $result = 0;
                        $weight = 0;
                    }
                ?>
                    <td scope="col"><?php printf("%01.1f", $result); ?></td>
                    <td scope="col"><?php printf("%01.3f", $weight); ?></td>
                @endforeach
            </tr>
            @endforeach
            <!-- <tr>
                <th></th>
                <td></td>
                @foreach($shop->product as $age)
                    <td scope="col"> <b>Jami:</td>
                @endforeach
            </tr> -->
        </tbody>
    </table>
</div>

@endsection

@section('script')

@endsection