@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="Modaldelete" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.deletepeople')}}" method="POST">
            @csrf
            <div id="deleteuser"></div>
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            	Userni o'chirish
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
<!-- EDIT Bog'chaga -->
<!-- Modal -->
<div class="modal editesmodal fade" id="Modalgarden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('technolog.bindgarden')}}" method="post">
                @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Bog'chaga bog'lash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                <select class="form-select" name="mname" required>
                    <option value="">Muassasa-nomi</option>
                    @foreach($gardens as $rows)
                    <option value="{{$rows['id']}}">{{$rows['kingar_name']}}</option>
                    @endforeach
                </select>
                <div id="ghidden"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-warning">Saqlash</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->

<!-- EDIT Tashkilotga-->
<!-- Modal -->
<div class="modal editesmodal fade" id="Modalshop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Yetkazuvchi sifatida bo'glash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('technolog.bindshop')}}" method="post">
            @csrf
            <div class="modal-body editesproduct">
                <select class="form-select" name="shname" required>
                    <option value="">Shop nomi</option>
                    @foreach($shops as $rows)
                    <option value="{{$rows['id']}}">{{$rows['shop_name']}}</option>
                    @endforeach
                </select>
                <div id="shhidden"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-warning">Saqlash</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->

<div class="py-4 px-4">
<table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Tashkilot</th>
                <th scope="col">user name</th>
                <th style="width: 70px;"><i class="fas fa-hotel"></i></th>
                <th style="width: 70px;"><i class="fas fa-store-alt"></i></th>
                <th style="width: 70px;"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                @if($user->garden == null)      
                    <td>{{ $user->shop->shop_name ?? 'None' }}</td>
                @elseif($user->shop == null)
                    <td>{{ $user->garden->kingar_name ?? 'None' }}</td>
                @else
                    <td>Aniqlanmagan</td>
                @endif
                <td>{{ $user->telegram_name }}</td>
                <td><i class="edites gadenedit far fa-edit text-info" data-garden-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#Modalgarden" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i></td>
                <td><i class="edites shopedit far fa-edit text-info" data-shop-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#Modalshop" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i></td>
                <td><i class="delete fas fa-minus-circle text-danger" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#Modaldelete" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.gadenedit').click(function() {
            id = $(this).attr('data-garden-id');
            h = $('#ghidden');
            h.html("<input type='hidden' name='personid' value='"+id+"' >");
        });

        $('.shopedit').click(function() {
            id = $(this).attr('data-shop-id');
            h = $('#shhidden');
            h.html("<input type='hidden' name='personid' value='"+id+"' >");
        });
        
        $('.delete').click(function() {
            id = $(this).attr('data-user-id');
            h = $('#deleteuser');
            h.html("<input type='hidden' name='personid' value='"+id+"' >");
        });
    })
</script>
@endsection