﻿@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
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
</style>
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
                    <h5 class="modal-title" id="exampleModalLabel">Umumiy Hisobot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <select class="form-select" id="startdayid" name="start" aria-label="Default select example" required onchange="changeFunc();">
                                <option value="">-Sanadan-</option>
                                @foreach($days as $row)
                                    <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required onchange="changeFunc();">
                                <option value="">-Sanaga-</option>
                                @foreach($days as $row)
                                    <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="form-select" id="RegionSelect" name="region_id" aria-label="Default select example" required onchange="changeFunc();">
                                <option value="">-Tuman-</option>
                                @foreach($regions as $row)
                                    <option value="{{$row['id']}}">{{ $row['region_name']; }}</option>
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

    <div class="row g-3 my-2">
        <div class="col-md-6">
        </div>
        <div class="col-md-6">
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalsettings">Umumiy Hisobot</button>
        </div>
        @foreach($kinds as $item)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <a href="kindreport/{{ $item->id }}" class="list-group-item-action bg-transparent first-text fw-bold" class="fs-5" data-garden-id="{{ $item->id }}" style="color: #6ac3de;">{{$item->kingar_name}}</a>
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
    function changeFunc() {
        var selectBox = document.getElementById("startdayid");
        var start = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("enddayid");
        var end = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("RegionSelect");
        var region = selectBox.options[selectBox.selectedIndex].value;
        var div = $('.urlpdf');
        
        if(start != "" && end != "" && region != ""){
            var html = '<div class="report-section">';
            
            // Umumiy faktura
            html += '<div class="general-invoice">';
            html += '<i class="fas fa-file-invoice-dollar"></i> Умумий свод';
            html += '</div>';
            
            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Счёт фактура</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/schotfaktursecond/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/allschotfakturexcel/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';

            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Қатнов</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/transportationRegion/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/transportationRegionexcel/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';

            html += '<div class="report-category">';
            html += '<h6><i class="fas fa-file-invoice"></i>Боғчалар кесимида</h6>';
            html += '<div class="report-links">';
            html += '<a href="/accountant/reportregion/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link pdf">';
            html += '<i class="far fa-file-pdf"></i>PDF';
            html += '</a>';
            html += '<a href="/accountant/reportregionexcel/'+region+'/'+start+'/'+end+'" target="_blank" class="report-link excel">';
            html += '<i class="far fa-file-excel"></i>Excel';
            html += '</a>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            
            div.html(html);
        }
    }

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

</script>
@endsection
