@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<script>
    function today(){
        console.log('ok');
    }
    function tommorow(){
        console.log('ok');
    }
</script>
<style>
    th, td{
        text-align: center;
        vertical-align: middle;
        border: solid 1px #b1b1b14f;
    }
    .loader-box {
    	text-align: center;
    	font-size: 54px;
        display: none;
        position: absolute;
    }
    .loader {
        border: 9px solid #f3f3f3;
        border-radius: 50%;
        border-top: 9px solid #3498db;
        width: 60px;
        display: block;
        height: 60px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: absolute;
        left: 353px;
        top: 153px;
    }

</style>
@endsection

@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- <p>2023-yil</p> -->
    <div class="row">
        <div class="col-sm-4">
            <select class="form-select" id="date" name="end" aria-label="Default select example" required>
                <option value="0">-Sanagacha-</option>
                @foreach($days as $row)
                    <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
        <button type="submit" id="load" class="btn btn-info form-control">load <i class="fas fa-sync" aria-hidden="true"></i></button>
        </div>
    </div>
    <hr>
    <div class="loader-box">
        <i class="fas fa-spinner fa-pulse"></i>
    </div>
    <div class="report">
    	
    </div>
</div>
@endsection
@section('script')
<script>
    $('#load').click(function(){
        var regionid = $("#date option:selected").val();
        var div = $('.report');
        if (regionid == 0){
            alert("Vaqtni belgilang.");
        }else{
            $.ajax({
            	beforeSend: function() {
	                // $('.loader-box').show();
	            },
                method: "GET",
                data:{
                    'lastid' : regionid
                },
                url: '/accountant/getreportlargebase',
                success: function(data) {
                    div.html(data);
                    $('.loader-box').hide();
                }
            })
        }
    });
</script>
@endsection