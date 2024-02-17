@extends('layouts.app')

@section('css')
<style>
    form {
        width: 85%;
        margin-top: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group .btn {
        width: 100%;
        background-color: #2f8d2f;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')

 <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><b class="kindname"></b> O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method='post' action='/technolog/editactivemanu'>
                @csrf
                <div class="modal-body" style="text-align: center;">
                    <div class="divproducts">
                    </div>
                    <button type="submit"  class="btn btn-success" >Saqlash</button>
                </div>
            </form>
            <div class="modal-footer" >

            </div>
        </div>
    </div>
</div>

<div class="py-4 px-4">
        <div class="form-group row">
            <div class="col-md-2">
                <select class="form-select" id="beginday" aria-label="Default select example">
                    <option value="0">-Sanadan-</option>
                    @foreach($days as $row)
                    <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="endday" aria-label="Default select example">
                    <option value="0">-Sanagacha-</option>
                    @foreach($days as $row)
                    <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" id="showreport" data-bs-toggle="modal" data-bs-target="#exampleModal" class="show btn btn-success">show</button>
            </div>
        </div>
    <hr>
    <h3><b class="sname"></b></h3>
    <div class="repottable"></div>
    <br>
    <a href="/technolog/seasons">Orqaga</a>
</div>

@endsection


@section('script')
<script>
	$('.show').click(function() {
        var baginid = $("#beginday option:selected").val();
        var endid = $("#endday option:selected").val();
        var div = $('.divproducts');
        $.ajax({
            method: "GET",
            data: {
                'bid': baginid,
                'eid': endid
            },
            url: '/technolog/getactivemenuproducts',
            success: function(data){
                div.html(data);
            }
			
        })
    });
</script>
@endsection





