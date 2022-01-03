@extends('layouts.app')

@section('content')

<div class="py-4 px-4">
    <form action="{{route('technolog.ordername')}}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="add-sklad">
                    <select class="form-select" name="mtmname" required>
                        <option value="">MTM-nomi</option>
                        @foreach($gardens as $rows)
                        <option value="{{$rows['id']}}">{{$rows['kingar_name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Izoh</span>
                    <input name="title" required style="padding: 8px 6px !important;" type="text" class="form-control">
                </div>
            </div>

            <div class="col-md-2">
                <div class="input-group mb-3">
                    <button class="btn btn-success" style="width: 100%;">Yaratish</button>
                </div>
            </div>
        </div>
    </form>

    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">MTM</th>
                <th scope="col">Sarlavha</th>
                <th scope="col">Mahsulotlar</th>
                <th style="width: 70px;">Holati</th>
                <th style="width: 70px;">...</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{$order['kingar_name']}}</td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <a href="/technolog/orderitem/{{$order['id']}}">{{$order['order_title']}}</a></td>
                    @else
                    {{$order['order_title']}}
                    @endif
                <td>
                    @foreach($products as $item)
                    @if($item->order_product_name_id == $order->id)
                        {{ $item->product_name."-".$item->product_weight.", " }}
                    @endif
                    @endforeach
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <span style="color: green">Yaratildi</span>
                    @else
                        Yuborildi
                    @endif
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <a href="/technolog/"><i class="far fa-paper-plane"></i></a></td>
                    @elseif($order['document_processes_id'] == 2)
                    <i class="fas fa-check" style="color: #1a61aa;"></i>                       
                    @elseif($order['document_processes_id'] == 3)
                    <i class="fas fa-check-double"></i>
                    @elseif($order['document_processes_id'] == 4)

                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection