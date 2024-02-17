@extends('layouts.app')

@section('leftmenu')
@include('boss.sidemenu'); 
@endsection
@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
td{
	border-right: dashed 2px #999999;
	border-bottom: solid 1px #999999;
	padding-left: 10px;
}
th{
	border: solid 2px #198754;
	padding-left: 10px;
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
            <div class="col-md-3">Bog'chalar</div>
            <div class="col-md-3">Oy</div>
            <div class="col-md-2">Ustama</div>
            <div class="col-md-2">NDS</div>
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <select id='testSelect1' name="catid[]" class="form-select" aria-label="Default select example" multiple>
                    @foreach($kinds as $row)
                    <option value="{{$row['id']}}">{{$row['kingar_name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="month" name="start" aria-label="Default select example" required>
                    <option value="0">-Oy-</option>
                    @foreach($months as $row)
                        <option value="{{$row['id']}}">{{ $row['month_name']; }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" id="raise" class="form-control" >
            </div>
            <div class="col-md-2">
                <input type="number" id="nds" class="form-control" >
            </div>
            <div class="col-md-2">
                <button type="submit" id="showreport" class="btn btn-success">Hisobot</button>
            </div>
        </div>

    <hr>
    <div class="sname"></div>
    
    <div class="repottable"></div>
    
    <br>
    <a href="/boss/home">Orqaga</a>    
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#showreport').click(function(){
            var kindid = [];
            $.each($('#testSelect1 option:selected'), function(){
            	kindid.push($(this).val());
            });
            var monthid = $("#month option:selected").val();
            var raise = $("#raise").val();
            var nds = $("#nds").val();
            var kindtext = $("#testSelect1 option:selected").text();
            var monthtext = $("#month option:selected").text();
            var div = $('.sname');
            var table = $('.repottable');
            if(monthid == 0){
                alert("Oy tanlanmagan!");
            }else{
                div.html("<b>"+kindtext+"</b> "+monthtext+"");
                $.ajax({
                    method: "GET",
                    data: {kindid: kindid, monthid: monthid, raise: raise, nds: nds}, 
                    url: '/boss/showincome',
                    success: function(data) {
                        table.html(data);
                    }
                });
            }
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
    
    document.multiselect('#testSelect1')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
		});

	function enable() {
		document.multiselect('#testSelect1').setIsEnabled(true);
	}

	function disable() {
		document.multiselect('#testSelect1').setIsEnabled(false);
	}

    $("#select_type").change(function(){
        if(this.value == 2){
            document.multiselect('#testSelect1').setIsEnabled(false);
            document.multiselect('#testSelect2').setIsEnabled(false);
        }else{
            document.multiselect('#testSelect1').setIsEnabled(true);
            document.multiselect('#testSelect2').setIsEnabled(true);
        }
    });
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