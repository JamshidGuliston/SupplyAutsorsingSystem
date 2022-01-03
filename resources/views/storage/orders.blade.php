@extends('layouts.app')

@section('content')
<!-- edite -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content loaders">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Tasdiqlash</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h3>Tanlangan hujjatni qabul qilasizmi</h3>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                    <button type="button" class="btn ok btn-info text-white">Ha</button>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- edite  -->

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
                    <a href="/technolog/orderitem/{{$order['id']}}">{{$order['order_title']}}</a>
                </td>
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
                    <span style="color: #000;text-align: center;background-color: #ffd12a;padding: 3px 10px;display: inline-block;font-size: 14px;">Qabul qilish</span>
                    @elseif($order['document_processes_id'] == 3)
                    <span style="color: green;background-color: cornflowerblue;color: #fff;padding: 4px 9px;">Yuborish</span>
                    @elseif($order['document_processes_id'] == 4)
                    <span style="color: white;background-color: green;padding: 3px 9px;border-radius: 3px;">Yuborildi</span>
                    @endif
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <i class="far fa-paper-plane" data-produc-id="{{$order['id']}}" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="cursor: pointer; margin-left: 16px; color: deepskyblue"></i>
                    @elseif($order['document_processes_id'] == 2)
                    <i class="fas fa-envelope" data-bs-toggle="modal" data-doc-id="{{$order['id']}}" data-bs-target="#exampleModalsadd" style="color: #1a61aa; cursor: pointer;"></i>
                    @elseif($order['document_processes_id'] == 3)
                    <i class="fa fa-paper-plane" data-id="{{$order['id']}}" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="color: #6cbaff; cursor: pointer;"></i>
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
        $('.fa-envelope').click(function() {
            doc = $(this).attr('data-doc-id');
            console.log(doc)
        });

        $('.fa-paper-plane').click(function() {
            id = $(this).attr('data-id');

        });

        $('.ok').click(function() {
            var g = doc;
            $.ajax({
                method: "GET",
                url: '/storage/getdoc',
                data: {
                    'getid': g,
                },
                success: function(data) {
                    location.reload();
                }
            })
        });

        $('#sendpass').click(function() {
            var g = id;
            var pass = $('#passw').val();
            var h = $('#succ');
            $.ajax({
                method: "GET",
                url: '/storage/controlpassword',
                data: {
                    'password': pass,
                    'orderid': g,
                },
                success: function(data) {
                    if (data == 1) {
                        h.html("<i class='fas fa-check' style='color: seagreen;'></i>");
                        location.reload();
                    } else {
                        h.html("<i class='fas fa-exclamation-triangle' style='color: red;'></i>");
                    }
                }
            })
        });
    })
</script>
@endsection