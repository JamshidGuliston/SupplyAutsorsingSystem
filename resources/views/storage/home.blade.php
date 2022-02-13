@extends('layouts.app')

@section('leftmenu')
<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="/storage/addproductform" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('storage/addproducts') ? 'active' : null }}"><i class="fas fa-plus"></i> Maxsulot qo'shish</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- end date -->
    <div class="row g-3 my-2">
        @foreach($products as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="fas fa-seedling fs-1 primary-text border rounded-full secondary-bg p-2"></i>
                <div class="text-center">
                    <h5 class="fs-3 mb-0 mt-1">{{ $row['weight']; }}</h5>
                    <p class="fs-4" style="font-size: 18px !important;">{{ $row['p_name']; }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection