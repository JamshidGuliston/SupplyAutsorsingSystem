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
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $debts->links() }}
    <br>
    <a href="/storage/debts">Orqaga</a>
</div>
@endsection

@section('script')

@endsection