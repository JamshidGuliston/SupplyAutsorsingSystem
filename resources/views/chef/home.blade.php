@extends('layouts.app')

@section('leftmenu')
@include('chef.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
    @if(intval(date("H")) >= 8)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
            SHOW
            </div>
        </div>
    @endif
    </div>
</div>
@endsection