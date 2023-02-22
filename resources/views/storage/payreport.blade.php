@extends('layouts.app')

@section('css')
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
td, th{
    border: 1px solid #b4c6d8;
}
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<div class="py-4 px-4">
        <div class="form-group row">
            <div class="col-md-3">
                <select class="form-select" id="shopid" aria-label="Default select example">
                    <option value="0">-Hammasi-</option>
                    @foreach($shops as $row)
                    <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                    @endforeach
                </select>
            </div>
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
                <button type="submit" id="showreport" class="btn btn-success">ok</button>
            </div>
        </div>
    <hr>
    <h3><b class="sname"></b></h3>
    <div class="repottable"></div>
    <br>
    <a href="/storage/home/0/0">Orqaga</a>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#showreport').click(function(){
            var shopid = $("#shopid option:selected").val();
            var shoptext = $("#shopid option:selected").text();
            // alert(shoptext);
            var beginid = $("#beginday option:selected").val();
            var endid = $("#endday option:selected").val();
            var beginidtext = $("#beginday option:selected").text();
            var endidtext = $("#endday option:selected").text();
            var div = $('.sname');
            var table = $('.repottable');
            if(beginid > endid){
                alert("Sanani belgilashda xatolik!");
            }else{
                div.html(shoptext);
                $.ajax({
                    method: "GET",
                    url: '/storage/selectreport/'+shopid+'/'+beginid+'/'+endid,
                    success: function(data) {
                        table.html(data);
                    }
                });
            }
        });
    });
</script>
@endsection