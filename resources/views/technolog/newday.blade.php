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
    <div class="row">
        <div class="col-md-6">
            <b>Taxminiy menyular</b>
            <!-- <a href="/technolog/createnextdaypdf">
                <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
            </a> -->
        </div>
        <div class="col-md-3">
            
        </div>
        <div class="col-md-3">
        
        </div>
    </div>
    <hr>
    <!-- Filter va qidiruv qismi -->
    <div class="row mb-3 filter-section">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
            <label for="exampleModalsadd" class="form-label">Bog'cha qo'shish</label><br>
            <button class="btn btn-info" style="text-align:end" data-bs-toggle="modal" data-bs-target="#exampleModalsadd"> <i class="fas fa-plus-square text-white "></i></button>
        </div>      
    </div>

    <table class="table table-light py-4 px-4" id="secondTable">
        <thead>
            <tr>
                <th scope="col" rowspan="2">ID</th>
                <th scope="col" rowspan="2">MTT-nomi</th>
                <th scope="col" rowspan="2">Xodimlar 
                @foreach($ages as $age)
                <th scope="col" colspan="2"> 
                    <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                </th>
                @endforeach
                <th style="width: 70px;" rowspan="2">Накладной</th>
                <th style="width: 70px;" rowspan="2">Amallar</th>
            </tr>
            <tr style="color: #888888;">
                @foreach($ages as $age)
                <th><i class="fas fa-users"></i></th>
                <th><i class="fas fa-book-open"></i></th>
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
                        $status = '#f8f882';
                        $status_icon = "<i class='fas fa-question' style='color:rgb(238, 65, 65); font-size: 14px; cursor: pointer;'></i>";
                        $st = $temp->where('kingar_name_id', $row['kingar_name_id'])->where('age_id', $age->id)->first();
                        if(isset($st->age_number) and $st->age_number == $row[$age->id][1]){
                            $status = '#93ff93';
                            $status_icon = "<i class='fas fa-check' style='color:rgb(18, 141, 13); font-size: 14px; cursor: pointer;'></i>";
                        }
                        
                        if(isset($row['created_at']) and isset($row['updated_at'])){
                            if($row['created_at']->format('Y-m-d H:i:s') != $row['updated_at']->format('Y-m-d H:i:s')){
                                $status = '#c2f6dc';
                                $status_icon = "<i class='fas fa-check' style='color:rgb(18, 141, 13); font-size: 14px; cursor: pointer;'></i>";
                            }
                        }
                    @endphp
                    <td style="background-color: {{ $status }};">
                      {{ $row[$age->id][1]."  " }}
                       @if($row[$age->id][2] != null and $st->age_number != $row[$age->id][1])
                        <i class="far fa-envelope envelope-notification" title="Yangi xabarnoma mavjud!"></i> 
                       @endif
                       <i class="ch_countedit far fa-edit" data-nextrow-id="{{ $row[$age->id][0]; }}" data-child-count="{{ $row[$age->id][1]; }}" data-temprow-id="{{ $row[$age->id][2]; }}" data-tempchild-count="{{ $row[$age->id][3]; }}" data-kinga-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#chcountModal" style="color: #727213; font-size: 14px; cursor: pointer;"></i>
                       <div class="status-icon-container">
                           {!! $status_icon !!}
                       </div>
                    </td>
                    <td>
                        <p>{{ $row[$age->id][5] }}</p>
                        <a href="/nextdaymenuPDF/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a> 
                        <i class="next_menu far fa-edit" data-nextmenu-id="{{ $row[$age->id][4]; }}" data-nextrow-count="{{ $row[$age->id][0]; }}" data-king-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#editnextmenuModal" style="color: #727213; font-size: 14px; cursor: pointer; margin-left: 11px;"></i>
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
    <?php $tr = 1 ?>
    @foreach($shops as $shop)
        <b>{{ $shop->shop_name }}</b>
        <a href="/technolog/nextdelivershop/{{ $shop->id }}" target="_blank">
        <i class="fas fa-shipping-fast" style="color: dodgerblue; font-size: 18px;"></i>
        </a>
        <br>
    @endforeach
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

        
    });
</script>
@endif
@endsection