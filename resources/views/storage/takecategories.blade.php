@extends('layouts.app')

@section('css')

@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- ADD -->
<!-- Modal -->
<div class="modal editesmodal" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/add_takecategory" method="post">
		    @csrf
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="exampleModalLabel">Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Modal -->
<div class="modal editeModal" id="editeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/update_takecategory" method="post">
		    @csrf
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="exampleModalLabel">O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editebody">
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- DELETE -->
<!-- Modal -->
<div class="modal deleteModal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/delete_takecategory" method="post">
		    @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body deletebody">
                
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-warning">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div class="py-4 px-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Qo'shish</button>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th style="width: 40px;">...</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->outside_name }}</td>
                    <td>
                        <a href="#" style="color: #959fa3; margin-right: 6px; font-size: 20px;">
                            <i class="fas fa-minus-circle deletename" data-nameid-id="{{ $row['id'] }}" data-name-id = "{{ $row['outside_name']}}" style="color: #da1313; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#deleteModal"></i>
                        </a>
                        <a href="#" style="color: blue; margin-right: 6px; font-size: 20px;">
                            <i class="fas fa-edit editename" data-nameid-id="{{ $row['id'] }}" data-name-id = "{{ $row['outside_name']}}" style="color: #2f9bc3; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editeModal"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home">Orqaga</a>
</div>

@endsection

@section('script')
<script>
    $('.editename').click(function(){
            var id = $(this).attr('data-nameid-id');
            name = $(this).attr('data-name-id');
            var div = $('.editebody');
            div.html("<input type='hidden' name='nameid' value="+id+"><input type='text' name='title' class='form-control' value="+name+" required>");  
    });
    $('.deletename').click(function(){
        var id = $(this).attr('data-nameid-id');
        var name = $(this).attr('data-name-id');
        var div = $('.deletebody');
        div.html("<input type='hidden' name='nameid' value="+id+"><p>"+name+" ni o'chirmoqchimisiz? </p>");  
    });
</script>
@endsection