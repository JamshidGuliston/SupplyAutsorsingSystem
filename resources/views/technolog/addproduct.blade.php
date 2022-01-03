@extends('layouts.app')

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="Modaldelete" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Shaxsingizni tasdiqlang</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="password" class='form-control' id="passw" name="password" placeholder="password" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="succ"></button>
                <button type="button" id="sendpass" class="btn bg-success" style="color: white">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->

<div class="py-4 px-4">
    @if(isset($orders[0]->day_number))
    <h4>Oyning {{ $orders[0]->day_number."-sanasi" }}</h4>
    @endif      
    <form action="{{route('technolog.ordername')}}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="add-sklad">
                    <select class="form-select" name="mtmname" required>
                        <option value="">MTM-nomi</option>
                        @foreach($gardens as $rows)
                        @if(!isset($rows['ok']))
                        <option value="{{$rows['id']}}">{{$rows['kingar_name']}}</option>
                        @endif
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
                    <span>Yaratildi</span>
                    @elseif($order['document_processes_id'] == 2)
                    <span style="color: green">Yuborildi</span>                       
                    @elseif($order['document_processes_id'] == 3)
                    <span style="color: green">Qabul qilindi</span> 
                    @elseif($order['document_processes_id'] == 4)
                    <span style="color: white; background-color: green">Yuborildi</span> 
                    @endif
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <i class="far fa-paper-plane" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="cursor: pointer; margin-left: 16px; color: deepskyblue"></i>
                    @elseif($order['document_processes_id'] == 2)
                    <i class="fas fa-check" style="color: #1a61aa;"></i>                       
                    @elseif($order['document_processes_id'] == 3)
                    <i class="fas fa-check-double"></i>
                    @elseif($order['document_processes_id'] == 4)
                    <i class="fas fa-clipboard-check"></i>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="/technolog/home">Orqaga</a>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#sendpass').click(function() {
        var pass = $('#passw').val(); 
        var h = $('#succ');
        $.ajax({
            method: "GET",
            url: '/technolog/controlpassword',
            data:{
                'password': pass
            },
            success: function(data) {
                if(data==1){
                    h.html("<i class='fas fa-check' style='color: seagreen;'></i>");
                    location.reload();
                }
                else{
                    h.html("<i class='fas fa-exclamation-triangle' style='color: red;'></i>");
                }
            }
        })
    });
})
</script>
@endsection