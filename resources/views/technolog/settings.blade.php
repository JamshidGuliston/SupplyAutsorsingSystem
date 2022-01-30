@extends('layouts.app')

@section('css')
<style>
    form {
        width: 85%;
        margin-top: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group .btn {
        width: 100%;
        background-color: #2f8d2f;
    }
</style>
@endsection

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
<div class="py-5 px-5">
    <h2>{{ $garden->kingar_name }}</h2>
    <form method="POST" action="{{route('updategarden')}}">
        @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nomi</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="kinname" id="staticEmail" value="{{ $garden->kingar_name }}" required>
                <input type="hidden" class="form-control" name="kinname_id" id="staticEmail" value="{{ $garden->id }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Parol</label>
            <div class="col-sm-10">
                <input type="text" name="kinparol" class="form-control" id="staticEmail" value="{{ $garden->kingar_password }}" required>
            </div>
        </div>
        <div class="form-group row">

            <label for="inputPassword" class="col-sm-2 col-form-label">Bolalar guruhi</label>
            @foreach($ages as $rows)
            <?php $i = 1; ?>
            <div class="col-sm-2">
                @foreach($garden->age_range as $b)
                @if($b->id == $rows->id)
                <?php $i = 0; ?>
                <input checked class="form-check-input" name="yongchek[]" type="checkbox" id="inlineCheckbox1" value="{{$rows['id']}}">
                @endif
                @endforeach
                @if($i)
                <input class="form-check-input" name="yongchek[]" type="checkbox" id="inlineCheckbox1" value="{{$rows['id']}}">
                @endif
                <label class="form-check-label" for="inlineCheckbox1">{{$rows['age_name']}}</label>
            </div>
            @endforeach
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Xodimlar soni</label>
            <div class="col-sm-10">
                <input type="number" name="worker" class="form-control" value="{{ $garden->worker_count }}" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Xodimlar menyusi</label>
            <div class="col-sm-10">
            <select class="form-select" name="worker_age_id" aria-label="Default select example" required>
                <option selected value="" requ>Tanlang</option>
                @foreach($ages as $rows)
                @if($garden->worker_age_id == $rows['id'])
                    <option selected value="{{$rows['id']}}">{{$rows['age_name']}}</option>
                @else
                    <option value="{{$rows['id']}}">{{$rows['age_name']}}</option>
                @endif
                @endforeach
            </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Tumanlar</label>
            <div class="col-sm-10">
                <select class="form-select" name="region" aria-label="Default select example" required>
                    @foreach($regions as $region)
                    @if($garden->region_id == $region->id)
                    <option selected value="{{$region['id']}}">{{$region['region_name']}}</option>
                    @else
                    <option value="{{$region['id']}}">{{$region['region_name']}}</option>
                    @endif

                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Ish faoliyati</label>
            <div class="col-sm-10">
                <input type="number" required name="hide" class="form-control" value="{{ $garden->hide}}">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </div>
    </form>
</div>
@endsection


@section('script')


@endsection