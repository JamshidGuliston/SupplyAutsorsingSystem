@extends('layouts.app')

@section('css')
<style>
.year {
    text-align: center;
}
.month,
.day {
    margin: 10px 20px;
    display: flex;
    justify-content: left;
}

.month__item{
    width: calc(100% / 12);
    text-align: center;
    border-bottom: 1px solid #000;
}

.month__item + .month__item {
    /* border-left: 1px solid #000; */
}
.day__item{
    background-color: #ecf6f1;
    text-align: center;
    vertical-align: middle;
    min-width: 34px;
    padding: 5px;
    margin-left: 5px;
    border-radius: 50%;
}

.month__item, .day__item{
    color: black;
    cursor: context-menu;
    /* border: 1px solid #87706a; */
    text-decoration: none;
}
.active{
    background-color: #23b242;
    color: #fff;
}
.month__item:hover,
.day__item:hover{
    background-color: #23b242;
    color: #fff;
    transition: all .5s;
    cursor: pointer;
}
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<div class="modal fade" id="Modalback" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maxsulotlarni qaytarib olish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="password" class='form-control' id="bpassword" name="password" placeholder="password" required>
            </div>
            <div class="modal-footer">
                <button type="button" id="backpass" class="btn bg-success" style="color: white">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
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
                <button type="button" id="sendpass" class="btn bg-success" style="color: white">Tasdiqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->
<div class="date">
    <!-- <div class="year">2020</div> -->
    <div class="month">
        @foreach($months as $month)
        @if($month->month_active == 1)
            <a href="#" class="month__item active">{{ $month->month_name }}</a>
        @else
            <a href="#" class="month__item">{{ $month->month_name }}</a>
        @endif
        @endforeach
    </div>
    <div class="day">
        <a href="#" class="day__item">1</a>
        <a href="#" class="day__item">2</a>
        <a href="#" class="day__item">3</a>
        <a href="#" class="day__item">4</a>
        <a href="#" class="day__item">5</a>
        <a href="#" class="day__item">6</a>
        <a href="#" class="day__item">7</a>
        <a href="#" class="day__item">8</a>
        <a href="#" class="day__item">9</a>
        <a href="#" class="day__item">10</a>
        <a href="#" class="day__item">11</a>
        <a href="#" class="day__item">12</a>
        <a href="#" class="day__item">13</a>
        <a href="#" class="day__item">14</a>
        <a href="#" class="day__item">15</a>
        <a href="#" class="day__item">16</a>
        <a href="#" class="day__item">17</a>
        <a href="#" class="day__item">18</a>
        <a href="#" class="day__item">19</a>
        <a href="#" class="day__item">20</a>
        <a href="#" class="day__item">21</a>
        <a href="#" class="day__item">22</a>
        <a href="#" class="day__item">23</a>
        <a href="#" class="day__item">24</a>
        <a href="#" class="day__item">25</a>
    </div>
</div>
<div class="py-4 px-4">
    <!-- @if(isset($orders[0]->day_number))
    <h4>Oyning {{ $orders[0]->day_number."-sanasi" }}</h4>
    @endif -->

    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">MTM</th>
                <th scope="col">Sarlavha</th>
                <th scope="col">Mahsulotlar</th>
                <th style="width: 40px;">PDF</th>
                <th style="width: 70px;">Holati</th>
                <th style="width: 70px;">...</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{$order['kingar_name']}}</td>
                <td>
                    @if($order['document_processes_id'] == 3)
                        <a href="/storage/orderitem/{{$order['id']}}">{{$order['order_title']}}</a>
                    @else
                    {{$order['order_title']}}
                    @endif
                </td>
                <td>
                    @foreach($products as $item)
                    @if($item->order_product_name_id == $order->id)
                    {{ $item->product_name."-".$item->product_weight.", " }}
                    @endif
                    @endforeach
                </td>
                <td>
                    <a href="/storage/orderskladpdf/{{ $order->id }}" target="__blank">pdf</a>
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <span>Yaratildi</span>
                    @elseif($order['document_processes_id'] == 2)
                    <span style="color: green">Yuborildi</span>
                    @elseif($order['document_processes_id'] == 3)
                    <span style="color: green;text-align: center;background-color: #ffd12a;padding: 3px 10px;display: inline-block;font-size: 14px;">Qabul qilindi</span>
                    @elseif($order['document_processes_id'] == 4)
                    <span style="color: white;background-color: blue;padding: 3px 9px;border-radius: 3px;">Yuborildi</span>
                    @elseif($order['document_processes_id'] == 5)
                    <span style="color: white;background-color: green;padding: 3px 9px;border-radius: 3px;">Tasdiqlandi</span>
                    @endif
                </td>
                <td>
                    @if($order['document_processes_id'] == 1)
                    <i class="far fa-paper-plane" data-produc-id="{{$order['id']}}" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="cursor: pointer; margin-left: 16px; color: deepskyblue"></i>
                    @elseif($order['document_processes_id'] == 2)
                    <i class="fas fa-check" style="color: #1a61aa;"></i>
                    @elseif($order['document_processes_id'] == 3)
                        <i class="far fa-paper-plane" data-produc-id="{{$order['id']}}" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="cursor: pointer; margin-left: 16px; color: deepskyblue"></i>
                    @elseif($order['document_processes_id'] == 4)
                        <i class="fas fa-undo" data-produc-id="{{$order['id']}}" data-bs-toggle="modal" data-bs-target="#Modaldelete" style="cursor: pointer; margin-left: 16px; color: deepskyblue"></i>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="/storage/addmultisklad">Orqaga</a>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {

        $('.fa-paper-plane').click(function() {
            id = $(this).attr('data-produc-id');
            // console.log(id)
        })
        $('#sendpass').click(function() {
            var g = id;
            var pass = $('#passw').val();
            $.ajax({
                method: "GET",
                url: '/storage/dostcontrolpassword',
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

        $('#backpass').click(function() {
            var g = $(this).attr('fa-undo');
            var pass = $('#bpassword').val();
            $.ajax({
                method: "GET",
                url: '/storage/backcontrolpassword',
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