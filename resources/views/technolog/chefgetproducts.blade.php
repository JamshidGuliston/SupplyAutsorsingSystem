@extends('layouts.app')
@section('css')
<style>
    .loader-box {
        width: 100%;
        background-color: #80afc68a;
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
    .vrt-header span{
        display: inline-block;
        text-align: center;
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -ms-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
        transform: rotate(-90deg);
        white-space: nowrap;
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
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<!-- Worker count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.chefeditproductw')}}" method="post">
		    @csrf
            <input type="hidden" name="dayid" value="{{ $day->id }}">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maxsulot qiymatini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="prodtitle"></h4>
                <div class="wor_countedit">

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
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <b>- Шу кунда ишлатилган махсулотлар</b>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <b>Сана: {{ $day->day_number.".".$day->month_name.".".$day->year_name }}</b>
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th>T/R</th>
                <th style="width: 30px;">Боғчалар</th>
                @foreach($products as $row)
                @if(isset($row->yes))
                    <th class='vrt-header' style="padding: 0px; width: 3%; height: 85px"><span>{{ $row->product_name }}</span></th>
                @endif
                @endforeach
                <th>Тўлиқлиги</th>
            </tr>
        </thead>
        <tbody>
            @foreach($all as $key => $kind)
            <?php $allcount = 0; ?>
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $kind['name'] }}</td>
                @foreach($products as $value)
                    @if(isset($value->yes) and isset($kind[$value->id]))
                        <td>{{ $kind[$value->id] }}<br><i data-edites-id="{{ $kind[$value->id] }}" data-product-name="{{ $value->product_name }}" data-product-id="{{ $value->id }}" data-kind-id="{{ $key }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#editModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i></td>
                        <?php $allcount ++; ?>
                    @else
                        <td></td>
                    @endif
                @endforeach
                @if($allcount == count($products))
                    <td>{{ "тўлиқ" }}</td>
                @else
                    <td>{{ "тўлиқ эмас" }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection

@section('script')
<script>
    $('.editess').click(function() {
        var kindid = $(this).attr('data-kind-id');
        var productname = $(this).attr('data-product-name');
        var productid = $(this).attr('data-product-id');
        var change = $(this).attr('data-edites-id');
        var div = $('.wor_countedit');
        var title = $('.prodtitle');
        div.html("<input type='text' name='kg' class='form-control' value="+change+">");
        title.html("<p>"+productname+"</p><input type='hidden' name='kingid' class='' value="+kindid+"><input type='hidden' name='prodid' class='' value="+productid+">");
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