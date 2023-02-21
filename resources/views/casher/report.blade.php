@extends('layouts.app')

@section('leftmenu')
@include('casher.sidemenu'); 
@endsection
@section('css')
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
</style>
@endsection
@section('content')
<!-- Edite -->
<div class="modal editesmodal fade" id="Modalgarden" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('technolog.bindgarden')}}" method="post">
                @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Katta xarajatlar turi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                
                <div id="ghidden"></div>
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
<!-- delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('casher.alldeletecost')}}" method="post">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="deletename"></div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn dele btn-danger">O'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- DELET -->
<div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-3">Katta tur</div>
            <div class="col-md-3">Kichik tur</div>
            <div class="col-md-2">Dan</div>
            <div class="col-md-2">Gacha</div>
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <select id="RegionSelect" class="form-select" name="catid" aria-label="Default select example">
                    <option value="0">Hammasi</option>
                    @foreach($costs as $row)
                    <option value="{{$row['id']}}">{{$row['cost_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="allcosts"></div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="beginday" name="start" aria-label="Default select example" required>
                    <option value="0">-Sanadan-</option>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="endday" name="start" aria-label="Default select example" required>
                    <option value="0">-Sanagacha-</option>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" id="showreport" class="btn btn-success">Ko'rish</button>
            </div>
        </div>

    <hr>
    <div class="sname"></div>
    
    <div class="repottable"></div>
    
    <br>
    <a href="/casher/home">Orqaga</a>    
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#showreport').click(function(){
            var costid = $("#RegionSelect option:selected").val();
            var beginid = $("#beginday option:selected").val();
            var endid = $("#endday option:selected").val();
            var costtext = $("#RegionSelect option:selected").text();
            var allcostid = $("#allcostid option:selected").val();
            var allcosttext = $("#allcostid option:selected").text();
            var div = $('.sname');
            var table = $('.repottable');
            if(beginid == 0 || endid == 0 || beginid > endid){
                alert("Sanani belgilashda xatolik!");
            }else if(costid == 0){
                div.html("<h3><b>"+costtext+"</b></h3>");
                $.ajax({
                    method: "GET",
                    url: '/casher/selectreport/0/'+0+'/'+beginid+'/'+endid,
                    success: function(data) {
                        table.html(data);
                    }
                });
            }else if(allcostid == 0){
                div.html("<h3><b>"+costtext+"</b></h3>");
                $.ajax({
                    method: "GET",
                    url: '/casher/selectreport/1/'+costid+'/'+beginid+'/'+endid,
                    success: function(data) {
                        table.html(data);
                    }
                });
            }else{
                div.html("<h3><b>"+allcosttext+"</b></h3>");
                $.ajax({
                    method: "GET",
                    url: '/casher/selectreport/2/'+allcostid+'/'+beginid+'/'+endid,
                    success: function(data) {
                        table.html(data);
                    }
                });
            }
        });
        $('#RegionSelect').change(function(){
            var costid = $("#RegionSelect option:selected").val();
            var regiontext = $("#RegionSelect option:selected").text();
            var div = $('.allcosts');
            $.ajax({
                method: "GET",
                url: '/casher/selectallcost/'+costid,
                success: function(data) {
                    div.html(data);
                }
            })
        });
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            var div = $('#hiddenid');
            div.html("<input type='hidden' name='id' value="+g+">");
        });

        $('.detete').click(function() {
            deleteid = $(this).attr('data-delet-id');
            pro_name = $(this).attr('data-name-id');
            var div = $('.deletename');
            div.html("<p>"+pro_name+" maxsulotini o'chirish.</p><input type='hidden' name='costid' value="+deleteid+">");
            
        });
    });
    function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
</script>
@if(session('status'))
<script> 
    // alert('{{ session("status") }}');
    swal({
        title: "Ajoyib!",
        text: "{{ session('status') }}",
        icon: "success",
        button: "ok",
    });
</script>
@endif
@endsection