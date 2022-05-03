@extends('layouts.app')

@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="deleteModalas" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.deletetitlemenuid')}}" method="POST">
            @csrf
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body deletefood">
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn dele btn-danger">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- DELET -->
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
        @foreach($regions as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="fas fa-map-marker-alt fs-1 primary-text border rounded-full secondary-bg p-2" style="color:chocolate"></i>
                <div class="text-center">
                    <p class="fs-4" style="font-size: 18px !important;"><a href="/accountant/bycosts/{{$row['id']}}" >{{$row['region_name']}}</a></p>
                        <!-- <a href="/accountant/menuitem/{{$row['id']}}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="far fa-edit"></i></a> -->
                    <!-- <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                    <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-eye"></i></a>
                    <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-minus-circle menudel" data-menu-id="{{ $row['id'] }}" data-menuname-id = "" style="color: #da1313; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#deleteModalas"></i></a> -->
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <a href="/accountant/home">Orqaga</a>
</div>
@endsection

@section('script')
<script>
    $('.menudel').click(function(){
            var menuid = $(this).attr('data-menu-id');
            var menuname = $(this).attr('data-menuname-id');
            var div = $('.deletefood');
            div.html("<input type='hidden' name='menuid' value="+menuid+"><p>"+menuname+"ни ўчирмоқчимисиз? </p>");  
        });
</script>
@endsection