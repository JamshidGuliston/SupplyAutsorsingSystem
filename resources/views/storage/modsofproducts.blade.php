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
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.3);
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
        margin: 0 auto;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>
@endsection

@section('leftmenu')
@include('storage.sidemenu'); 
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
        <div class="col-sm-4">
            <div class="btn-group w-100" role="group">
                <button type="submit" id="load" class="btn btn-info">
                    <i class="fas fa-sync" aria-hidden="true"></i> Hisobot
                </button>
                <button type="submit" id="pdf" class="btn btn-danger">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> PDF
                </button>
            </div>
        </div>
    </div>
    <hr>
    <div class="loader-box">
        <div class="loader"></div>
        <p style="margin-top: 20px; color: #3498db; font-size: 16px;">Hisobot tayyorlanmoqda...</p>
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
	                $('.loader-box').show();
	            },
                method: "GET",
                data:{
                    'lastid' : regionid
                },
                url: '/storage/getreportlargebase',
                success: function(data) {
                    div.html(data);
                    $('.loader-box').hide();
                },
                error: function() {
                    $('.loader-box').hide();
                    alert('Xatolik yuz berdi!');
                }
            })
        }
    });
    
    $('#pdf').click(function(){
        var regionid = $("#date option:selected").val();
        if (regionid == 0){
            alert("Vaqtni belgilang.");
        }else{
            // PDF yuklab olish uchun yangi oynada ochish
            var url = '/storage/getreportlargebasePDF?lastid=' + regionid;
            $('.loader-box').show();
            window.open(url, '_blank');
            setTimeout(function() {
                $('.loader-box').hide();
            }, 3000);
        }
    });
</script>
@endsection