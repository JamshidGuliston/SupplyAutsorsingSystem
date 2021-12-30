@extends('layouts.app')

@section('css')
<style>
    form{
        width: 85%;
        margin-top: 30px;
    }
    .form-group{
        margin-bottom: 20px;
    }
    .form-group .btn{
        width: 100%;
        background-color: #2f8d2f;
    }
</style>
@endsection

@section('content')
<div class="py-5 px-5">
    <h2>{{ $garden->kingar_name }}</h2>
    <form>
        @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nomi</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="staticEmail" value="{{ $garden->kingar_name }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Parol</label>
            <div class="col-sm-10">
            <input type="text" class="form-control" id="staticEmail" value="{{ $garden->kingar_password }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Xodimlar soni</label>
            <div class="col-sm-10">
            <input type="number" class="form-control" value="{{ $garden->worker_count }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Boloar guruhi</label>
            <div class="col-sm-3">
                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                <label class="form-check-label" for="inlineCheckbox1">3-4 yosh</label>
            </div>
            <div class="col-sm-3">
                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                <label class="form-check-label" for="inlineCheckbox1">4-7 yosh</label>
            </div>
            <div class="col-sm-3">
                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                <label class="form-check-label" for="inlineCheckbox1">qisqa guruh</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Ish faoliyati</label>
            <div class="col-sm-10">
            <input type="number" class="form-control" value="{{ $garden->hide}}">
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