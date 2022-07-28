@extends('layouts.app')

@section('leftmenu')
    @include('accountant.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- end date -->
    <div class="row g-3 my-2">
        @foreach($products as $row)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <!-- <i class="fas fa-seedling fs-1 primary-text border rounded-full secondary-bg p-2"></i> -->
                <div class="text-center" style="width: 100%">
                    <p class="fs-4" style="font-size: 22px !important;">{{ $row['p_name']; }}</p>
                <p>
                    <i class='fas fa-arrow-down' style='color: green; width: 5%'></i><?php echo $row['weight'].' '.$row['size_name'] ?> <i class='fas fa-arrow-up' style='color: red; width: 5%'></i><?php echo round($row['minusweight'], 1).' '.$row['size_name'] ?>
                </p>
                <h4 class="fs-3 mb-0 mt-1">{{ round($row['weight'] - $row['minusweight'], 1).' '.$row['size_name']; }}</h4>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection