@extends('layouts.app')
@section('css')
<style>
    .table{
        width: auto;
    }
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
@include(' accountant.sidemenu'); 
@endsection
@section('content')
<!-- EDD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot narxlarini yangilash</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
        </div>
    </div>
</div>

<!-- End -->
<!-- Modal -->
<div class="modal editesmodal fade" id="pcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maxsulot narxini o'zgartirish</h5>
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
<div class="modal editesmodal fade" id="modalsettings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hisobot: {{ $kindgar->kingar_name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label>Ustama</label>
                        <input class="form-control" id="ust" placeholder="Ustama %" value="0">
                    </div>
                    <div class="col-sm-6">
                        <label>QQS</label>
                        <input class="form-control" id="nds" placeholder="QQS %" value="0">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-4">
                        <input type="hidden" id="kind" name="kindid" value="{{ $kindgar->id }}" /> 
                        <select class="form-select" id="startdayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Sanadan</option>
                            @foreach($yeardays as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-select" id="enddayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Sanaga</option>
                            @foreach($yeardays as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-select" id="costdayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Narx sanasi</option>
                            @foreach($costs as $row)
                            <?php
                                if($row['month_id'] % 12 == 0){
                                    $mth = 12;
                                }else{
                                    $mth = $row['month_id'] % 12;
                                }
                            ?>
                                <option value="{{$row['day_id']}}">{{ sprintf("%02d", $row['day_number']).'.'.sprintf("%02d", $mth).'.'.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <h5 class="menutitle"></h5>
                <div class="urlpdf">
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-9">
            <b>{{ $kindgar->kingar_name .": ". $days[0]['month_name']; }}</b>
        </div>
        <div class="col-md-3">
            <!-- <b>Bog'chalarga so'rov yuborish</b>
            <a href="">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a> -->
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalsettings">Hisobot qurish</button>
        </div>
        <div class="col-md-3">
            <b></b>
            <!-- <i class="fas fa-plus-circle" style="color: #3c7a7c; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#Modalsadd"></i> -->
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <th style="width: 30px;">Махсулотлар/{{ $days[0]['month_name']; }}</th>
            <th style="width: 30px;">Нарх</th>
            @foreach($days as $day)
                <th scope="col">{{ $day->day_number; }}</th>
            @endforeach
            <th>Жами</th>
            <th>Сумма</th>
        </thead>
        <tbody>
            @foreach($nakproducts as $key => $row)
            <tr>
                <td>{{ isset($row['product_name']) ? $row['product_name'] : '...' }}</td>
                <td>{{ "1" }}</td>
                <?php 
                    $summ = 0;
                ?>
                @foreach($days as $day)
                    @if(isset($row[$day['id']]))
                        <td>
                        <?php  
                            printf("%01.2f", $row[$day['id']]); 
                            $summ += $row[$day['id']];
                        ?>
                        </td>
                    @else
                        <td>
                            {{ '0' }}
                        </td>
                    @endif
                @endforeach
                <td><?php printf("%01.2f", $summ) ?></td>
                <td><?php printf("%01.2f", $summ*1) ?></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"><a href="/accountant/reports">Orqaga</a></label>
        <div class="col-sm-6">
        <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
        </div>
    </div>
    
</div>
@endsection

@section('script')
<script>
    function changeFunc() {
        var selectBox = document.getElementById("startdayid");
        var start = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("enddayid");
        var end = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("costdayid");
        var cost = selectBox.options[selectBox.selectedIndex].value;
        var kindid = document.getElementById("kind").value; 
        var nds = document.getElementById("nds").value; 
        var ust = document.getElementById("ust").value; 
        var div = $('.urlpdf');
        if(start != "" && end != "" && cost != ""){
            var html = "4-7 ёшли<br><p>Накапител: <a href='/accountant/nakapit/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/nakapitexcel/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Cчёт фактура: <a href='/accountant/schotfaktur/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/schotfakturexcel/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Меёр: <a href='/accountant/norm/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/normexcel/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a></p>";
            html += "3-4 ёшли <br><p>Накапител: <a href='/accountant/nakapit/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/nakapitexcel/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Cчёт фактура: <a href='/accountant/schotfaktur/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/schotfakturexcel/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Меёр: <a href='/accountant/norm/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/normexcel/"+kindid+"/"+2+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a></p>";
            html += "Қисқа гурух<br><p> Накапител: <a href='/accountant/nakapit/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/nakapitexcel/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Cчёт фактура: <a href='/accountant/schotfaktur/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/schotfakturexcel/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Меёр: <a href='/accountant/norm/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/normexcel/"+kindid+"/"+3+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a></p>";
            html += "3-7 ёшли<br><p>Накапител: <a href='/accountant/nakapit/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/nakapitexcel/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Cчёт фактура: <a href='/accountant/schotfaktur/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/schotfakturexcel/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Меёр: <a href='/accountant/norm/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/normexcel/"+kindid+"/"+4+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a></p>";
            html += "Умумий фактура<br><p> Cчёт фактура: <a href='/accountant/allschotfaktur/"+kindid+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/allschotfakturexcel/"+kindid+"/"+start+"/"+end+"/"+cost+"/"+nds+"/"+ust+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a>";

            div.html(html);
        }
    }
    $('.edites').click(function() {
        var regid = $(this).attr('data-regionid');
        var dayid = $(this).attr('data-dayid');
        var prodid = $(this).attr('data-prodid');
        var kg = $(this).attr('data-weight');
        var div = $('.wor_countedit');
        div.html("<input type='hidden' name='prodid' class='form-control' value="+prodid+"><input type='hidden' name='regid' class='form-control' value="+regid+"><input type='hidden' name='dayid' class='form-control' value="+dayid+"><input type='text' name='kg' class='form-control' value="+kg+">");
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