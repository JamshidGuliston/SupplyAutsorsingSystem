@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
        @foreach($seasons as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="fas fa-cloud fs-1 primary-text border rounded-full secondary-bg p-2" style="color:chocolate"></i>
                <div class="text-center">
                    <p class="fs-4" style="font-size: 18px !important;"><a href="/technolog/menus/{{$row['id']}}">{{$row['season_name']}}</a></p>
                    <a href="{{ route('foodsettings',  ['id' => $row->id ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <hr>
    <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
            <div class="text-center">
				<a href="/technolog/updatemanu">Menyuni o'zgartirish</a>
			</div>
        </div>
    </div>
</div>
@endsection