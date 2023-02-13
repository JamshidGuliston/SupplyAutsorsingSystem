@extends('layouts.app')

@section('leftmenu')
@include('storage.sidemenu'); 
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
                <div class="deletename"></div>
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
                        @foreach($products as $all)
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
                <h3 class="documentnumber">{{ $group->day_number.".".$group->month_name.".".$group->year_name; }}</h3>
            </div>
        </div>
    </div>

    <!-- <form action="{{route('storage.addproduct')}}" method="POST">
        @csrf
        <input type="hidden" name="titleid" value="{{$id}}">
        <div class="row">
            <div class="col-md-4">
                <div class="product-select">
                    <select class="form-select" name="productid" required aria-label="Default select example">
                        <option value="">--Mahsulotlar--</option>
                        @foreach($products as $all)
                        @if(!isset($all['ok']))
                        <option value="{{$all['id']}}">{{$all['product_name']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="weight" placeholder="Og'irlik kg" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="cost" placeholder="Narx" required>
            </div>
            <div class="col-md-4">
                <div class="sub" style="display: flex;justify-content: end;">
                    <button class="btn btn-dark">Qo'shish</button>
                </div>
            </div>
        </div>
    </form>
</div> -->

<div class="row py-1 px-4">
    <div class="col-md-12">
        <div class="table">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col">Birlik</th>
                        <th scope="col">Og'irligi</th>
                        <th scope="col">Narxi</th>
                        <!-- <th scope="col" style="text-align: end;">O'chirish</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($productall as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->size_name }}</td>
                        <td>{{ $item->weight }}</td>
                        <td>{{ $item->cost }}</td>
                        <!-- <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $item->product_name }}" data-delet-id="{{$item->id}}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#exampleModalss"></i></td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/storage/addedproducts/0/{{ $group->month_id }}">Orqaga</a>
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
            pro_name = $(this).attr('data-name-id');
            var div = $('.deletename');
            div.html("<p>"+pro_name+" maxsulotini o'chirish.</p>");
            
        });

        $('.dele').click(function() {
            var del = deletes;
            $.ajax({
                method: "GET",
                url: '/storage/deleteproduct',
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