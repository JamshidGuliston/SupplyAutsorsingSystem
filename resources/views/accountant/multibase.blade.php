@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
@endsection

@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
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