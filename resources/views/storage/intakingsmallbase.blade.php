@extends('layouts.app')

@section('leftmenu')
@include('storage.sidemenu'); 
@endsection

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('storage.deletetakingsmallbase')}}" method="POST">
            @csrf
            <input type="hidden" name="grodid" value="{{$id}}">
            <input type="hidden" name="kind_id" value="{{$kind->id}}">
            <input type="hidden" name="day" value="{{$day}}">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="deletename"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn dele btn-danger">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- DELET -->
<div class="row py-1 px-4">
    <h3>{{ $kind->kingar_name }}</h3>
    <div class="col-md-12" >
    <form action="{{route('storage.addintakingsmallbase')}}" method="POST">
        @csrf
        <input type="hidden" name="groid" value="{{$id}}">
        <input type="hidden" name="kid" value="{{$kind->id}}">
        <input type="hidden" name="day" value="{{$day}}">
        <div class="row">
            <div class="col-md-3">
                <div class="product-select">
                    <select class="form-select" name="productid" required aria-label="Default select example">
                        <option value="">--Mahsulotlar--</option>
                        @foreach($products as $all)
                            <option value="{{$all['id']}}">{{$all['product_name']}}</option>
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
            <div class="col-md-2">
                <div class="sub" style="display: flex;justify-content: end;">
                    <button class="btn btn-dark">Qo'shish</button>
                </div>
            </div>
        </div>
    </form>
    </div>
    <div class="col-md-12">
        <div class="table" style="margin-top: 20px">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col">Birlik</th>
                        <th scope="col">Og'irligi</th>
                        <th scope="col">Narxi</th>
                        <th scope="col" style="text-align: end;">O'chirish</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; $products = []?>
                    @foreach($res as $row)
                    
                    	@if(!isset($products[$row->product_id]))
                    		<?php
                    		    $products[$row->product_id]['tid'] = $row->tid;
                    			$products[$row->product_id]['product_name'] = $row->product_name;
                    			$products[$row->product_id]['weight'] = $row->weight;
                    			$products[$row->product_id]['size_name'] = $row->size_name;
                    			$products[$row->product_id]['cost'] = $row->cost;
                    		?>
                    	@else
                    		<?php 
                    			$products[$row->product_id]['weight'] += $row->weight;
                    		?>
                    	@endif
                    	
                    @endforeach
                    @foreach($products as $row)
                    <tr>
                        <th scope="row">{{ $i++ }}</th>
                        <td>{{ $row['product_name'] }}</td>
                        <td>{{ $row['size_name'] }}</td>
                        <td>{{ $row['weight'] }}</td>
                        <td>{{ $row['cost'] }}</td>
                        <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $row['product_name'] }}" data-delete-id="{{ $row['tid'] }}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/storage/takinglargebase">Orqaga</a>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.detete').click(function() {
            var rowid = $(this).attr('data-delete-id');
            var pro_name = $(this).attr('data-name-id');
            var div = $('.deletename');
            div.html("<h3><b>"+pro_name+"</b> maxsulotini o'chirish.</h3><input type='hidden' name='rowid' value="+rowid+" >");
        });


    });
</script>
@endsection