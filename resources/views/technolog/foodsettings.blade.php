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
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="py-5 px-5">
    <h2>{{ $food->food_name }}</h2>
    <form method="POST" action="{{route('updatefood')}}">
        @csrf
        <input type="hidden" name="foodid" value="{{ $food->id }}" >
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Сатегория</label>
            <div class="col-sm-10">
                <select class="form-select" name="catid" aria-label="Default select example">
                    @foreach($categories as $row)
                    @if($food->food_cat_id == $row->id)
                    <option selected value="{{$row['id']}}">{{$row['food_cat_name']}}</option>
                    @else
                    <option value="{{$row['id']}}">{{$row['food_cat_name']}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Овқат вақти</label>
            <div class="col-sm-10">
                <select class="form-select" name="timeid" aria-label="Default select example" required>
                    <option value="" selected>--Tanlang--</option>
                    @if($food->meal_time_id == 0)
                    	<option selected value="0">Барчасига</option>
                    @else
                    	<option value="0">Барчасига</option>
                    @endif
                    @foreach($times as $row)
                    @if($food->meal_time_id == $row->id)
                    	<option selected value="{{$row['id']}}">{{$row['meal_time_name']}}</option>
                    @else
                    	<option value="{{$row['id']}}">{{$row['meal_time_name']}}</option>
                    @endif
                    @endforeach
                </select>
            </div>

        </div>
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Вазни: </label>
            <div class="col-sm-10">
                <input type="text" name="weight" value="{{ $food->food_weight }}" class="form-control" id="staticEmail" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </div>
    </form>
    <a href="/technolog/food">Orqaga</a>
</div>
@endsection


@section('script')


@endsection