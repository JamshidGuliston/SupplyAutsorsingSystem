@extends('layouts.app')

@section('leftmenu')
    @include('storage.sidemenu'); 
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
    {{ $month_id }}
</div>
@endsection