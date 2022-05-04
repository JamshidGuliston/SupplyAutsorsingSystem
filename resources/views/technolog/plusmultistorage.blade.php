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
</style>
@endsection
@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<!-- Worker count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="wcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ishchilar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="gardentitle"></h4>
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
            <b>+ Шу ойда qo'shilgan махсулотлар</b>
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
                <th style="width: 70px;">Қолдиқ:</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plusproducts as $key => $row)
            @if(is_numeric($key) and isset($minusproducts[$key]))
            <?php $ke = $key; $allq = $minusproducts[$key];  $all = 0;?>
            @else
            <?php $all = 0;  $allq = 0 ?>
            @endif
            <tr>
                <td>{{ $row['productname'] }}</td>
                @foreach($days as $day)
                    @if(isset($row[$day['id']]))
                        <td>+{{ $row[$day['id']] }}</td>
                        <?php $all += $row[$day['id']]; ?>
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
                <td style="width: 70px;">+{{ $all }}</td>
                
                <td style="width: 70px;">{{ round($all-$allq, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection

@section('script')
<script>
    $('.w_countedit').click(function() {
        var king = $(this).attr('data-menu-id');
        var wc = $(this).attr('data-wor-count');
        var kn = $(this).attr('data-king-name');
        var div = $('.wor_countedit');
        var title = $('.gardentitle');
        div.html("<input type='number' name='workers' class='form-control' value="+wc+">");
        title.html("<p>"+kn+"</p><input type='hidden' name='kingid' class='' value="+king+">");
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