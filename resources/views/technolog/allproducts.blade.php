@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
        @foreach($products as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="far fa-lemon fs-1 primary-text border rounded-full secondary-bg p-2" style="color:chocolate"></i>
                <div class="text-center">
                    <p class="fs-4" style="font-size: 18px !important;">{{$row['product_name']}}</p>
                    <a href="{{ route('technolog.settingsproduct',  ['id' => $row->id ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                    @foreach($row->shop as $shop)
                    <span style="font-size: 11px; color:darkgreen">{{ $shop->shop_name; }}</span>
                    @if($loop->index < count($row->shop)-1)
                    <span>{{ ', ' }}</span>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection