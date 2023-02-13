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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Pul berish</button>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Firma</th>
                <th scope="col">To'langan</th>
                <th scope="col">Qarzdorlik</th>
                <th scope="col">Firma qarzi</th>
                <th style="width: 40px;"><a href="#">Maxsulot</a></th>
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
                    <td>{{ $row->pay }} so'm</td>
                    <td>{{ $row->loan }} so'm</td>
                    <td>{{ $row->hisloan }} so'm</td>
                    <td><a href="#">Product</a></td>
                    <td>{{ $row->date }}</td>
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