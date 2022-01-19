@extends('layouts.app')

@section('leftmenu')
<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-project-diagram me-2"></i>Projects</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-chart-line me-2"></i>Analytics</a>
    <a href="/technolog/seasons" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/seasons') ? 'active' : null }}"><i class="fas fa-paste"></i> Menyular</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/allproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allproducts') ? 'active' : null }}"><i class="fas fa-carrot"></i> Products</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
@endsection

@section('content')
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
    })
</script>
@endsection