@extends('layouts.app')

@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('css')
<style>
    .year {
        text-align: center;
    }
    .month,
    .day {
        margin: 10px 20px;
        display: flex;
        justify-content: left;
    }

    .month__item{
        width: calc(100% / 12);
        text-align: center;
        border-bottom: 1px solid #000;
    }

    .month__item + .month__item {
        /* border-left: 1px solid #000; */
    }
    .day__item{
        background-color: #ecf6f1;
        text-align: center;
        vertical-align: middle;
        min-width: 34px;
        padding: 5px;
        margin-left: 5px;
        border-radius: 50%;
    }

    .month__item, .day__item{
        color: black;
        cursor: context-menu;
        /* border: 1px solid #87706a; */
        text-decoration: none;
    }
    .active{
        background-color: #23b242;
        color: #fff;
    }
    .month__item:hover,
    .day__item:hover{
        background-color: #23b242;
        color: #fff;
        transition: all .5s;
        cursor: pointer;
    }
</style>
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
    <!-- <div class="year">2022</div> -->
    <div class="month">
        @foreach($months as $month)
            <a href="/storage/home/{{ $month->id }}" class="month__item {{ (Request::is('storage/home/'.$month->id) or ($month->month_active == 1 and $id == 0)) ? 'active' : null }}">{{ $month->month_name }}</a>
        @endforeach
    </div>
</div>
<div class="container-fluid px-4">
    <!-- end date -->
    <div class="row g-3 my-2">
        @foreach($products as $key => $row)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <!-- <i class="fas fa-seedling fs-1 primary-text border rounded-full secondary-bg p-2"></i> -->
                <div class="text-center" style="width: 100%">
                    <p class="fs-4" style="font-size: 22px !important;">{{ $row['p_name']; }} <i class='fas fa-minus' style='color: red; cursor: pointer' data-delet-id='{{$key}}' data-bs-toggle='modal' data-bs-target='#delModal'></i></p>
                <p>
                    <i class='fas fa-arrow-down' style='color: green; width: 5%'></i><?php echo $row['weight'].' '.$row['size_name'] ?> <i class='fas fa-arrow-up' style='color: red; width: 5%'></i><?php echo round($row['minusweight'], 1).' '.$row['size_name'] ?>
                </p>
                <h4 class="fs-3 mb-0 mt-1">{{ round($row['weight'] - $row['minusweight'], 1).' '.$row['size_name']; }} </h4> 
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection