@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="container-fluid px-4">
    <div style="text-align: end;">
        <a href="/technolog/addshop">+ qo'shish</a>
    </div>
    <div class="row g-3 my-2">
        @foreach($shops as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="fas fa-store-alt fs-1 primary-text border rounded-full secondary-bg p-2" style="color:chocolate"></i>
                <div class="text-center">
                    <p class="fs-4" style="font-size: 18px !important;">{{$row['shop_name']}}</p>
                    <a href="{{ route('technolog.shopsettings',  ['id' => $row->id ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection