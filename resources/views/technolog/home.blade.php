@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
    .date{
        margin-bottom: 30px;
    }
    .modal-header{
        background-color: ghostwhite;
    }
    .year {
        text-align: center;
    }
    .month{
        margin: 10px 20px;
        display: flex;
        justify-content: left;
    }
    .day {
        margin: 10px 20px;
        display: flex;
        justify-content: left;
        height: 34px;
    }

    .month__item{
        width: calc(100% / 13);
        text-align: center;
        border-bottom: 1px solid #000;
    }
    .day__item{
        background-color: #ecf6f1;
        text-align: center;
        vertical-align: middle;
        min-width: 34px;
        padding: 5px;
        margin-left: 5px;
        border-radius: 50%;
    }

    .month__item, .day__item{
        color: black;
        cursor: context-menu;
        /* border: 1px solid #87706a; */
        text-decoration: none;
    }
    .active{
        background-color: #23b242;
        color: #fff;
    }
    .month__item:hover,
    .day__item:hover{
        background-color: #23b242;
        color: #fff;
        transition: all .5s;
        cursor: pointer;
    }
    table, th{
        padding: 10px;
        border-collapse: collapse;
        background-color: white;
    }
    tr:hover {background-color: aliceblue;}
    td, th{
        text-align: center;
    }
    span{
        color: black;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="date">
    <div class="month">
        @if($year->id != 1)
            <a href="/technolog/showdate/{{ $year->id-1 }}/0/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/technolog/showdate/{{ $year->id }}/{{ $month->id }}/0" class="month__item {{ ($month->month_active == 1) ? 'active' : null }}">{{ $month->month_name }}</a>
        @endforeach
        <a href="/technolog/showdate/{{ $year->id+1 }}/0/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
    <div class="day">
        @foreach($date as $day)
        <a href="/technolog/showdate/{{ $day->year_id }}/{{ $day->month_id }}/{{ $day->id }}" class="day__item">{{ $day->day_number }}</a>
        @endforeach
        <div id="timeline">
            <!-- //date -->
            <!-- @if(!empty($date) and count($date)>2)
            <div class="dot" id="one">
                <a href="{{ route('technolog.sendmenu', ['day'=> $date[count($date)-1]->id]); }}"><span>{{ $date[count($date)-1]->day_number }}</span></a>
                <date>{{ $date[count($date)-1]->month_name }}</date>
            </div>
            @endif -->
            @if(!empty($date) and count($date)>1)
            <div class="dot" id="two">
                <a href="#"><span>{{ "" }}</span></a>\
            </div>
            @endif
            @if(!empty($date) and count($date)>0)
            <div class="dot" id="three">
                <a href="#"><span>{{ "" }}</span></a>
            </div>
            @endif
            @if(empty($date))
            <div class="dot" id="four" type="button" data-bs-toggle="modal" data-bs-target="#exampleModals">
                <span>{{ date("d", $tomm) }}</span>
                <date>{{ date("F", $tomm) }}</date>
            </div>
            @elseif($date[0]->day_number != date("d", $tomm))
            <div class="dot" id="four" type="button" data-bs-toggle="modal" data-bs-target="#exampleModals">
                <span>{{ date("d", $tomm) }}</span>
                <date>{{ date("F", $tomm) }}</date>
            </div>
            @endif 
            <!-- $date[0]->day_number == date("d", $tomm) -->
            @if(1)
            <div class="dot" id="four2">
                <a href="{{ route('technolog.sendmenu', ['day'=> date('d-F-Y', $tomm)]); }}"><span>{{ "T" }}</span></a>
                <date>{{ "Taxminiy" }}</date>
            </div>
            @endif
            <div class="inside"></div>
        </div>
    </div>
</div>
<div class="container-fluid px-4">
    <!-- Modals -->
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button> -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Omborxona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method='post' action='/technolog/plusmultimodadd'>
                    @csrf
                    <div class="modal-body" style="text-align: center;">
                        <div class="divmodproduct">
                        </div>
                        <button type="submit"  class="btn btn-success" >Saqlash</button>
                    </div>
                </form>
                <div class="modal-footer" >

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModals" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('technolog.newday') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Yangi ish kuni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="daynum" value="{{ date('d', $tomm) }}" />
                        <input type="hidden" class="form-control" name="daymonth" value="{{ date('F', $tomm) }}" />
                        <input type="hidden" class="form-control" name="dayyear" value="{{ date('Y', $tomm) }}" />
                        {{ date('d', $tomm) ." - ". date("F", $tomm) }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Yaratish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- end date -->

    <div class="row g-3 my-2">
        @foreach($kingardens as $item)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <a href="#!" class="list-group-item-action bg-transparent first-text fw-bold" class="fs-5" data-garden-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#exampleModal" style="color: #6ac3de;">{{$item->kingar_name}}</a>

                    <div class="user-box">
                        <div class="user-worker-number">
                            <i class="fas fa-users" style="color: #959fa3; margin-right: 8px; font-size: 20px;"></i>
                            <h2 class="text-sizes fs-2 m-0">{{$item->worker_count}}</h2>
                        </div>
                        <a href="{{ route('technolog.plusmultistorage',  ['id' => $item->id, 'monthid' => 0 ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 14px;"><i class="fas fa-plus"></i></a>
                        <a href="{{ route('technolog.minusmultistorage',  ['id' => $item->id, 'monthid' => 0 ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 14px;"><i class="fas fa-minus"></i></a>
                        <a href="{{ route('technolog.settings',  ['id' => $item->id ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
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
    $('.fa-school').click(function() {
        var gardenid = $(this).attr('data-garden-id');
        $.ajax({
            method: "GET",
            url: '/technolog/getmodproduct/'+gardenid,
            success: function(data) {
                div.html(data);
            }

        })
    });

    $('#three').hide();
    $('#two').hide();

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
    
    $('#today').change(function() {
        var menuid = $("#today option:selected").val();
        var div = $('.today');
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodnametoday',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    });
    $('#tomorrow').change(function() {
        var menuid = $("#tomorrow option:selected").val();
        var div = $('.tomorrow');
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodnametomorrow',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    });
    window.addEventListener('load', MyFunc, true);
    var i = 0;
    var j = 0;
    
    // document.multiselect('#testSelect1')
	// 	.setCheckBoxClick("checkboxAll", function(target, args) {
	// 		console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
	// 	})
	// 	.setCheckBoxClick("1", function(target, args) {
	// 		console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
	// 	});
    
    // document.multiselect('#testSelect2')
	// 	.setCheckBoxClick("checkboxAll", function(target, args) {
	// 		console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
	// 	})
	// 	.setCheckBoxClick("1", function(target, args) {
	// 		console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
	// 	});
    
    function divchange() {
        var divtag = document.getElementById("four");
        var bgcolor = ["#d2f8e9", "#ee928e"];
        divtag.style.backgroundColor = bgcolor[i];
        i = (i + 1) % bgcolor.length;
    }

    function MyFunc() {
        setInterval(divchange, 1000);
    }

    
</script>
@endsection