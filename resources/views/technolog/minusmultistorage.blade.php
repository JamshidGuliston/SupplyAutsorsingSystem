@extends('layouts.app')
@section('css')
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
    b{
        color: #3c7a7c;
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
    th, td{
        font-size: 0.8rem;
        margin: .3rem .3rem;
        text-align: center;
        vertical-align: middle;
        border-bottom-color: currentColor;
        border-right: 1px solid #c2b8b8;
    }
    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    .year {
        text-align: center;
    }
    .month,
    .day {
        margin: 10px 20px;
        display: flex;
        justify-content: left;
    }

    .month__item{
        width: calc(100% / 12);
        text-align: center;
        border-bottom: 1px solid #000;
    }

    .month__item + .month__item {
        /* border-left: 1px solid #000; */
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
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<!-- Worker count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="pcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.editminusproduct')}}" method="POST">
		    @csrf
            <input type="hidden" name="monthid" value="{{ $monthid }}">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maxsulot og'irligini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="gardentitle"></h4>
                <div class="wor_countedit">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">O'zgartirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Cheldren count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="chcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="childrentitle"></h5>
                <div class="chil_countedit">

                </div>
                <div class="temp_count">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Menu edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="editnextmenuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menyuni o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="menutitle"></h5>
                <div class="menu_select">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="date">
    <div class="month">
        @foreach($months as $month)
            <a href="{{ route('technolog.minusmultistorage',  ['id' => $kingar->id, 'monthid' => $month->id ]) }}" class="month__item {{ (Request::is('technolog/minusmultistorage/'.$kingar->id.'/'.$month->id) or ($month->month_active == 1 and $monthid == 0)) ? 'active' : null }}">{{ $month->month_name }}</a>
        @endforeach
    </div>
</div>
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <b>- Шу ойда ишлатилган махсулотлар</b>
        </div>
        <div class="col-md-3">
            <!-- <b>Bog'chalarga so'rov yuborish</b>
            <a href="">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a> -->
        </div>
        <div class="col-md-3">
            <b>Боғча: {{ $kingar->kingar_name }}</b>
            <!-- <b>Taxminiy menyu yuborish</b>
            <a href="">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a> -->
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th style="width: 30px;">Махсулотлар</th>
                @foreach($days as $day)
                <th scope="col">{{ $day->day_number }}</th>
                @endforeach
                <?php
                for($i = 0; $i < 21-count($days); $i++){
                    ?>
                    <th scope="col"></th>
                    <?php
                }
                ?>
                <th style="width: 70px;">Жами:</th>
            </tr>
        </thead>
        <tbody>
            @foreach($minusproducts as $key => $row)
            <?php $all = 0; ?>
            <tr>
                <td>{{ $row['productname'] }}</td>
                @foreach($days as $day)
                    @if(isset($row[$day['id']."+"]) or isset($row[$day['id']."-"]))
                        <td>
                            {{ $row[$day['id']."+"] }}
                            <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#pcountModal" data-dayid="{{ $day->id }}" data-prodid="{{ $key }}" data-weight="{{ $row[$day['id'].'+'] }}" data-kinid="{{ $kingar->id }}" style="cursor: pointer; margin-right: 16px;"> </i>
                            @if($row[$day['id']."-"] != 0)
                                <hr>
                                {{ $row[$day['id']."-"] }}
                            @endif
                        </td>
                        <?php $all += $row[$day['id']."+"] + $row[$day['id']."-"]; ?>
                    @else
                        <td></td>
                    @endif
                @endforeach
                <?php
                for($i = 0; $i < 21-count($days); $i++){
                    ?>
                    <td></td>
                    <?php
                }
                ?>
                <td style="width: 70px;">{{ $all }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection

@section('script')
<script>
    $('.edites').click(function() {
        var kinid = $(this).attr('data-kinid');
        var dayid = $(this).attr('data-dayid');
        var kg = $(this).attr('data-weight');
        var prodid = $(this).attr('data-prodid');
        var div = $('.wor_countedit');
        div.html("<input type='hidden' name='kinid' class='form-control' value="+kinid+"><input type='hidden' name='dayid' class='form-control' value="+dayid+"><input type='hidden' name='prodid' class='form-control' value="+prodid+"><input type='text' name='kg' class='form-control' value="+kg+">");
        // title.html("<p>"+kn+"</p><input type='hidden' name='kingid' class='' value="+king+">");
    });

    $('.ch_countedit').click(function() {
        var nextrow = $(this).attr('data-nextrow-id');
        var chc = $(this).attr('data-child-count');
        var kn = $(this).attr('data-kinga-name');
        var temprow = $(this).attr('data-temprow-id');
        var tempchild = $(this).attr('data-tempchild-count');
        var div1 = $('.chil_countedit');
        var div2 = $('.temp_count');
        var title = $('.childrentitle');
        title.html("<p>"+kn+"</p><input type='hidden' name='nextrow' class='' value="+nextrow+"><input type='hidden' name='temprow' class='' value="+temprow+">");
        div1.html("<input type='number' name='agecount' class='form-control' value="+chc+">");
        div2.html("<br><p style='color: red'>Xabarnoma: <i class='far fa-envelope' style='color: #c40c0c'></i> "+tempchild+"</p>");
    });

    $('.next_menu').click(function() {
        var nextmenu = $(this).attr('data-nextmenu-id');
        var nextrow = $(this).attr('data-nextrow-count');
        var king = $(this).attr('data-king-name');
        var div = $('.menutitle');
        var select = $('.menu_select');
        div.html("<p>"+king+"</p><input type='hidden' name='nextrow' class='' value="+nextrow+">");
        $.ajax({
            method: "GET",
            url: '/technolog/fornextmenuselect',
            data: {
                'menuid': nextmenu,
            },
            success: function(data) {
                select.html(data);
            }
        })
    });
</script>
@endsection