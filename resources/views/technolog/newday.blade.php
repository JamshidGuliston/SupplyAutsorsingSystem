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
        text-align: center;
        vertical-align: middle;
        border-bottom-color: currentColor;
        border-right: 1px solid #c2b8b8;
    }
    
    .phone-icon {
        transition: all 0.3s ease;
    }
    
    .phone-icon:hover {
        transform: scale(1.2);
        color: #28a745 !important;
    }
    
    /* Envelope ikon uchun yonib o'chish animatsiyasi */
    .envelope-notification {
        animation: blink 2s infinite;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .envelope-notification:hover {
        animation: none;
        transform: scale(1.3);
        color: #ff0000 !important;
    }
    
    @keyframes blink {
        0%, 50% {
            opacity: 1;
            color: #c40c0c;
        }
        51%, 100% {
            opacity: 0.3;
            color: #ff6b6b;
        }
    }
    
    .chef-name {
        font-size: 12px;
        color: #6c757d;
        margin-left: 5px;
        font-style: italic;
    }
    
    /* Status icon container uchun */
    .status-icon-container {
        position: relative;
        display: inline-block;
        width: 100%;
        height: 100%;
    }
    
    .status-icon-container i {
        position: absolute;
        bottom: 2px;
        right: 2px;
        font-size: 12px;
        z-index: 10;
    }
    
    /* Table cell uchun relative positioning */
    .table td {
        position: relative;
    }
    
    .filter-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    .filter-section .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .filter-section .form-control,
    .filter-section .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
    }
    
    .filter-section .btn {
        border-radius: 6px;
        font-weight: 500;
    }
    
    /* Filter notification uchun */
    .filter-notification,
    .filter-notification1 {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Filter section yaxshilash */
    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .filter-section .form-label small {
        font-size: 11px;
        opacity: 0.7;
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

    .share-menu {
        transition: all 0.3s ease;
    }

    .share-menu:hover {
        transform: scale(1.2);
        color: #1e7e34 !important;
    }

    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .container-fluid {
        max-width: 1800px;
        margin: 0 auto;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    #secondTable {
        width: 100%;
        margin-bottom: 1rem;
        background-color: #fff;
        border-collapse: collapse;
    }

    #secondTable th,
    #secondTable td {
        padding: .75rem;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }

    #secondTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .mx-2 {
        margin-left: 1rem !important;
        margin-right: 1rem !important;
    }
    
    /* Notification stillar */
    .notification-panel .dropdown-menu {
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f8f9fa;
        transition: background-color 0.2s ease;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
    }
    
    .notification-item.unread {
        background-color: #e3f2fd;
        border-left: 3px solid #2196f3;
    }
    
    .notification-item .notification-content {
        font-size: 14px;
        line-height: 1.4;
    }
    
    .notification-item .notification-time {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }
    
    .notification-item .notification-actions {
        margin-top: 8px;
    }
    
    .notification-item .btn-sm {
        font-size: 11px;
        padding: 2px 8px;
    }
    
    .notification-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>
@endsection
@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
@if($sendmenu == 0)
<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editage" method="post">
		    @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-warning">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="menuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Keyingi kun menyusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                <select id="tommenu" class="form-control" required>
                    <option value="" selected>Bugungi menyu</option>
                    @foreach($menus as $menu)
                        <option data-menu-id="{{ $menu->id }}" value="{{ $menu->id }}">{{ $menu->menu_name }} - {{ $menu->season_name }}</option>
                    @endforeach
                </select>
                <br>
                <div class="hiddiv">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="addmenutom btn btn-success" data-bs-dismiss="modal">ok</button>
                <!-- <button type="submit" class="btn addmenutom btn-warning">Saqlash</button> -->
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->

<!-- DELETE Modal 1 - Birinchi jadval uchun -->
<!-- Modal -->
<div class="modal fade" id="deleteModal1" tabindex="-1" aria-labelledby="deleteModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/technolog/deletegarden" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="deleteModalLabel1">O'chirish tasdiqlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Rostdan ham bu bog'chani o'chirmoqchimisiz?</p>
                    <h5 class="garden-delete-name1 text-danger"></h5>
                    <input type="hidden" name="garden_id" class="delete-garden-id1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-danger">Ha, o'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- DELETE Modal 1 -->

<!-- DELETE Modal 2 - Ikkinchi jadval uchun -->
<!-- Modal -->
<div class="modal fade" id="deleteModal2" tabindex="-1" aria-labelledby="deleteModalLabel2" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/technolog/deletegarden" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="deleteModalLabel2">O'chirish tasdiqlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Rostdan ham bu bog'chani o'chirmoqchimisiz?</p>
                    <h5 class="garden-delete-name2 text-danger"></h5>
                    <input type="hidden" name="garden_id" class="delete-garden-id2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-danger">Ha, o'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- DELETE Modal 2 -->


<!-- EDD -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content loaders">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="kingarden">
                    <label for="basic-url" class="form-label">MTM nomi</label>
                    <select class="form-select" id="select-add" aria-label="Default select example">
                        <option selected>--</option>
                        @foreach($gardens as $gardenall)
                        @if(!isset($gardenall['ok']))
                        <option value="{{$gardenall['id']}}">{{$gardenall['kingar_name']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="yang-ages">

                </div>

            </div>
            <div class="loader-box">
                <div class="loader"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn add-age btn-info text-white">Qo'shish</button>
            </div>
        </div>
    </div>
</div>
<!-- EDD -->
<div class="py-4 px-4">
	<form action="/technolog/todaynextdaymenu" method="post">
		@csrf
		<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
            <div class="col-md-6">
                <div class="today">
                </div>
            </div>
            <div class="col-md-6">
                <b>Taxminiy menyu:</b>
                <div class="tomorrowmenu">
                </div><br>
                <div class="tomorrowmenufood">
                </div>
                <br>
                <input type="checkbox" required> Tasdiqlash
                <br><br>
                <input type="submit"  value="Yuborish">
            </div>
        </div>
		<br/>
    	<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
    	<a href="/technolog/home">Orqaga</a>
        <p>Bog'chalar soni: {{ count($temps) }}</p>
        <!--@if(count($temps) == count($activ))-->
        <!--<input type="submit"  class="yuborish btn btn-success text-white mb-2" value="Yuborish">-->
        <!--@endif-->
    </div>
    </form>
    <!-- Filter va qidiruv qismi -->
    <div class="row mb-3 filter-section">
        <div class="col-md-4">
            <label for="regionFilter" class="form-label">
                <i class="fas fa-filter me-1"></i>Hudud bo'yicha filter:
                <small class="text-muted d-block">Filter avtomatik saqlanadi</small>
            </label>
            <select class="form-select" id="regionFilter">
                <option value="">Barcha hududlar</option>
                @foreach(\App\Models\Region::all() as $region)
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="searchInput" class="form-label">
                <i class="fas fa-search me-1"></i>Qidiruv:
                <small class="text-muted d-block">Qidiruv avtomatik saqlanadi</small>
            </label>
            <input type="text" class="form-control" id="searchInput" placeholder="Bog'cha nomi yoki oshpaz nomi...">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-secondary me-2" id="clearFilters" title="Filterlarni tozalash va saqlangan ma'lumotlarni o'chirish">
                <i class="fas fa-trash-alt me-1"></i>Filterlarni tozalash
            </button>
            <button class="btn btn-info p-0" style="padding: 3px 16px !important;" data-bs-toggle="modal" data-bs-target="#exampleModalsadd">
                <i class="fas fa-plus-square text-white"></i>
            </button>
        </div>
    </div>

    <table class="table table-light py-4 px-4" id="firstTable">
        <thead>
            <tr>
                <th style="width: 14px;">
                    <input type="checkbox" id="select-all">
                </th>
                <th colspan="3">
                    
                </th>
                <th></th>
                <th>
                    
                </th>
	
                <th></th>
            </tr>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                <th scope="col">Xodimlar 
                    <!-- shu joyida ishchilar faqat 1 - idli menyudan ovqatlanadi -->
                    <input id="hiddenworkerage" type="hidden" name="workerage" value="4">
                    <!-- <select name="workerege" id="workerege" required>
                        <option value="">---</option>
                        @foreach($ages as $age)
                            <option data-menu-id="{{ $age->id }}" value="{{ $menu->id }}">{{ $age->age_name }}</option>
                        @endforeach
                    </select></th> -->
                @foreach($ages as $age)
                <th scope="col"> <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                    <i data-age-id="{{ $age->id }}" data-bs-toggle="modal" data-bs-target="#menuModal" class="addmenu agehide{{ $age->id }} fas fa-file-alt" style="cursor: pointer;"></i>
                </th>
                @endforeach
                <th style="width: 70px;">Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($temps as $temp)
            @php
                $kindgarden = \App\Models\Kindgarden::find($temp['id']);
                $user = $kindgarden ? $kindgarden->user->first() : null;
            @endphp
            <tr data-region-id="{{ $kindgarden ? $kindgarden->region_id : '' }}" data-user-name="{{ $user ? $user->name : '' }}">
                <th scope="row"><input type="checkbox" id="bike" name="vehicle" value="gentra"></th>
                <td>
                    {{ $temp['name'] }}
                    @php
                        $kindgarden = \App\Models\Kindgarden::find($temp['id']);
                        $user = $kindgarden ? $kindgarden->user->first() : null;
                    @endphp
                    @if($user && $user->phone)
                        <i class="fas fa-phone text-success phone-icon" style="cursor: pointer; margin-left: 8px;" 
                           data-bs-toggle="tooltip" data-bs-placement="top" 
                           title="Telefon raqam: {{ $user->phone }}'\n' Oshpaz: {{ $user->name }}"></i>
                    @endif
                </td>
                <td>{{ $temp['workers'] }}</td>
                @foreach($ages as $age)
                @if(isset($temp[$age->id]))
                <td>{{ $temp[$age->id] }}</td>
                @else
                <td><i class="far fa-window-close" style="color: red;"></i></td>
                @endif
                @endforeach
                <td>
                    <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="{{$temp['id']}}" style="cursor: pointer; margin-right: 16px;"></i>
                    <i class="deletegarden2 far fa-trash-alt text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal2" data-garden-id="{{$temp['id']}}" data-garden-name="{{$temp['name']}}" style="cursor: pointer;" title="O'chirish"></i>
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@else

<!-- EDD -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content loaders">
        <form action="/technolog/nextdayaddgarden" method="post">
                @csrf
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Qo'shish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="kingarden">
                    <label for="basic-url" class="form-label">MTM nomi</label>
                    <select class="form-select" id="selectadd" name="kgarden" aria-label="Default select example" required>
                        <option selected>--</option>
                        @foreach($gardens as $gardenall)
                        @if(!isset($gardenall['ok']))
                        <option value="{{$gardenall['id']}}">{{$gardenall['kingar_name']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="yangages">
                </div>
                <span>Ishchilar soni:</span>
                <div>
                    <input type="text" class = "form-control" name="workers" required>
                </div>
            </div>
            <div class="loader-box">
                <div class="loader"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-info text-white">Qo'shish</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- EDD -->
<!-- //////////////////////////////////////////////////////////////Taxminiy menular/////////////////////////////////////////////////////////// -->
<!-- Worker count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="wcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editnextworkers" method="post">
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
        <form action="/technolog/editnextcheldren" method="post">
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
        <form action="/technolog/editnextmenu" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menyuni o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="menutitle"></h5>
                <div class="menu_select">
                    <select name="menuid" class="form-select" required>
                        <option value="">Menyu tanlang</option>
                        @foreach($allmenus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->menu_name }} - {{ $menu->season_name }}</option>
                        @endforeach
                    </select>
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
<!-- all Menu edit -->
<div class="modal editesmodal fade" id="editnextmenuModalAll" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/update-bulk-age-menu" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Barcha menyularni o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="menutitle"></h5>
                <div class="menu_select">
                    <input type="hidden" name="age_id" id="bulk_age_id">
                    <select name="menu_id" class="form-select" required>
                        <option value="">Menyu tanlang</option>
                        @foreach($allmenus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->menu_name }} - {{ $menu->season_name }}</option>
                        @endforeach
                    </select>
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
<!-- Edit number of employees -->
<!-- Modal -->
<div class="modal fade" id="editModalForEmployees" tabindex="-1" aria-labelledby="editModalForEmployeesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('technolog.editnextallworkers') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalForEmployeesLabel">Xodimlar sonini o'zgartirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="employees-title">Oxirgi ish kunidagi xodimlar soni olib o'zgartirish</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Bajarish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->

<!-- Copy children numbers modal -->
<div class="modal fade" id="copyChildrenModal" tabindex="-1" aria-labelledby="copyChildrenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('technolog.copyChildrenNumbers') }}" method="post">
                @csrf
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="copyChildrenModalLabel">Bolalar sonini nusxalash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="age-title text-primary"></h6>
                            <input type="hidden" name="age_id" id="copy_age_id">
                        </div>
                        <div class="col-md-6">
                            <label for="daySelect" class="form-label">Kunni tanlang:</label>
                            <select class="form-select" id="daySelect" name="day_id" required>
                                <option value="">Kunni tanlang</option>
                                @foreach(\App\Models\Day::join('months', 'months.id', '=', 'days.month_id')
                                    ->join('years', 'years.id', '=', 'days.year_id')
                                    ->orderBy('days.id', 'DESC')
                                    ->limit(30)
                                    ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']) as $day)
                                    <option value="{{ $day->id }}">{{ $day->day_number }}.{{ $day->month_name }}.{{ $day->year_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tanlangan kundagi bolalar sonlari keyingi kun uchun nusxalanadi. 
                            Mavjud ma'lumotlar vaqtincha saqlanadi va kerak bo'lsa qayta tiklanadi.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-info text-white">Nusxalash</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Copy children numbers modal -->

<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="historyModalLabel">Bolalar soni o'zgartirish tarixi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="history-info mb-3">
                    <h6 class="history-garden-name text-primary"></h6>
                    <h6 class="history-age-name text-info"></h6>
                </div>
                <div class="history-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yuklanmoqda...</span>
                        </div>
                        <p class="mt-2">Tarix yuklanmoqda...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
            </div>
        </div>
    </div>
</div>
<!-- History Modal -->

<!-- DELETE Modal 2 - Birinchi jadval uchun -->
<!-- Modal -->
<div class="modal fade" id="deleteModal2" tabindex="-1" aria-labelledby="deleteModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/technolog/deletegarden" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="deleteModalLabel1">O'chirish tasdiqlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Rostdan ham bu bog'chani o'chirmoqchimisiz?</p>
                    <h5 class="garden-delete-name2 text-danger"></h5>
                    <input type="hidden" name="garden_id" class="delete-garden-id2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-danger">Ha, o'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- DELETE Modal 2 -->

<div class="py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <b>Taxminiy menyular</b>
        </div>
        <div class="col-md-6 text-end">
            <!-- Notification panel -->
            <div class="notification-panel d-inline-block">
                <button class="btn btn-outline-primary position-relative" id="notificationBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none;">
                        0
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>Xabarlar</span>
                        <button class="btn btn-sm btn-outline-secondary" id="markAllReadBtn" style="display: none;">
                            <i class="fas fa-check-double"></i> Barchasini o'qilgan deb belgilash
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li id="notificationList">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Yuklanmoqda...</span>
                            </div>
                            <p class="mt-2 mb-0">Xabarlar yuklanmoqda...</p>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- <a href="{{ route('technolog.downloadAllKindergartensMenusPDF') }}" class="btn btn-success" title="Barcha bog'chalar uchun alohida PDF fayllarini ZIP arxiv qilish">
                <i class="fas fa-download me-1"></i>Barcha menyularni ZIP arxiv qilish
            </a> -->
        </div>
    </div>
        <div class="col-md-3">
            
        </div>
        <div class="col-md-3">
        
        </div>
    </div>
    <hr>
    <!-- Filter va qidiruv qismi -->
    <div class="container-fluid">
        <div class="row mb-3 filter-section mx-2">
            <div class="col-md-3">
                <label for="regionFilter2" class="form-label">
                    <i class="fas fa-filter me-1"></i>Hudud bo'yicha filter:
                    <small class="text-muted d-block">Filter avtomatik saqlanadi</small>
                </label>
                <select class="form-select" id="regionFilter2">
                    <option value="">Barcha hududlar</option>
                    @foreach(\App\Models\Region::all() as $region)
                        <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="searchInput2" class="form-label">
                    <i class="fas fa-search me-1"></i>Qidiruv:
                    <small class="text-muted d-block">Qidiruv avtomatik saqlanadi</small>
                </label>
                <input type="text" class="form-control" id="searchInput2" placeholder="Bog'cha nomi yoki oshpaz nomi...">
            </div>
            <div class="col-md-2">
                <label for="clearFilters2" class="form-label">Filterlarni tozalash</label><br>
                <button class="btn btn-secondary me-2" id="clearFilters2" title="Filterlarni tozalash va saqlangan ma'lumotlarni o'chirish"> 
                    <i class="fas fa-trash-alt text-white"></i>
                </button>
            </div>
            <div class="col-md-2">
                <label for="downloadAllMenus" class="form-label">Menyular ZIP</label><br>
                <button class="btn btn-success me-2" id="downloadAllMenus" title="Barcha bog'cha menyularini ZIP arxiv qilish">
                    <i class="fas fa-file-archive text-white"></i>
                </button>
            </div>
            <div class="col-md-2">
                <label for="exampleModalsadd" class="form-label">Bog'cha qo'shish</label><br>
                <button class="btn btn-info" style="text-align:end" data-bs-toggle="modal" data-bs-target="#exampleModalsadd"> <i class="fas fa-plus-square text-white "></i></button>
            </div>      
        </div>
    </div>

    <div class="container-fluid">
        <div class="table-responsive mx-2">
            <table class="table table-light table-bordered table-hover" id="secondTable">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2">ID</th>
                        <th scope="col" rowspan="2">MTT-nomi</th>
                        <th scope="col" rowspan="2">Xodimlar<br>
                            <i class="fas fa-users"></i>
                            <i class="next_allmenu fas fa-edit" style="color: #727213; font-size: 14px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editModalForEmployees"></i>
                        </th>
                        @foreach($ages as $age)
                        <th scope="col" colspan="2"> 
                            <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                            <i class="fas fa-edit copy-children-btn" 
                               style="color: #727213; font-size: 14px; cursor: pointer; margin-left: 5px;" 
                               data-age-id="{{ $age->id }}" 
                               data-age-name="{{ $age->age_name }}"
                               data-bs-toggle="modal" 
                               data-bs-target="#copyChildrenModal" 
                               title="Bolalar sonini nusxalash"></i>
                            <i class="fas fa-undo restore-children-btn" 
                               style="color: #dc3545; font-size: 14px; cursor: pointer; margin-left: 3px;" 
                               data-age-id="{{ $age->id }}" 
                               data-age-name="{{ $age->age_name }}"
                               title="Ma'lumotlarni qayta tiklash"></i>
                        </th>
                        @endforeach
                        <th style="width: 70px;" rowspan="2">Накладной</th>
                        <th style="width: 70px;" rowspan="2">Amallar</th>
                    </tr>
                    <tr style="color: #888888;">
                        @foreach($ages as $age)
                        <th><i class="fas fa-users"></i></th>
                        <th>
                            <i class="fas fa-book-open"></i>
                            <i class="next_allmenu fas fa-edit" style="color: #727213; font-size: 14px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editnextmenuModalAll" data-age-id="{{ $age->id }}"></i>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $t = 1;

                    // dd($nextdayitem);
                ?>   
                @foreach($nextdayitem as $row)
                    @php
                        $kindgarden = \App\Models\Kindgarden::find($row['kingar_name_id']);
                        $user = $kindgarden ? $kindgarden->user->first() : null;
                    @endphp
                    <tr data-region-id="{{ $kindgarden ? $kindgarden->region_id : '' }}" data-user-name="{{ $user ? $user->name : '' }}">
                        <td>{{ $t++ }}</td>
                        <td>
                            {{ $row['kingar_name'] }}
                            @php
                                $kindgarden = \App\Models\Kindgarden::find($row['kingar_name_id']);
                                $user = $kindgarden ? $kindgarden->user->first() : null;
                            @endphp
                            @if($user && $user->phone)
                                <i class="fas fa-phone text-success phone-icon" style="cursor: pointer; margin-left: 8px;" 
                                   data-bs-toggle="tooltip" data-bs-placement="top" 
                                   title="Oshpaz: {{ $user->name }}         Telefon raqam: {{ $user->phone }} "></i>
                            @endif
                        </td>
                        <td>{{ $row['workers_count'] }} <i class="w_countedit far fa-edit" data-menu-id="{{ $row['kingar_name_id'] }}" data-wor-count="{{ $row['workers_count'] }}" data-king-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#wcountModal" style="color: #727213; font-size: 14px; cursor: pointer;"></i></td>
                        @foreach($ages as $age)
                        @if(isset($row[$age->id]))
                            @php
                                // Bugungi kun uchun bolalar soni o'zgartirish tarixini tekshirish
                                $todayHistory = $childrenCountHistory->where('kingar_name_id', $row['kingar_name_id'])
                                    ->where('king_age_name_id', $age->id)
                                    ->where('created_at', '>=', date('Y-m-d 00:00:00'))
                                    ->where('created_at', '<=', date('Y-m-d 23:59:59'))
                                    ->first();
                                
                                // Default holat - o'zgartirish yo'q
                                $status = '#ffffca'; // Sariq rang
                                $status_icon = "<i class='fas fa-question' style='color:rgb(238, 65, 65); font-size: 14px; cursor: pointer;' title='Bugungi kun uchun o'zgartirish yo'q'></i>";
                                
                                // Agar bugungi kun uchun o'zgartirish mavjud bo'lsa
                                if($todayHistory) {
                                    $status = '#bfffbf'; // Yashil rang
                                    $status_icon = "<i class='fas fa-check' style='color:rgb(18, 141, 13); font-size: 14px; cursor: pointer;' title='Bugungi kun uchun o'zgartirish mavjud'></i>";
                                }
                            @endphp
                            <td style="background-color: {{ $status }};">
                              {{ $row[$age->id][1]."  " }}
                               @if($row[$age->id][2] != null and isset($todayHistory) and $todayHistory->new_children_count != $row[$age->id][1])
                                <i class="far fa-envelope envelope-notification" title="Yangi xabarnoma mavjud!"></i> 
                               @endif
                               <i class="ch_countedit far fa-edit" data-nextrow-id="{{ $row[$age->id][0]; }}" data-child-count="{{ $row[$age->id][1]; }}" data-temprow-id="{{ $row[$age->id][2]; }}" data-tempchild-count="{{ $row[$age->id][3]; }}" data-kinga-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#chcountModal" style="color: #727213; font-size: 14px; cursor: pointer;"></i>
                               <i class="history-btn fas fa-history" 
                                  data-garden-id="{{ $row['kingar_name_id'] }}" 
                                  data-age-id="{{ $age->id }}" 
                                  data-garden-name="{{ $row['kingar_name'] }}" 
                                  data-age-name="{{ $age->age_name }}"
                                  data-bs-toggle="modal" 
                                  data-bs-target="#historyModal" 
                                  style="color: #2196f3; font-size: 14px; cursor: pointer; margin-left: 5px;" 
                                  title="O'zgartirish tarixini ko'rish"></i>
                               <div class="status-icon-container">
                                   {!! $status_icon !!}
                               </div>
                            </td>
                            <td>
                                <p>{{ $row[$age->id][5] }}</p>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="/nextdaymenuPDF/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank" title="PDF ko'rish">
                                        <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
                                    </a>
                                    <a href="#" class="share-menu" style="text-decoration: none;"
                                       data-garden-id="{{ $row['kingar_name_id'] }}"
                                       data-age-id="{{ $age->id }}"
                                       data-garden-name="{{ $row['kingar_name'] }}"
                                       data-age-name="{{ $age->age_name }}"
                                       title="Telegramga yuborish">
                                        <i class="fas fa-share-alt" style="color: #28a745; font-size: 18px; cursor: pointer;"></i>
                                    </a>
                                    <i class="next_menu far fa-edit" 
                                       data-nextmenu-id="{{ $row[$age->id][4]; }}" 
                                       data-nextrow-count="{{ $row[$age->id][0]; }}" 
                                       data-king-name="{{ $row['kingar_name'] }}" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#editnextmenuModal" 
                                       style="color: #727213; font-size: 14px; cursor: pointer;"></i>
                                </div>
                            </td>
                        @else
                            <td>{{ ' ' }}</td>
                            <td>{{ ' ' }}</td>
                        @endif
                        @endforeach
                        <td><a href="/nextnakladnoyPDF/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
                        <td>
                            <i class="deletegarden2 far fa-trash-alt text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal2" data-garden-id="{{$row['kingar_name_id']}}" data-garden-name="{{$row['kingar_name']}}" style="cursor: pointer;" title="O'chirish"></i>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <?php $tr = 1 ?>
    
    <!-- Shopslar uchun alohida jadval -->
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h5 class="text-primary">
                    <i class="fas fa-store me-2"></i>Yetkazuvchilar
                </h5>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-light table-bordered table-striped table-hover">
                <thead class="table-primary">
                    <tr>    
                        <th scope="col" style="width: 50px;">№</th>
                        <th scope="col">Yetkazuvchi nomi</th>
                        <th scope="col" style="width: 120px;">Zayavkani saqlab qo'yish</th>
                        <th scope="col" style="width: 100px;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shops as $shop)
                    <tr>
                        <td>{{ $tr++ }}</td>
                        <td>
                            <strong>{{ $shop->shop_name }}</strong>
                        </td>
                        <td class="text-center">
                            @if(isset($shopOrderStatus[$shop->id]) && $shopOrderStatus[$shop->id])
                                <!-- Zayavka saqlangan bo'lsa -->
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Saqlangan
                                </span>
                            @else
                                <!-- Zayavka saqlanmagan bo'lsa -->
                                <button class="btn btn-outline-success btn-sm save-request-btn" 
                                        data-shop-id="{{ $shop->id }}" 
                                        data-shop-name="{{ $shop->shop_name }}"
                                        title="Zayavkani saqlab qo'yish">
                                    <i class="fas fa-save"></i>
                                </button>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="/technolog/nextdelivershop/{{ $shop->id }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm"
                               title="Yetkazib berish jadvalini ko'rish">
                                <i class="fas fa-shipping-fast"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@section('script')
@if($sendmenu == 0)
<script>
    // Tooltip funksionalligini ishga tushirish
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.getElementsByName('vehicle');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
    $(document).ready(function() {
        // Filter va qidiruv funksionalligi
        function filterTable() {
            var regionFilter = $('#regionFilter').val();
            var searchText = $('#searchInput').val().toLowerCase();
            var table = $('#firstTable tbody tr');
            
            table.each(function() {
                var row = $(this);
                var gardenName = row.find('td:eq(1)').text().toLowerCase();
                var regionId = row.attr('data-region-id');
                var user = row.attr('data-user-name');
                
                var showByRegion = !regionFilter || regionId == regionFilter;
                var showBySearch = !searchText || 
                    gardenName.includes(searchText) || 
                    (user && user.toLowerCase().includes(searchText));
                
                if (showByRegion && showBySearch) {
                    row.show();
                } else {
                    row.hide();
                }
            });
            
            // Filter qiymatlarini localStorage ga saqlash
            localStorage.setItem('newday_regionFilter1', regionFilter);
            localStorage.setItem('newday_searchInput1', searchText);
        }
        
        // Sahifa yuklanganda filter qiymatlarini tiklash
        function restoreFilters1() {
            var savedRegionFilter = localStorage.getItem('newday_regionFilter1');
            var savedSearchText = localStorage.getItem('newday_searchInput1');
            
            if (savedRegionFilter) {
                $('#regionFilter').val(savedRegionFilter);
            }
            if (savedSearchText) {
                $('#searchInput').val(savedSearchText);
            }
            
            // Agar filter qiymatlari mavjud bo'lsa, jadvalni filterlash
            if (savedRegionFilter || savedSearchText) {
                filterTable();
            }
        }
        
        // Sahifa yuklanganda filterlarni tiklash
        restoreFilters1();
        
        // Filter va qidiruv eventlari
        $('#regionFilter, #searchInput').on('change keyup', filterTable);
        
        // Filterlarni tozalash
        $('#clearFilters').click(function() {
            $('#regionFilter').val('');
            $('#searchInput').val('');
            
            // localStorage dan ham o'chirish
            localStorage.removeItem('newday_regionFilter1');
            localStorage.removeItem('newday_searchInput1');
            
            filterTable();
            
            // Tozalash haqida xabar berish
            var clearMessage = 'Filterlar muvaffaqiyatli tozalandi!';
            showNotification1(clearMessage, 'success');
        });
        
        // Xabar ko'rsatish funksiyasi - birinchi jadval uchun
        function showNotification1(message, type) {
            // Mavjud xabarni o'chirish
            $('.filter-notification1').remove();
            
            var alertClass = type === 'success' ? 'alert-success' : 'alert-info';
            var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
            
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show filter-notification1" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="' + icon + '" style="margin-right: 8px;"></i>' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            $('body').append(notification);
            
            // 3 soniyadan keyin xabarni yashirish
            setTimeout(function() {
                notification.fadeOut();
            }, 3000);
        }
        
        var menuinp = $('.menucounts');
        if(menuinp.length == ''){
            var button = document.getElementsByClassName("yuborish");
            for(var i = 0; i < button.length; i++){
                button[i].style.display = "none"; // depending on what you're doing
            }
        }

        // const target = document.querySelector('#deleteModal1');
        // if (target) {
        //     new bootstrap.Modal(target).show();
        // }
        // O'chirish tugmasi - Birinchi jadval uchun
        $('.deletegarden1').click(function() {
            alert('deletegarden1');
            var gardenId = $(this).attr('data-garden-id');
            var gardenName = $(this).attr('data-garden-name');
            $('.delete-garden-id1').val(gardenId);
            $('.garden-delete-name1').text(gardenName);
        });
        $('#select-add').change(function() {
            g = $(this).val();
            h = $('.yang-ages');
            $.ajax({
                method: "GET",
                url: '/technolog/gageranges/' + g,
                beforeSend: function() {
                    $('.loader-box').show();
                },
                success: function(data) {
                    h.html(data);
                    $('.loader-box').hide();
                }
            })
        });

        $('.addmenu').click(function() {
            var k = $(this).attr('data-age-id');
            var div = $('.hiddiv');
            div.html("<input type='hidden' name='ageid' class='ageid' value="+k+">");
        });

        $('.addmenutom').click(function() {
            var menuid = $("#tommenu").val();
            document.getElementById('tommenu').getElementsByTagName('option')[0].selected = 'selected';
            var wage = $('#hiddenworkerage').val();
            var divtom = $('.tomorrowmenufood');
            var agename = $('.age_name'+wage).text();
            var checkedValue = null; 
            var inputElements = document.getElementsByClassName('checkfood');
            if(inputElements.length>0){
                divtom.append("<b>Xodimlar ovqati "+agename+" guruh menusidan: </b>");
            }
            for(var i=0; inputElements[i]; ++i){
                if(inputElements[i].checked){
                    var fodname = $('#worfood'+inputElements[i].value).text();
                    divtom.append("<input type='hidden' class='foodcounts' name='dmf[]' value="+wage+"_"+menuid+"_"+inputElements[i].value+"> "+fodname+", ");
                }
            }
            var menuinp = $('.menucounts');
            var foodinp = $('.foodcounts');
            if(menuinp.length < 3 || foodinp.length == ''){
                var button = document.getElementsByClassName("yuborish");
                for(var i = 0; i < button.length; i++){
                    button[i].style.display = "none"; // depending on what you're doing
                }
            }
            else{
                var button = document.getElementsByClassName("yuborish");
                for(var i = 0; i < button.length; i++){
                    button[i].style.display = "block"; // depending on what you're doing
                }
            }
        });

        $('.add-age').click(function() {
            var inp = $('.ageranges');
            inp.each(function() {
                var j = $(this).attr('data-id');
                var g = $(this).attr('gar-id');
                var valuess = $(this).val();
                $.ajax({
                    method: 'GET',
                    url: '/technolog/addage/' + g + '/' + j + '/' + valuess,
                    success: function(data) {
                        location.reload();
                    }
                })
            })
        })

        var edite = $('.edites');
        edite.click(function() {
            var ll = $(this).attr('data-kinid');
            $.ajax({
                method: 'GET',
                url: '/technolog/getage/' + ll,
                success: function(data) {
                    var modaledite = $('.editesmodal .modal-body');
                    modaledite.html(data);
                },
            })
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
    $('#tommenu').change(function() {
        var menuid = $("#tommenu option:selected").val();
        var menutext = $("#tommenu option:selected").text();
        var age = $('.ageid').val();
        var wage = $('#hiddenworkerage').val();
        var div = $('.hiddiv');
        var agename = $('.age_name'+age).text();
        var divtom = $('.tomorrowmenu');
        var icon = $(".agehide"+age).hide();
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodnametoday',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                divtom.append("<input type='hidden' class='menucounts' name='mid[]' value="+age+"_"+menuid+"><b>"+agename+":</b> "+menutext+";    ");
                if(age == wage){
                    div.append(data);
                }
            }
        })
    });
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
        if(tempchild != null){
            div2.html("<br><p style='color: red'>Xabarnoma: <i class='far fa-envelope' style='color: #c40c0c'></i> "+tempchild+"</p>");
        }
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
        

        // Envelope ikon uchun qo'shimcha funksionallik - ikkinchi jadval
        $('.envelope-notification').click(function() {
            // Xabarnoma ko'rsatish
            var notificationText = $(this).attr('title');
            alert(notificationText);
            
            // Ikonni to'xtatish (animatsiyani o'chirish)
            $(this).removeClass('envelope-notification').addClass('envelope-read');
            $(this).css({
                'animation': 'none',
                'color': '#666',
                'opacity': '0.7'
            });
        });
        
        // Envelope ikon uchun qo'shimcha funksionallik
        $('.envelope-notification').click(function() {
            // Xabarnoma ko'rsatish
            var notificationText = $(this).attr('title');
            alert(notificationText);
            
            // Ikonni to'xtatish (animatsiyani o'chirish)
            $(this).removeClass('envelope-notification').addClass('envelope-read');
            $(this).css({
                'animation': 'none',
                'color': '#666',
                'opacity': '0.7'
            });
        });
</script>
@else
<script>
    $(document).ready(function() {
        // Tooltip funksionalligini ishga tushirish
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Filter va qidiruv funksionalligi - ikkinchi jadval
        function filterTable2() {
            var regionFilter = $('#regionFilter2').val();
            var searchText = $('#searchInput2').val().toLowerCase();
            var table = $('#secondTable tbody tr');
            
            table.each(function() {
                var row = $(this);
                var gardenName = row.find('td:eq(1)').text().toLowerCase();
                var regionId = row.attr('data-region-id');
                var user = row.attr('data-user-name');
                
                var showByRegion = !regionFilter || regionId == regionFilter;
                var showBySearch = !searchText || 
                    gardenName.includes(searchText) || 
                    (user && user.toLowerCase().includes(searchText));
                
                if (showByRegion && showBySearch) {
                    row.show();
                } else {
                    row.hide();
                }
            });
            
            // Filter qiymatlarini localStorage ga saqlash
            localStorage.setItem('newday_regionFilter2', regionFilter);
            localStorage.setItem('newday_searchInput2', searchText);
        }
        
        // Sahifa yuklanganda filter qiymatlarini tiklash
        function restoreFilters() {
            var savedRegionFilter = localStorage.getItem('newday_regionFilter2');
            var savedSearchText = localStorage.getItem('newday_searchInput2');
            
            if (savedRegionFilter) {
                $('#regionFilter2').val(savedRegionFilter);
            }
            if (savedSearchText) {
                $('#searchInput2').val(savedSearchText);
            }
            
            // Agar filter qiymatlari mavjud bo'lsa, jadvalni filterlash
            if (savedRegionFilter || savedSearchText) {
                filterTable2();
            }
        }
        
        // Sahifa yuklanganda filterlarni tiklash
        restoreFilters();
        
        // Filter va qidiruv eventlari - ikkinchi jadval
        $('#regionFilter2, #searchInput2').on('change keyup', filterTable2);
        
        // Filterlarni tozalash - ikkinchi jadval
        $('#clearFilters2').click(function() {
            $('#regionFilter2').val('');
            $('#searchInput2').val('');
            
            // localStorage dan ham o'chirish
            localStorage.removeItem('newday_regionFilter2');
            localStorage.removeItem('newday_searchInput2');
            
            filterTable2();
            
            // Tozalash haqida xabar berish
            var clearMessage = 'Filterlar muvaffaqiyatli tozalandi!';
            showNotification(clearMessage, 'success');
        });
        
        // Xabar ko'rsatish funksiyasi
        function showNotification(message, type) {
            // Mavjud xabarni o'chirish
            $('.filter-notification').remove();
            
            var alertClass = type === 'success' ? 'alert-success' : 'alert-info';
            var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';
            
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show filter-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="' + icon + '" style="margin-right: 8px;"></i>' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            $('body').append(notification);
            
            // 3 soniyadan keyin xabarni yashirish
            setTimeout(function() {
                notification.fadeOut();
            }, 3000);
        }

        $('.deletegarden2').click(function() {
            var gardenId = $(this).attr('data-garden-id');
            var gardenName = $(this).attr('data-garden-name');
            $('.delete-garden-id2').val(gardenId);
            $('.garden-delete-name2').text(gardenName);
        });
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
            if (parseInt(tempchild) > 0) {  
                div2.html("<br><p style='color: red'>Xabarnoma: <i class='far fa-envelope' style='color: #c40c0c'></i> "+tempchild+"</p>");
            }
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
        
        $('.next_allmenu').click(function() {
            var ageId = $(this).attr('data-age-id');
            $('#bulk_age_id').val(ageId);
            var ageName = $('.age_name' + ageId).text();
            $('.menutitle').html("<p>" + ageName + " yosh guruhi uchun barcha menyularni o'zgartirish</p>");
        });

        // Copy children numbers modal
        $('.copy-children-btn').click(function() {
            var ageId = $(this).attr('data-age-id');
            var ageName = $(this).attr('data-age-name');
            
            $('#copy_age_id').val(ageId);
            $('.age-title').text(ageName + ' yosh guruhi uchun bolalar sonini nusxalash');
        });

        // History modal
        $('.history-btn').click(function() {
            var gardenId = $(this).attr('data-garden-id');
            var ageId = $(this).attr('data-age-id');
            var gardenName = $(this).attr('data-garden-name');
            var ageName = $(this).attr('data-age-name');
            
            // Modal sarlavhasini yangilash
            $('.history-garden-name').text('Bog\'cha: ' + gardenName);
            $('.history-age-name').text('Yosh guruhi: ' + ageName);
            
            // Loading ko'rsatish
            $('.history-content').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yuklanmoqda...</span>
                    </div>
                    <p class="mt-2">Tarix yuklanmoqda...</p>
                </div>
            `);
            
            // AJAX orqali tarixni olish
            $.ajax({
                url: '/technolog/children-count-history/' + gardenId + '/' + ageId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.history.length > 0) {
                        var historyHtml = '<div class="table-responsive"><table class="table table-striped table-hover">';
                        historyHtml += '<thead class="table-primary"><tr><th>Eski son</th><th>Yangi son</th><th>O\'zgartirgan</th><th>Vaqt</th><th>Sabab</th></tr></thead><tbody>';
                        
                        response.history.forEach(function(record) {
                            historyHtml += '<tr>';
                            historyHtml += '<td>' + (record.old_children_count || 'N/A') + '</td>';
                            historyHtml += '<td><strong>' + record.new_children_count + '</strong></td>';
                            historyHtml += '<td>' + record.changed_by_name + '</td>';
                            historyHtml += '<td>' + record.changed_at_formatted + '</td>';
                            historyHtml += '<td>' + (record.change_reason || '-') + '</td>';
                            historyHtml += '</tr>';
                        });
                        
                        historyHtml += '</tbody></table></div>';
                        $('.history-content').html(historyHtml);
                    } else {
                        $('.history-content').html(`
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Hech qanday o'zgartirish tarixi yo'q</h5>
                                <p class="text-muted">Bu bog'cha va yosh guruhi uchun hali hech qanday o'zgartirish kiritilmagan</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('.history-content').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Tarixni yuklashda xatolik yuz berdi. Iltimos, qaytadan urinib ko'ring.
                        </div>
                    `);
                }
            });
        });

        // Restore children numbers
        $('.restore-children-btn').click(function() {
            var ageId = $(this).attr('data-age-id');
            var ageName = $(this).attr('data-age-name');
            
            if (!confirm(ageName + ' yosh guruhi uchun ma\'lumotlarni qayta tiklamoqchimisiz?')) {
                return;
            }
            
            var icon = $(this);
            icon.removeClass('fa-undo').addClass('fa-spinner fa-spin');
            
            $.ajax({
                url: '{{ route("technolog.restoreChildrenNumbers") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    age_id: ageId
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        // Sahifani qayta yuklash
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Xatolik yuz berdi!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification(errorMessage, 'error');
                },
                complete: function() {
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-undo');
                }
            });
        });

        // Share menu funksiyasi
        $('.share-menu').click(function(e) {
            e.preventDefault();
            var gardenId = $(this).data('garden-id');
            var ageId = $(this).data('age-id');
            var gardenName = $(this).data('garden-name');
            var ageName = $(this).data('age-name');
            
            var icon = $(this).find('i');
            icon.removeClass('fa-share-alt').addClass('fa-spinner fa-spin');
            
            // Bot orqali Telegram guruhiga yuborish
            var groupId = "{{ config('services.telegram.group_id') }}";
            var url = '/technolog/share-menu-telegram/' + gardenId + '/' + ageId;
            if (groupId) {
                url += '?group_id=' + encodeURIComponent(groupId);
            }
            
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        showNotification('Menyu Telegram guruhiga yuborildi!', 'success');
                    } else {
                        showNotification(response.message || 'Yuborishda xatolik.', 'error');
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.';
                    showNotification(msg, 'error');
                },
                complete: function() {
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-share-alt');
                }
            });
        });

        $('#selectadd').change(function() {
            g = $(this).val();
            hn = $('.yangages');
            $.ajax({
                method: "GET",
                url: '/technolog/ageranges/' + g,
                beforeSend: function() {
                    $('.loader-box').show();
                },
                success: function(data) {
                    hn.html(data);
                    $('.loader-box').hide();
                }
            })
        });

        // Share menu funksiyasi
        // $('.share-menu').click(function() {
        //     var gardenId = $(this).data('garden-id');
        //     var ageId = $(this).data('age-id');
        //     var gardenName = $(this).data('garden-name');
        //     var ageName = $(this).data('age-name');
            
        //     if (!confirm(gardenName + ' - ' + ageName + ' yosh guruhi menyusini telegramga yuborishni xohlaysizmi?')) {
        //         return;
        //     }
            
        //     var icon = $(this);
        //     icon.removeClass('fa-share-alt').addClass('fa-spinner fa-spin');
            
        //     $.ajax({
        //         url: '/technolog/share-menu-telegram/' + gardenId + '/' + ageId,
        //         method: 'GET',
        //         success: function(response) {
        //             if (response.success) {
        //                 showNotification(response.message, 'success');
        //             } else {
        //                 showNotification(response.message, 'error');
        //             }
        //         },
        //         error: function(xhr) {
        //             showNotification('Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.', 'error');
        //         },
        //         complete: function() {
        //             icon.removeClass('fa-spinner fa-spin').addClass('fa-share-alt');
        //         }
        //     });
        // });
        
    });

    // newday.blade.php ning script qismiga qo'shish kerak
    $(document).ready(function() {
        // Mavjud JavaScript kodlar...
        
        // Barcha menyularni ZIP arxiv qilish
        $('#downloadAllMenus').click(function() {
            // Region tanlanganligini tekshirish
            var selectedRegion = $('#regionFilter2').val();
            
            if (!selectedRegion) {
                showNotification('Iltimos, avval hududni tanlang!', 'error');
                return;
            }
            
            // Loading ko'rsatish
            $(this).html('<i class="fas fa-spinner fa-spin text-white"></i> Yuklanmoqda...');
            $(this).prop('disabled', true);
            
            // AJAX so'rov
            $.ajax({
                url: '{{ route("technolog.downloadAllKindergartensMenusPDF") }}',
                method: 'GET',
                data: {
                    region_id: selectedRegion
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    // Faylni yuklab olish
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    
                    // Fayl nomini olish
                    var contentDisposition = xhr.getResponseHeader('Content-Disposition');
                    var fileName = 'barcha_menyular_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.zip';
                    
                    if (contentDisposition) {
                        var fileNameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (fileNameMatch) {
                            fileName = fileNameMatch[1];
                        }
                    }
                    
                    link.download = fileName;
                    link.click();
                    
                    // Muvaffaqiyat xabari
                    showNotification('ZIP fayl muvaffaqiyatli yuklab olindi!', 'success');
                    
                    // Tugmani qayta tiklash
                    $('#downloadAllMenus').html('<i class="fas fa-file-archive text-white"></i>');
                    $('#downloadAllMenus').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Xatolik yuz berdi!';
                    
                    // JSON xatolik xabarini olish
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 400) {
                        errorMessage = 'Iltimos, hududni tanlang!';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Tanlangan hududda hech qanday bog\'cha topilmadi!';
                    }
                    
                    showNotification(errorMessage, 'error');
                    
                    // Tugmani qayta tiklash
                    $('#downloadAllMenus').html('<i class="fas fa-file-archive text-white"></i>');
                    $('#downloadAllMenus').prop('disabled', false);
                }
            });
        });
        
        // Xabar ko'rsatish funksiyasi (error turi uchun)
        function showNotification(message, type) {
            // Mavjud xabarni o'chirish
            $('.filter-notification').remove();
            
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show filter-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="' + icon + '" style="margin-right: 8px;"></i>' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            $('body').append(notification);
            
            // 5 soniyadan keyin xabarni yashirish
            setTimeout(function() {
                notification.fadeOut();
            }, 5000);
        }

        // Zayavkani saqlab qo'yish funksiyasi
        $('.save-request-btn').click(function() {
            var shopId = $(this).data('shop-id');
            var shopName = $(this).data('shop-name');
            
            if (!confirm(shopName + ' uchun zayavkani saqlab qo\'ymoqchimisiz?')) {
                return;
            }
            
            var btn = $(this);
            var icon = btn.find('i');
            
            // Loading holatini ko'rsatish
            icon.removeClass('fa-save').addClass('fa-spinner fa-spin');
            btn.prop('disabled', true);
            
            $.ajax({
                url: '/technolog/createShopOrder',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    shop_id: shopId
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Zayavka muvaffaqiyatli saqlandi!', 'success');
                        
                        // Tugmani muvaffaqiyatli holatga o'tkazish
                        btn.removeClass('btn-outline-success').addClass('btn-success');
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-check');
                        
                        // 2 soniyadan keyin asl holatga qaytarish
                        setTimeout(function() {
                            btn.removeClass('btn-success').addClass('btn-outline-success');
                            icon.removeClass('fa-check').addClass('fa-save');
                            btn.prop('disabled', false);
                        }, 2000);
                        // reload page
                        location.reload();
                    } else {
                        showNotification(response.message || 'Xatolik yuz berdi!', 'error');
                        icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Xatolik yuz berdi!';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showNotification(errorMessage, 'error');
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-save');
                    btn.prop('disabled', false);
                }
            });
        });
        
        // Notification funksionalligi
        loadNotifications();
        
        // Har 30 soniyada notificationlarni tekshirish
        setInterval(loadNotifications, 30000);
        
        // Test notification tugmasi
        $('#testNotificationBtn').on('click', function() {
            var btn = $(this);
            var icon = btn.find('i');
            
            // Loading holatini ko'rsatish
            icon.removeClass('fa-bell').addClass('fa-spinner fa-spin');
            btn.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("technolog.notifications.test") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Test notification yaratildi!', 'success');
                        // Notificationlarni qayta yuklash
                        loadNotifications();
                    } else {
                        showNotification(response.message || 'Xatolik yuz berdi!', 'error');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Xatolik yuz berdi!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification(errorMessage, 'error');
                },
                complete: function() {
                    icon.removeClass('fa-spinner fa-spin').addClass('fa-bell');
                    btn.prop('disabled', false);
                }
            });
        });
        
        // Notification tugmasini bosganda
        $('#notificationBtn').on('click', function() {
            loadNotifications();
        });
        
        // Barcha notificationlarni o'qilgan deb belgilash
        $('#markAllReadBtn').on('click', function() {
            markAllNotificationsAsRead();
        });
        
        // Notificationlarni yuklash
        function loadNotifications() {
            $.ajax({
                url: '{{ route("technolog.notifications") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        updateNotificationUI(response.notifications, response.count);
                    }
                },
                error: function() {
                    console.log('Notificationlarni yuklashda xatolik');
                }
            });
        }
        
        // Notification UI ni yangilash
        function updateNotificationUI(notifications, count) {
            var $count = $('#notificationCount');
            var $list = $('#notificationList');
            var $markAllBtn = $('#markAllReadBtn');
            
            // Count ni yangilash
            if (count > 0) {
                $count.text(count).show().addClass('notification-badge');
                $markAllBtn.show();
            } else {
                $count.hide().removeClass('notification-badge');
                $markAllBtn.hide();
            }
            
            // Notification listini yangilash
            if (notifications.length > 0) {
                var html = '';
                notifications.forEach(function(notification) {
                    var isUnread = !notification.read_at;
                    var timeAgo = getTimeAgo(notification.created_at);
                    
                    html += '<div class="notification-item ' + (isUnread ? 'unread' : '') + '" data-id="' + notification.id + '">';
                    html += '<div class="notification-content">' + notification.data.message + '</div>';
                    html += '<div class="notification-time">' + timeAgo + '</div>';
                    if (isUnread) {
                        html += '<div class="notification-actions">';
                        html += '<button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="' + notification.id + '">O\'qilgan deb belgilash</button>';
                        html += '</div>';
                    }
                    html += '</div>';
                });
                $list.html(html);
            } else {
                $list.html('<div class="text-center p-3"><i class="fas fa-bell-slash fa-2x text-muted mb-2"></i><p class="text-muted mb-0">Hech qanday xabar yo\'q</p></div>');
            }
            
            // Mark as read tugmalarini bog'lash
            $('.mark-read-btn').on('click', function() {
                var notificationId = $(this).data('id');
                markNotificationAsRead(notificationId);
            });
        }
        
        // Notification ni o'qilgan deb belgilash
        function markNotificationAsRead(notificationId) {
            $.ajax({
                url: '{{ route("technolog.notification.read", ":id") }}'.replace(':id', notificationId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // UI dan o'chirish
                        $('.notification-item[data-id="' + notificationId + '"]').removeClass('unread').find('.notification-actions').remove();
                        // Count ni yangilash
                        loadNotifications();
                    }
                }
            });
        }
        
        // Barcha notificationlarni o'qilgan deb belgilash
        function markAllNotificationsAsRead() {
            $.ajax({
                url: '{{ route("technolog.notifications.read_all") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        loadNotifications();
                        showNotification('Barcha xabarlar o\'qilgan deb belgilandi', 'success');
                    }
                }
            });
        }
        
        // Vaqt hisoblash funksiyasi
        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var diff = now - date;
            
            var minutes = Math.floor(diff / 60000);
            var hours = Math.floor(diff / 3600000);
            var days = Math.floor(diff / 86400000);
            
            if (minutes < 1) return 'Hozir';
            if (minutes < 60) return minutes + ' daqiqa oldin';
            if (hours < 24) return hours + ' soat oldin';
            return days + ' kun oldin';
        }
        
    });
</script>
@endif
@endsection