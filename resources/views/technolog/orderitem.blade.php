@extends('layouts.app')

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
<div class="modal fade" id="exampleModalss" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bu mahsulotni o'chirasizmi
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn dele btn-danger">O'chirish</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->

<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn editsub btn-warning">Saqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->


<!-- EDD -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="yang-ages">
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn add-age btn-info text-white">Qo'shish</button>
            </div>
        </div>
    </div>
</div>

<!-- EDD -->

<div class="box-products py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="text-title">
                <h3 class="documentnumber">{{ $orderid." - ҳужжат, " }} {{ $ordername->kingar_name.", " }} {{ $ordername->order_title }}</h3>
            </div>
        </div>
    </div>

    <form action="{{route('technolog.plusproduct')}}" method="POST">
        @csrf
        <input type="hidden" name="titleid" value="{{$orderid}}">
        <div class="row">
            <div class="col-md-6">
                <div class="product-select">
                    <select class="form-select" name="productsid" required aria-label="Default select example">
                        <option value="">--Mahsulotlar--</option>
                        @foreach($productall as $all)
                        @if(!isset($all['ok']))
                        <option value="{{$all['id']}}">{{$all['product_name']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group mb-3">
                    <input type="text" id="check_char" name="sizeproduct" required class="form-control" placeholder="raqam kiriting" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <span class="input-group-text" id="basic-addon2">KG</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="sub" style="display: flex;justify-content: end;">
                    <button class="btn btn-dark">Qo'shish</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="row py-1 px-4">
    <div class="col-md-12">
        <div class="table">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col">Og'irligi</th>
                        <th scope="col" style="text-align: end;">Tahrirlash</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($items as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->product_weight }}</td>
                        <td style="text-align: end;"><i data-edites-id="{{ $item->id }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i><i class="detete  fa fa-trash" aria-hidden="true" data-delet-id="{{$item->id}}" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#exampleModalss"></i></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/technolog/addproduct">Orqaga</a>
    </div>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#check_char').keypress(function(event){
            var str = $('#check_char').val(); 

            if(String.fromCharCode(event.which) == ','){
                event.preventDefault();
                $('#check_char').val(str + '.'); 
            }
        });
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            $.ajax({
                method: 'GET',
                url: '/technolog/getproduct',
                data: {
                    'id': g
                },
                success: function(data) {
                    var $editesproduct = $('.editesproduct');
                    varproducts = $editesproduct.html(data);
                }

            })
        });

        $('.editsub').click(function() {
            var orderinpval = $('.product_order').val();
            var orderinpid = $('.product_order').attr('data-producy');
            $.ajax({
                method: 'GET',
                url: '/technolog/editproduct',
                data: {
                    'producid': orderinpid,
                    'orderinpval': orderinpval
                },
                success: function(data) {
                    location.reload();
                }
            })
        });

        $('.detete').click(function() {
            deletes = $(this).attr('data-delet-id');
        });

        $('.dele').click(function() {
            var del = deletes
            $.ajax({
                method: "GET",
                url: '/technolog/deleteid',
                data: {
                    'id': del,
                },
                success: function(data) {
                    location.reload();
                }

            })
        })

    });
</script>
@endsection