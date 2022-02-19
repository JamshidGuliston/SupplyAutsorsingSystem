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
<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-project-diagram me-2"></i>Projects</a>
    <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i class="fas fa-chart-line me-2"></i>Analytics</a>
    <a href="/technolog/seasons" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/seasons') ? 'active' : null }}"><i class="fas fa-paste"></i> Menyular</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/allproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allproducts') ? 'active' : null }}"><i class="fas fa-carrot"></i> Products</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
@endsection

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
        @foreach($days as $day)
        @if($day->active == 1)
            <?php $act = $day->id; ?>
            <a href="/technolog/addshopproduct/{{$day->id}}" class="day__item active">{{ $day->day_number }}</a>
        @else
            <a href="/technolog/addshopproduct/{{$day->id}}" class="day__item">{{ $day->day_number }}</a>
        @endif
        @endforeach
    </div>
</div>
<div class="py-4 px-4">
    <h5>Складга</h5>
    <form action="{{route('technolog.productshoptogarden')}}" method="post">
        @csrf
        <input type="hidden" name="dayid" value="{{ $act }}">
        <div class="row">
            <div class="col-md-3">
                <div class="add-sklad">
                    <select class="form-select" name="shopname" required>
                        <option value="">Етказувчи</option>
                        @foreach($shops as $rows)
                        <!-- @if(!isset($rows['ok'])) -->
                        <option value="{{$rows['id']}}">{{$rows['shop_name']}}</option>
                        <!-- @endif -->
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="add-sklad">
                    <select class="form-select" name="mtmname" required>
                        <option value="">MTM-nomi</option>
                        @foreach($gardens as $rows)
                        <!-- @if(!isset($rows['ok'])) -->
                        <option value="{{$rows['id']}}">{{$rows['kingar_name']}}</option>
                        <!-- @endif -->
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="add-sklad">
                    <select class="form-select" name="productid" required>
                        <option value="">Маҳсулот</option>
                        @foreach($allproducts as $rows)
                        <!-- @if(!isset($rows['ok'])) -->
                        <option value="{{$rows['id']}}">{{$rows['product_name']}}</option>
                        <!-- @endif -->
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="add-sklad">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" id="inputGroup-sizing-sm">KG</span>
                        <input name="weight" required style="padding: 8px 6px !important;" type="text" class="form-control">
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="input-group mb-3">
                    <button class="btn btn-success" style="width: 100%;">Qo'shish</button>
                </div>
            </div>
        </div>
    </form>

    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">shop</th>
                <th scope="col">MTM</th>
                <th scope="col">Mahsulotlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{$order['shop_name']}}</td>
                <td>
                    {{$order['kingar_name']}}
                </td>
                <td>
                    {{$order['product_name']}} - {{$order['product_weight']}}
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

        $('.fa-paper-plane').click(function() {
            id = $(this).attr('data-produc-id');
            // console.log(id)
        })
        $('#sendpass').click(function() {
            var g = id;
            var pass = $('#passw').val();
            var h = $('#succ');
            $.ajax({
                method: "GET",
                url: '/technolog/controlpassword',
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