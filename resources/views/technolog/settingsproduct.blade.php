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
    <h2>{{ $product->product_name }}</h2>
    <form method="POST" action="{{route('updateproduct')}}">
        @csrf
        <input type="hidden" name="productid" value="{{ $product->id }}" >
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Сатегория</label>
            <div class="col-sm-10">
                <select class="form-select" name="catid" aria-label="Default select example">
                    @foreach($categories as $row)
                    @if($product->category_name_id == $row->id)
                    <option selected value="{{$row['id']}}">{{$row['pro_cat_name']}}</option>
                    @else
                    <option value="{{$row['id']}}">{{$row['pro_cat_name']}}</option>
                    @endif

                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Ўлчов бирлиги</label>
            <div class="col-sm-10">
                <select class="form-select" name="sizeid" aria-label="Default select example">
                    @foreach($sizes as $row)
                    @if($product->size_name_id == $row->id)
                    <option selected value="{{$row['id']}}">{{$row['size_name']}}</option>
                    @else
                    <option value="{{$row['id']}}">{{$row['size_name']}}</option>
                    @endif

                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Ўлчов бирлигини ифодаловчи бўлувчи миқдори</label>
            <div class="col-sm-10">
                <input type="text" name="div" class="form-control" id="staticEmail" value="{{ $product->div }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Тартиби</label>
            <div class="col-sm-10">
                <input type="text" name="sort" class="form-control" id="staticEmail" value="{{ $product->sort }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Норма Сатегория</label>
            <div class="col-sm-10">
                <select class="form-select" name="normid" aria-label="Default select example">
                    <option selected value="0">{{ '-Norma-' }}</option>
                    @foreach($norms as $row)
                    @if($product->norm_cat_id == $row->id)
                        <option selected value="{{$row['id']}}">{{$row['norm_name']}}</option>    
                    @else
                        <option value="{{$row['id']}}">{{$row['norm_name']}}</option>
                    @endif

                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Ish faoliyati</label>
            <div class="col-sm-10">
                <input type="number" required name="hide" class="form-control" value="{{ $product->hide }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </div>
    </form>
    <a href="/technolog/allproducts">Orqaga</a>
</div>
@endsection


@section('script')


@endsection