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
    <div class="report"></div>
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
                method: "GET",
                data:{
                    'lastid' : regionid
                },
                url: '/accountant/getreportlargebase',
                success: function(data) {
                    div.html(data);
                }
            })
        }
    });

    $('.list-group-item-action').click(function() {
        var gardenid = $(this).attr('data-garden-id');
        // alert(gardenid);
        var div = $('.divmodproduct');
        $.ajax({
            method: "GET",
            url: '/technolog/getmodproduct/'+gardenid,
            success: function(data) {
                div.html(data);
            }

        })
    });
    
    function changeFunc() {
        var div = $('.divmodproduct');
        // div.html("<p value=''>1-sad</p>");
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
</script>
@endsection