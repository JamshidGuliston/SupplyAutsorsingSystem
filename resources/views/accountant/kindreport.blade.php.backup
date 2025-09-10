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
    
    /* Hisobot modeli uchun yangi stillar */
    .report-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }
    
    .report-section h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .report-section h6 i {
        margin-right: 8px;
        color: #007bff;
    }
    
    .report-category {
        background-color: #e9ecef;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
    }
    
    .report-category h6 {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .report-links {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    
    .report-link {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        text-decoration: none;
        color: #495057;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .report-link:hover {
        background-color: #007bff;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .report-link i {
        margin-right: 4px;
        font-size: 0.9rem;
    }
    
    .report-link.pdf {
        border-color: #dc3545;
        color: #dc3545;
    }
    
    .report-link.pdf:hover {
        background-color: #dc3545;
        color: white;
    }
    
    .report-link.excel {
        border-color: #198754;
        color: #198754;
    }
    
    .report-link.excel:hover {
        background-color: #198754;
        color: white;
    }
    
    .age-group-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .general-invoice {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 0.95rem;
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hisobot: {{ $kindgar->kingar_name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
        var div = $('.urlpdf');
        
        if(start != "" && end != "" && cost != ""){
            var html = '<div class="report-section">';
            
            // Qisqa guruh
            html += '<div class="age-group-header">';
            html += '<i class="fas fa-child"></i>Қисқа гурух (3 yosh)';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-warehouse"></i>Накапител</h6>';
            html += '<div class="report-links">';
            html += '<span class="me-2">Narx bilan:</span>';
            html += '<a href="/accountant/nakapit/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/nakapitexcel/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '<span class="me-2 ms-3">Narxsiz:</span>';
            html += '<a href="/accountant/nakapitwithoutcost/'+kindid+'/3/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/nakapitexcelwithoutcost/'+kindid+'/3/'+start+'/'+end+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Счёт фактура</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/schotfaktur/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/schotfakturexcel/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-ruler"></i>Меёр</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/norm/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/normexcel/'+kindid+'/3/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            // 3-7 yoshli
            html += '<div class="age-group-header">';
            html += '<i class="fas fa-users"></i>3-7 ёшли (4 yosh)';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-warehouse"></i>Накапител</h6>';
            html += '<div class="report-links">';
            html += '<span class="me-2">Narx bilan:</span>';
            html += '<a href="/accountant/nakapit/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/nakapitexcel/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '<span class="me-2 ms-3">Narxsiz:</span>';
            html += '<a href="/accountant/nakapitwithoutcost/'+kindid+'/4/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/nakapitexcelwithoutcost/'+kindid+'/4/'+start+'/'+end+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Счёт фактура</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/schotfaktur/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/schotfakturexcel/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-ruler"></i>Меёр</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/norm/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/normexcel/'+kindid+'/4/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            // Umumiy faktura
            html += '<div class="general-invoice">';
            html += '<i class="fas fa-file-invoice-dollar"></i>Умумий фактура';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Счёт фактура</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/schotfaktursecond/'+kindid+'/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/allschotfakturexcel/'+kindid+'/'+start+'/'+end+'/'+cost+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            
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