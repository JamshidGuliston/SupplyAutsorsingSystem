@extends('layouts.app')

@section('leftmenu')
<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-project-diagram me-2"></i>Projects</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-chart-line me-2"></i>Analytics</a>
    <a href="/technolog/seasons" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/seasons') ? 'active' : null }}"><i class="fas fa-paste"></i> Menyular</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/allproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allproducts') ? 'active' : null }}"><i class="fas fa-carrot"></i> Products</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
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
            <a href="/technolog/#">
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
                <th scope="col">{{ $age->product_name}}</th>
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
                    <td scope="col">{{ $row[$age->id] }}</td>
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