@extends('layouts.app')

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="exampleModals" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-danger">Ok</button>
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
            <div class="modal-body">
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
                    <input type="number" name="sizeproduct" required class="form-control" placeholder="raqam kiriting" aria-label="Recipient's username" aria-describedby="basic-addon2">
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
                        <td style="text-align: end;"><i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i><i class="fa fa-trash" aria-hidden="true"></i></td>
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
    // $('#select-add').change(function() {
    // g = $(this).val();
    // h = $('.yang-ages');
    // $.ajax({
    //     method: "GET",
    //     url: '/technolog/ageranges/' + g,
    //     success: function(data) {
    //         h.html(data);
    //     }
    // })
    // });

    // $('.add-age').click(function() {
    // var inp = $('.form-control');
    // var k = inp.attr('data-id');
    // inp.each(function() {
    //     var j = $(this).attr('data-id');
    //     console.log(j);
    //     var valuess = $(this).val();
    //     console.log(valuess);
    //     console.log(g)
    //     $.ajax({
    //         method: 'GET',
    //         url: '/technolog/addage/' + g + '/' + j + '/' + valuess,
    //         success: function(data) {
    //             location.reload();
    //         }
    //     })
    // })
    // })

    // var edite = $('.edites');
    // edite.click(function() {
    // var ll = $(this).attr('data-kinid');
    // $.ajax({
    //     method: 'GET',
    //     url: '/technolog/getage/' + ll,
    //     success: function(data) {
    //         var modaledite = $('.editesmodal .modal-body');
    //         modaledite.html(data);
    //     },
    // })
    // })

    // var editSub = $('.editsub');
    // editSub.click(function() {
    // var inp = $('.form-control');
    // var k = inp.attr('data-id');
    // var b = $('.kingarediteid').val();
    // inp.each(function() {
    //     var j = $(this).attr('data-id');
    //     if ($(this).val() == "" || $(this).val() == 0) {
    //         alert('Maydonlarni to`ldiring');
    //     } else {
    //         var valuess = $(this).val();
    //         $.ajax({
    //             method: 'GET',
    //             url: '/technolog/editage/' + b + '/' + j + '/' + valuess,
    //             success: function(data) {
    //                 location.reload();
    //             }
    //         })
    //     };


    // })
    // })


    });
</script>
@endsection