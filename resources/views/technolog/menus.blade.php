@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
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
    <div style="text-align: end;">
        <a href="/technolog/addtitlemenu/{{ $id }}">+ qo'shish</a>
    </div>
    <div class="row g-3 my-2">
        @foreach($menus as $row)
        <!-- Parent menyu -->
        <div class="col-md-12 mb-3">
            <div class="row">
                <div class="col-md-2">
                    <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded" style="border-left: 4px solid #667eea;">
                        <i class="fas fa-utensils fs-1 primary-text border rounded-full secondary-bg p-2" style="color:chocolate"></i>
                        <div class="text-center">
                            <p class="fs-4 mb-0" style="font-size: 18px !important;">{{$row['menu_name']}}</p>
                            <small class="text-muted"><i class="fas fa-folder"></i> Parent</small>
                            <div class="mt-2">
                                @if($row->us == "1")
                                    <i class="fas fa-bullseye" style="color: #22aa6b; margin-right: 6px; font-size: 20px;"></i>
                                    <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                                    <a href="/technolog/menuitemshow/{{$row['id']}}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-eye"></i></a>
                                @else
                                    <a href="/technolog/menuitem/{{$row['id']}}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="far fa-edit"></i></a>
                                    <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-minus-circle menudel" data-menu-id="{{ $row['id'] }}" data-menuname-id = "{{ $row['menu_name']}} " style="color: #da1313; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#deleteModalas"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Child menyular -->
                @if($row->children && count($row->children) > 0)
                    @foreach($row->children as $child)
                    <div class="col-md-2">
                        <div class="p-3 bg-light shadow-sm d-flex flex-column justify-content-around align-items-center rounded" style="border-left: 2px solid #ccc; margin-left: 10px;">
                            <i class="fas fa-utensils fs-1 primary-text border rounded-full secondary-bg p-2" style="color:#ff9800"></i>
                            <div class="text-center">
                                <p class="fs-5 mb-0" style="font-size: 16px !important;">{{$child['menu_name']}}</p>
                                <small class="text-muted"><i class="fas fa-file"></i> Child</small>
                                <div class="mt-2">
                                    @if($child->us == "1")
                                        <i class="fas fa-bullseye" style="color: #22aa6b; margin-right: 6px; font-size: 18px;"></i>
                                        <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 18px;"><i class="fas fa-cog"></i></a>
                                        <a href="/technolog/menuitemshow/{{$child->id}}" style="color: #959fa3; margin-right: 6px; font-size: 18px;"><i class="fas fa-eye"></i></a>
                                    @else
                                        <a href="/technolog/menuitem/{{$child->id}}" style="color: #959fa3; margin-right: 6px; font-size: 18px;"><i class="far fa-edit"></i></a>
                                        <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 18px;"><i class="fas fa-minus-circle menudel" data-menu-id="{{ $child->id }}" data-menuname-id = "{{ $child->menu_name }} " style="color: #da1313; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#deleteModalas"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <a href="/technolog/seasons">Orqaga</a>
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