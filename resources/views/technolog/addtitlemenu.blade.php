@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('css')
<style>
    /* form {
        width: 85%;
        margin-top: 30px;
    } */
    .row{
        margin-bottom: 10px;
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
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-paperclip me-2"></i>Reports</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/allproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allproducts') ? 'active' : null }}"><i class="fas fa-carrot"></i> Products</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a>
</div>
@endsection

@section('content')
<div class="py-5 px-5">
    <form method="POST" action="{{route('technolog.createmenu')}}">
        @csrf
        <input type="hidden" name="seasonid" value="{{ $id }}" />
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Меню номи: </label>
            <div class="col-sm-6">
                <input type="text" name="name" class="form-control" id="staticEmail" required>
            </div>
        </div>
        <div class="form-group row">

            <label for="inputPassword" class="col-sm-2 col-form-label">Bolalar guruhi</label>
            @foreach($ages as $rows)
            <?php $i = 1; ?>
            <div class="col-sm-2">
                <input class="form-check-input" name="yongchek[]" type="checkbox" id="inlineCheckbox1" value="{{$rows['id']}}">
                <label class="form-check-label" for="inlineCheckbox1">{{$rows['age_name']}}</label>
            </div>
            @endforeach
        </div>
        <!-- <div class="row">
            <div class="col-md-6">
                <div class="product-select">
                    <select class="form-select" name="productid" required aria-label="Default select example">
                        <option value="">--Овқатланиш вақти--</option>
                        
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sub" style="display: flex;justify-content: end;">
                    <button class="btn btn-dark">Qo'shish</button>
                </div>
            </div>
        </div>
        <div class="table">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col" style="text-align: end;">Tahrirlash</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
    </div> -->
    
    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"></label>
        <div class="col-sm-6">
        <button type="submit" class="btn btn-success">Saqlash</button>
        </div>
    </div>
    </form>
    <a href="/technolog/menus/{{$id}}">Orqaga</a> 
</div>
@endsection


@section('script')


@endsection