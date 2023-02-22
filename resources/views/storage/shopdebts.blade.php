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
    <h3><a href="#">{{ $debts[0]->shop_name }}</a></h3>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Firma</th>
                <th scope="col">To'landi</th>
                <th scope="col">Maxsulot</th>
                <th scope="col">Kg</th>
                <th scope="col">Narxi</th>
                <th scope="col">Jami</th>
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
                    <td>{{ $row->cost }} so'm</td>
                    <td>{{ $row->loan }} so'm</td>
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