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
@endsection

@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Modals -->
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button> -->

    <!-- Modal -->
    <div class="modal fade" id="modalsettings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xodimlar hisoboti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('accountant.svodworkers')}}" method="GET" target="_blank">
                <div class="row modal-body">
                    @csrf
                    <div class="col-sm-4">
                        <select id='testSelect1' name="kindgardens[]" class="form-select" aria-label="Default select example" multiple required>
                            @foreach($kinds as $row)
                                <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select id="RegionSelect" class="form-select" id="enddayid" name="region_id" aria-label="Default select example" required>
                            <option value="">-Narx-</option>
                            @foreach($regions as $row)
                                <option value="{{$row['id']}}">{{ $row['region_name']; }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <div class="region_narx"></div>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-select" id="enddayid" name="start" aria-label="Default select example" required>
                            <option value="">-Sanadan-</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required>
                            <option value="">-Sanaga-</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        Ustama % da
                        <input type="number" name="over" class="form-control" required>
                    </div>
                    <div class="col-sm-4">
                        NDS % da
                        <input type="number" name="nds" class="form-control" required>
                    </div>
                    <div class="col-sm-4">
                        Yuklab olish
                        <button type="submit" class="btn btn-info form-control">PDF <i class="fas fa-download" aria-hidden="true"></i></button>
                    </div>
                    <br/>
                </div>
                </form>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button> -->
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 my-2">
        <div class="col-md-9">
        </div>
        <div class="col-md-3">
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalsettings">Umumiy Svod</button>
        </div>
        @foreach($kinds as $item)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <a href="kindreportworker/{{ $item->id }}" class="list-group-item-action bg-transparent first-text fw-bold" class="fs-5" data-garden-id="{{ $item->id }}" style="color: #6ac3de;">{{$item->kingar_name}}</a>
                    <div class="user-box">
                    </div>
                </div>
                <i class="fas fa-school fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>
        @endforeach
    </div>

    

</div>
@endsection
@section('script')
<script>
    $('#RegionSelect').change(function(){
        var regionid = $("#RegionSelect option:selected").val();
        var regiontext = $("#RegionSelect option:selected").text();
        var div = $('.region_narx');
        $.ajax({
            method: "GET",
            url: '/accountant/narxselect/'+regionid,
            success: function(data) {
                div.html(data);
            }
        })
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