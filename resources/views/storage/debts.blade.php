@extends('layouts.app')

@section('css')
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<div class="py-4 px-4">
    <form method="POST" action="{{route('storage.createpay')}}">
        @csrf
        <div class="form-group row">
            <div class="col-md-3">
                <select class="form-select" name="catid" aria-label="Default select example">
                    @foreach($shops as $row)
                    <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="dayid" aria-label="Default select example">
                    @foreach($days as $row)
                    <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="value" class="form-control" placeholder="so'm" required>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-success">ok</button>
            </div>
        </div>

    </form>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Firma</th>
                <th scope="col">To'landi</th>
                <th scope="col">Maxsulot</th>
                <th scope="col">Kg</th>
                <th scope="col">Narxi (so'm)</th>
                <th scope="col">Jami (so'm)</th>
                <th scope="col">Sana</th>
                <th scope="col">...</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($debts as $row)
                <tr>
                    <td>{{ $row->debtid }}</td>
                    <td><a href="/storage/shopdebts?ShopId={{ $row->shop_id }}">{{ $row->shop_name }}</a></td>
                    <td>{{ $row->pay }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->weight }}</td>
                    <td>{{ $row->cost }}</td>
                    <td>{{ $row->loan }}</td>
                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
                    <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="" data-delet-id="" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#exampleModalss"></i></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $debts->links() }}
    <br>
    <a href="/storage/home/0/0">Orqaga</a>
</div>
@endsection

@section('script')

@endsection