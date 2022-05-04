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
        <form action="{{route('technolog.editproductfood')}}" method="POST">
            @csrf
            <input type="hidden" name="titleid" value="{{$id}}">
            <div id="hiddenid">
            </div>
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                    <select class="form-select" name="productid" required aria-label="Default select example">
                        <option value="">--Mahsulotlar--</option>
                        @foreach($productall as $all)
                        @if(!isset($all['ok']))
                        <option value="{{$all['id']}}">{{$all['product_name']}}</option>
                        @endif
                        @endforeach
                    </select>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-warning">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->

<div class="box-products py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="text-title">
                <h3 class="documentnumber">{{ (empty($food[0]->food_name)) ? "" :  $food[0]->food_name }}</h3>
            </div>
        </div>
    </div>

    <form action="{{route('technolog.addproductfood')}}" method="POST">
        @csrf
        <input type="hidden" name="titleid" value="{{$id}}">
        <div class="row">
            <div class="col-md-6">
                <div class="product-select">
                    <select class="form-select" name="productid" required aria-label="Default select example">
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
                        <th scope="col" style="text-align: end;">Tahrirlash</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($food as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->product_name }}</td>
                        <td style="text-align: end;"><i data-edites-id="{{ $item->id }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i><i class="detete  fa fa-trash" aria-hidden="true" data-delet-id="{{$item->id}}" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#exampleModalss"></i></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/technolog/food">Orqaga</a>
    </div>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            var div = $('#hiddenid');
            div.html("<input type='hidden' name='id' value="+g+">");
        });

        $('.detete').click(function() {
            deletes = $(this).attr('data-delet-id');
        });

        $('.dele').click(function() {
            var del = deletes
            $.ajax({
                method: "GET",
                url: '/technolog/deleteproductfood',
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