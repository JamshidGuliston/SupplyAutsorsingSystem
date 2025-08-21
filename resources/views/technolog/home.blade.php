@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
    .loader-box {
        width: 100%;
        background-color: #80afc68a;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        display: none;
        justify-content: center;
    }
    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #3498db;
        width: 30px;
        display: block;
        height: 30px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: absolute;
        left: 353px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .divmodproduct{
        text-align: center;
    }
    @keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="date">
    <div class="year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="/technolog/showdate/{{ $year->id-1 }}/0/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/technolog/showdate/{{ $year->id }}/{{ $month->id }}/0" class="month__item {{ ( $month->month_active == 1 ) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/technolog/showdate/{{ $year->id+1 }}/0/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
    <div class="day">
        @foreach($date as $day)
        <a href="/technolog/showdate/{{ $day->year_id }}/{{ $day->month_id }}/{{ $day->id }}" class="day__item">{{ $day->day_number }}</a>
        @endforeach
        <div id="timeline">
            @php
                $nextDay = $tomm;
            @endphp
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
            @elseif($date[count($date)-1]->day_number != date("d", $tomm))
            <div class="dot" id="four" type="button" data-bs-toggle="modal" data-bs-target="#exampleModals">
                <span>{{ date("d", $tomm) }}</span>
                <date>{{ date("F", $tomm) }}</date>
            </div>
            @endif 
            <!-- $date[0]->day_number == date("d", $tomm) -->
            @if($date[count($date)-1]->day_number == date("d", $tomm))
                @php
                $nextDay = strtotime("+1 day", $tomm);
                @endphp
            @endif
            <div class="dot" id="four2">
                <a href="{{ route('technolog.sendmenu', ['day'=> date('d-F-Y', $tomm)]); }}"><span>{{ date("d", $nextDay) }}</span></a>
                <date>{{ date("F", $nextDay) }}<b style="color: red;">{{ " Taxminiy" }}</b></date>
            </div>
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
    <!-- Modal inout -->
    <div class="modal fade" id="Modalinout" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Xisobot qurish</h5>
                    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('technolog.reportinout')}}" method="GET" target="_blank">
                    @csrf
                    <input type="hidden" id="kindergarden_id" name="kindergarden_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="kindergarden-name col-sm-12">
                            </div>
                        <hr>
                        <div class="col-sm-12">
                            <select class="form-select" name="month_id" aria-label="Default select example" required>
                                <option value="">Oyni tanlang</option>
                                @foreach($monthsofyears as $row)
                                    <option value="{{$row['id']}}">{{ $row['month_name'].'-'.$row['year_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn add-age btn-primary text-white">Ko'rish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><b class="kindname"></b> Omborxona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- <form method='post' action='/technolog/plusmultimodadd'>
                    @csrf -->
                    <div class="modal-body" style="text-align: center;">
                        <div class="divmodproduct">
                            <div class="loader-box">
                                <div class="loader"></div>
                            </div>
                        </div>
                        <!-- <button type="submit"  class="btn btn-success" >Saqlash</button> -->
                    </div>
                <!-- </form> -->
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
                <!-- <i class="fas fa-school fs-1 primary-text border rounded-full secondary-bg p-3"></i> -->
                <i class="fas fa-school fs-1 primary-text border rounded-full"></i>
                <div>
                    <a href="#!" class="list-group-item-action bg-transparent first-text fw-bold" class="fs-5" data-name ="{{ $item->kingar_name }}" data-garden-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#exampleModal" style="color: #6ac3de;">{{$item->kingar_name}}</a>
                    <div class="user-box">
                        <div class="user-worker-number">
                            <i class="fas fa-users" style="color: #959fa3; margin-right: 8px; font-size: 20px;"></i>
                            <h2 class="text-sizes fs-2 m-0">{{$item->worker_count}}</h2>
                        </div>
                        <a href="{{ route('technolog.plusmultistorage',  ['id' => $item->id, 'monthid' => 0 ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 14px;"><i class="fas fa-plus"></i></a>
                        <a href="{{ route('technolog.minusmultistorage',  ['id' => $item->id, 'monthid' => 0 ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 14px;"><i class="fas fa-minus"></i></a>
                        <a href="#!" style="color: #959fa3; margin-right: 6px; font-size: 14px;" ><i class="inout fas fa-exchange-alt" data-kname ="{{ $item->kingar_name }}" data-garden-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#Modalinout"></i></a>
                        <a href="{{ route('technolog.weightcurrent',  ['kind' => $item->id, 'yearid' => 0, 'monthid' => 0 ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-balance-scale"></i></a>
                        <a href="{{ route('technolog.settings',  ['id' => $item->id ]) }}" style="color: #959fa3; margin-right: 6px; font-size: 20px;"><i class="fas fa-cog"></i></a>
                    </div>
                </div>
                
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

    $('.inout').click(function() {
        var gardenid = $(this).attr('data-garden-id');
        document.getElementById("kindergarden_id").value = gardenid;
        var name = $(this).attr('data-kname');
        var div = $('.kindergarden-name');
        div.html(name);
    });

    $('#three').hide();
    $('#two').hide();

    $('.list-group-item-action').click(function() {
        var gardenid = $(this).attr('data-garden-id');
        var name = $(this).attr('data-name');
        var title = $('.kindname');
        title.html(name);
        var div = $('.divmodproduct');
        $.ajax({
            method: "GET",
            url: '/technolog/getmodproduct/'+gardenid,
            beforeSend: function() {
                div.html("<div class='loader-box'><div class='loader'></div></div>");
                $('.loader-box').show();
            },
            success: function(data) {
                div.html(data);
                $('.loader-box').hide();
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