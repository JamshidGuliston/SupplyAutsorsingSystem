@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
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


<!-- aDD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot buyurtmasi</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('technolog.plusproduct')}}" method="POST">
                @csrf
                <input type="hidden" name="titleid" value="{{$orderid}}">
                <div class="modal-body">
                    <table class="table table-light table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Og'irligi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $all->product_name }}</td>
                                <td><input type="text" onkeypress="javascript:return isNumber(event)" name="orders[{{ $all->id }}]"></td>
                            </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn add-age btn-info text-white">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End -->

<div class="box-products py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="text-title">
                <h3 class="documentnumber">{{ $orderid." - ҳужжат, " }} {{ $ordername->kingar_name.", " }} {{ $ordername->order_title }}</h3>
            </div>
        </div>
    </div>    
        <input type="hidden" name="titleid" value="{{$orderid}}">
        <div class="row">
            <div class="col-md-2">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon2">Yuborilmagan</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sub" style="display: flex;justify-content: end;">
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#Modalsadd">Tez Qo'shish</button>
                </div>
            </div>
        </div>
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
    function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
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
