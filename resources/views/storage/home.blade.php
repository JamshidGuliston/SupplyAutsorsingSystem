@extends('layouts.app')

@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
@endsection
@section('content')
<!-- Modal -->
<div class="modal fade" id="delModal" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bu mahsulotni o'chirasizmi
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button">O'chirish</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->
<div class="date">
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="/storage/home/{{ $year->id-1 }}/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/storage/home/{{ $year->id }}/{{ $month->id }}" class="month__item {{ ( $month->id == $id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/storage/home/{{ $year->id+1 }}/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>
<div class="container-fluid px-4">
    <!-- end date -->
    <div class="row g-3 my-2">
        @foreach($products as $key => $row)
        @php
            $percentage = $row['weight'] > 0 ? ($row['minusweight'] / $row['weight']) * 100 : 0;
            $cardClass = $percentage >= 80 ? 'border-danger bg-danger bg-opacity-10' : 'bg-white';
        @endphp
        <div class="col-md-3">
            <div class="p-3 {{ $cardClass }} shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <!-- <i class="fas fa-seedling fs-1 primary-text border rounded-full secondary-bg p-2"></i> -->
                <div class="text-center" style="width: 100%">
                    <p class="fs-4" style="font-size: 22px !important;">
                        {{ $row['p_name']; }} 
                        <!-- <i class='fas fa-minus' style='color: red; cursor: pointer' data-delet-id='{{$key}}' data-bs-toggle='modal' data-bs-target='#delModal'></i> -->
                    </p>
                <p>
                    <i class='fas fa-arrow-down' style='color: green; width: 5%'></i><?php echo $row['weight'].' '.$row['size_name'] ?> <i class='fas fa-arrow-up' style='color: red; width: 5%'></i><?php echo round($row['minusweight'], 1).' '.$row['size_name'] ?>
                </p>
                <h4 class="fs-3 mb-0 mt-1">{{ round($row['weight'] - $row['minusweight'], 1).' '.$row['size_name']; }} </h4> 
                @if($percentage >= 80)
                    <small class="text-danger fw-bold">⚠️ {{ round($percentage, 1) }}% chiqim</small>
                @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection