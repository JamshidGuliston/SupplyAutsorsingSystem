@extends('layouts.app')

@section('leftmenu')
@include('boss.sidemenu'); 
@endsection
@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
@endsection
@section('content')
<div class="modal editesmodal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">Qabul qilish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body editesproduct">
                
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn editsub btn-warning">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="date">
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> 0]) }}" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> $month->id]) }}" class="month__item {{ ( $month->id == $id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> 0]) }}" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>

<div class="container-fluid px-4">
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
            @foreach($regions as $region)
                <th colspan="6">{{ $region->region_name }}</th>
            @endforeach
                <th scope="col">Chiqidi</th>
                <th scope="col">So'm</th>
            </tr>
        </thead>
        
    </table>
    <br>  
</div>
@endsection

@section('script')

@if(session('status'))
<script> 
    // alert('{{ session("status") }}');
    swal({
        title: "Ajoyib!",
        text: "{{ session('status') }}",
        icon: "success",
        button: "ok",
    });
</script>
@endif
@endsection