@extends('layouts.app')
@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
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
    
    /* Jadval uchun scroll wrapper */
    .table-wrapper {
        overflow-x: auto;
        position: relative;
    }
    
    .table-light {
        border-collapse: collapse;
        width: max-content;
        min-width: 100%;
    }
    
    th, td{
        font-size: 0.8rem;
        margin: .3rem .3rem;
        text-align: center;
        vertical-align: middle;
        border-bottom-color: currentColor;
        border-right: 1px solid #c2b8b8;
        white-space: nowrap;
    }
    
    /* Birinchi ustunni qotib qo'yish (Mahsulotlar) */
    .table-light thead th:first-child,
    .table-light tbody td:first-child {
        position: sticky;
        left: 0;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .table-light thead th:first-child {
        z-index: 11;
    }
    
    /* Editable cells */
    .editable-cell {
        cursor: pointer;
        position: relative;
    }
    
    .editable-cell:hover {
        background-color: #fff3cd;
    }
    
    .editable-cell input {
        width: 100%;
        border: 2px solid #007bff;
        padding: 2px 5px;
        text-align: center;
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
<!-- Qoldiqni shu oyga ko'chirish -->
<div class="modal editesmodal fade" id="qoldiqModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('technolog.moveremainder') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Qoldiqni ko'chirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>O'tgan oydan qoldiqni shu oyga ko'chirish</h5>
                    <input type="hidden" name="kind" value="{{ $kingar->id }}">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ko'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    <div class="year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="{{ route('technolog.plusmultistorage', ['id' => $kingar->id, 'monthid' => 0]) }}" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="{{ route('technolog.plusmultistorage', ['id' => $kingar->id, 'monthid' => $month->id]) }}" class="month__item {{ (Request::is('technolog/plusmultistorage/'.$kingar->id.'/'.$month->id) or ($month->month_active == 1 and $monthid == 0)) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="{{ route('technolog.plusmultistorage', ['id' => $kingar->id, 'monthid' => 0]) }}" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <b>+ Шу ойда qo'shilgan махсулотлар</b>
            <button style="margin-left: 10px;" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#ModalQoldiqOlish">Qoldiq olish</button>
        </div>
        <div class="col-md-3">
            <b>Боғча: {{ $kingar->kingar_name }}</b>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('technolog.storageChangeLogs', ['id' => $kingar->id, 'monthid' => $monthid]) }}" class="btn btn-sm btn-info">
                <i class="fas fa-history"></i> O'zgarishlar tarixi
            </a>
            <a href="{{ route('technolog.plusmultistoragePDF', ['id' => $kingar->id, 'monthid' => $monthid]) }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('technolog.plusmultistorageExcel', ['id' => $kingar->id, 'monthid' => $monthid]) }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
    <hr>
    <div class="table-wrapper">
        <table class="table table-light py-4 px-4">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 150px; vertical-align: middle;">Махсулотлар</th>
                <!-- o'tgan oydan qoldiq va Qoldiqni shu oyga ko'chirish -->
                <th rowspan="2" scope="col" style="vertical-align: middle;">{{ "O'tgan oydan Qoldiq" }} <i class="fa fa-plus" style="color: #23b242; font-size: 18px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#qoldiqModal"></i></th>
                @foreach($days as $day)
                <th colspan="2" scope="col" style="text-align: center;">{{ $day->day_number }}</th>
                @endforeach
                <?php
                for($i = 0; $i < 21-count($days); $i++){
                    ?>
                    <th colspan="2" scope="col"></th>
                    <?php
                }
                ?>
                <th rowspan="2" style="width: 80px; vertical-align: middle;">Жами киритилган</th>
                <th rowspan="2" style="width: 80px; vertical-align: middle;">Жами сарфланган</th>
                <th rowspan="2" style="width: 80px; vertical-align: middle;">Фарқи</th>
            </tr>
            <tr>
                @foreach($days as $day)
                <th scope="col" style="text-align: center; font-size: 0.7rem;">-</th>
                <th scope="col" style="text-align: center; font-size: 0.7rem;">+</th>
                @endforeach
                <?php
                for($i = 0; $i < 21-count($days); $i++){
                    ?>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <?php
                }
                ?>
            </tr>
        </thead>
        <tbody>
            @foreach($plusproducts as $key => $row)
            <?php 
                $totalMinus = 0;  // Jami sarflangan
                $totalPlus = 0;   // Jami kiritilgan
                $residualWeight = isset($residualProducts[$key]) ? $residualProducts[$key]['weight'] : 0;
                $totalPlus += $residualWeight; // O'tgan oydan qoldiqni kirimga qo'shish
            ?>
            <tr>
                <td>{{ $row['productname'] }}</td>
                <td>{{ $residualWeight > 0 ? $residualWeight : 0 }}</td>
                @foreach($days as $day)
                    <?php
                        $minusValue = isset($minusproducts[$key][$day['id']]) ? $minusproducts[$key][$day['id']] : 0;
                        $plusValue = isset($row[$day['id']."+"], $row[$day['id']."-"]) ? $row[$day['id']."+"] : 0;
                        $totalMinus += $minusValue;
                        $totalPlus += $plusValue;
                    ?>
                    <td class="editable-cell editable-minus" 
                        data-product-id="{{ $key }}" 
                        data-day-id="{{ $day['id'] }}" 
                        data-value="{{ $minusValue }}">
                        {{ $minusValue > 0 ? $minusValue : '' }}
                    </td>
                    <td class="editable-cell editable-plus" 
                        data-product-id="{{ $key }}" 
                        data-day-id="{{ $day['id'] }}" 
                        data-value="{{ $plusValue }}">
                        {{ $plusValue > 0 ? $plusValue : '' }}
                    </td>
                @endforeach
                <?php
                for($i = 0; $i < 21-count($days); $i++){
                    ?>
                    <td></td>
                    <td></td>
                    <?php
                }
                ?>
                <td style="width: 80px;">{{ round($totalPlus, 2) }}</td>
                <td style="width: 80px;">{{ round($totalMinus, 2) }}</td>
                <td style="width: 80px;">{{ round($totalPlus - $totalMinus, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    
</div>
@endsection

@section('script')
<script>
    // Editable cells
    let editingCell = null;
    
    // Minus (sarflangan) edit
    $(document).on('click', '.editable-minus', function() {
        if(editingCell) return;
        
        editingCell = $(this);
        let value = $(this).data('value');
        let productId = $(this).data('product-id');
        let dayId = $(this).data('day-id');
        
        $(this).html('<input type="number" step="0.01" value="' + value + '" class="edit-input" />');
        $(this).find('input').focus().select();
        
        $(this).find('input').on('blur keypress', function(e) {
            if(e.type === 'blur' || e.which === 13) {
                let newValue = parseFloat($(this).val()) || 0;
                saveMinusValue(productId, dayId, newValue, editingCell);
            }
        });
    });
    
    // Plus (kirim) edit
    $(document).on('click', '.editable-plus', function() {
        if(editingCell) return;
        
        editingCell = $(this);
        let value = $(this).data('value');
        let productId = $(this).data('product-id');
        let dayId = $(this).data('day-id');
        
        $(this).html('<input type="number" step="0.01" value="' + value + '" class="edit-input" />');
        $(this).find('input').focus().select();
        
        $(this).find('input').on('blur keypress', function(e) {
            if(e.type === 'blur' || e.which === 13) {
                let newValue = parseFloat($(this).val()) || 0;
                savePlusValue(productId, dayId, newValue, editingCell);
            }
        });
    });
    
    function saveMinusValue(productId, dayId, value, cell) {
        $.ajax({
            method: "POST",
            url: '{{ route("technolog.editMinusStorage") }}',
            data: {
                '_token': '{{ csrf_token() }}',
                'product_id': productId,
                'day_id': dayId,
                'kingarden_id': '{{ $kingar->id }}',
                'weight': value
            },
            success: function(response) {
                cell.data('value', value);
                cell.html(value > 0 ? value : '');
                editingCell = null;
                location.reload(); // Jami ustunlarni yangilash uchun
            },
            error: function() {
                alert('Xatolik yuz berdi!');
                cell.html(cell.data('value') > 0 ? cell.data('value') : '');
                editingCell = null;
            }
        });
    }
    
    function savePlusValue(productId, dayId, value, cell) {
        $.ajax({
            method: "POST",
            url: '{{ route("technolog.editPlusStorage") }}',
            data: {
                '_token': '{{ csrf_token() }}',
                'product_id': productId,
                'day_id': dayId,
                'kingarden_id': '{{ $kingar->id }}',
                'weight': value
            },
            success: function(response) {
                cell.data('value', value);
                cell.html(value > 0 ? value : '');
                editingCell = null;
                location.reload();
            },
            error: function() {
                alert('Xatolik yuz berdi!');
                cell.html(cell.data('value') > 0 ? cell.data('value') : '');
                editingCell = null;
            }
        });
    }

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

<!-- Qoldiq olish modal oynasi -->
<div class="modal fade" id="ModalQoldiqOlish" tabindex="-1" aria-labelledby="ModalQoldiqOlishLabel" aria-hidden="true">
<div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Qoldiqni qo'shish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('technolog.addResidualStorage')}}" method="POST">
                @csrf
                <input type="hidden" name="kingarden_id" value="{{ $kingar->id }}">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <select class="form-select" name="day_id" aria-label="Default select example" required>
                            <option value="">Sana tanlang</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Izoh</label>
                            <input class="form-control" name="title" value="{{ time().' '.$kingar->kingar_name }}" required>
                        </div>
                    </div> 
                    <hr> 
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">O'lchami</th>
                                <th scope="col">Narxi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($products as $all)
                                <tr>
                                    <td scope="row">{{ ++$i }}</td>
                                    <td>{{ $all->product_name }}</td>
                                    <td>{{ $all->size_name }}</td>
                                    <td style="width: 50px;"><input type="text" name="weights[{{ $all->id }}]"  placeholder="{{ '1 '.$all->size_name.' '.$all->product_name.' narxi' }}" value="0"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn add-age btn-primary text-white">saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection