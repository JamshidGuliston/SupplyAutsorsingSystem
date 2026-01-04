@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
    .multiselect-container {
        margin-bottom: 15px;
    }
    
    .multiselect-container .multiselect {
        min-height: 100px;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .multiselect-container .multiselect .multiselect-option {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }
    
    .multiselect-container .multiselect .multiselect-option:hover {
        background-color: #f8f9fa;
    }
    
    .multiselect-container .multiselect .multiselect-option.selected {
        background-color: #007bff;
        color: white;
    }
    
    .multiselect-container .multiselect .multiselect-option.optgroup {
        font-weight: bold;
        background-color: #e9ecef;
        padding: 10px 12px;
    }
    
    .multiselect-container .multiselect .multiselect-option.optgroup + .multiselect-option {
        padding-left: 20px;
    }
    
    .share-button {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .share-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .share-button i {
        margin-right: 5px;
    }
    
    /* Kategoriya card stillari */
    .category-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.3s ease;
    }
    
    .category-card .card-header:hover {
        background-color: #e9ecef;
    }
    
    .toggle-icon {
        transition: transform 0.3s ease;
    }
    
    .category-products {
        border-top: 1px solid #dee2e6;
        background-color: #fafafa;
        padding: 8px 0;
    }
    
    .category-products .table {
        margin-bottom: 0;
    }
    
    .category-products .table th {
        background-color: #e9ecef;
        border-top: none;
        font-size: 0.875rem;
    }
    
    /* Mahsulotlar jadvali uchun oddiy stillar */
    .category-products .table {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 0;
    }
    
    .category-products .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-align: left;
        padding: 8px 12px;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.875rem;
    }
    
    .category-products .table tbody tr {
        border-bottom: 1px solid #dee2e6;
    }
    
    .category-products .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .category-products .table tbody td {
        padding: 6px 12px;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    .category-products .table tbody td:first-child {
        font-weight: 500;
        color: #495057;
    }
    
    .category-products .table tbody td:last-child {
        text-align: center;
        width: 60px;
    }
    
    /* Mahsulot o'chirish tugmasi uchun */
    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
        transition: all 0.3s ease;
        border-radius: 6px;
        padding: 6px 10px;
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }
    
    .btn-outline-danger i {
        font-size: 12px;
    }
    
    /* Table responsive uchun */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    /* Mahsulot qatorlari uchun oddiy stillar */
    .product-row {
        transition: background-color 0.2s ease;
    }
    
    .product-row:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Input maydonlari uchun stillar */
    .category-products input[type="number"] {
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    
    .category-products input[type="number"]:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }
    
    .category-products input[type="number"]:hover {
        border-color: #6c757d;
    }
    
    /* Kategoriya card header uchun qo'shimcha stillar */
    .category-card .card-header {
        padding: 15px 20px;
    }
    
    .category-card .card-header input[type="number"] {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .category-card .card-header input[type="number"]:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        outline: none;
        transform: translateY(-1px);
    }
    
    .category-card .card-header input[type="number"]:hover {
        border-color: #007bff;
    }
    
    .category-card .card-header .toggle-icon {
        transition: all 0.3s ease;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
    }
    
    .category-card .card-header .toggle-icon:hover {
        background-color: #f8f9fa;
        color: #495057 !important;
        transform: scale(1.1);
    }
    
    .category-card .card-header .toggle-icon:active {
        transform: scale(0.95);
    }
    
    /* Kategoriya nomi uchun stillar */
    .category-card .card-header span:first-child {
        transition: all 0.3s ease;
        padding: 5px 10px;
        border-radius: 6px;
    }
    
    .category-card .card-header span:first-child:hover {
        background-color: #f8f9fa;
        color: #007bff !important;
    }
    
    .category-card .card-header .btn-danger {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .category-card .card-header .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
    
    /* Toggle switch uchun stillar */
    .form-check-input {
        background-color: #e9ecef;
        border-color: #ced4da;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        border-color: #28a745;
    }
    
    .form-check-input:hover {
        transform: scale(1.05);
    }
    
    /* Qoldiq yozuvi uchun stillar */
    .category-card .card-header .d-flex .d-flex span {
        font-weight: 500;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    
    .category-card .card-header .d-flex .d-flex:hover span {
        color: #495057;
    }
    
    /* Miqdori va Amal ustuni uchun */
    .category-products .table tbody td:last-child {
        min-width: 150px;
    }
    
    .category-products .d-flex.justify-content-between {
        gap: 8px;
    }
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- AddModal -->
<div class="modal editesmodal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <form action="{{route('storage.newordersklad')}}" method="POST">
            @csrf
            <input type="hidden" id="titleid" name="titleid" value="">
            <div id="hiddenid">
            </div>
            <div class="modal-header">
                <!-- <h5 class="modal-title" id="exampleModalLabel">Продукт хисоби</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <div class="row" id="menu-selects-container">
                    @if(isset($ageRanges) && $ageRanges->count() > 0)
                        @php
                            $ageRangesCount = $ageRanges->count();
                            // Har bir yosh toifasi uchun column kengligini hisoblash
                            // Plus tugmasi uchun col-md-2 ajratilgan, qolgan joy yosh toifalariga taqsimlanadi
                            // Agar 3 ta bo'lsa: (12-2)/3 = 3.33, har biri col-md-3 yoki col-md-4
                            // Agar 2 ta bo'lsa: (12-2)/2 = 5, har biri col-md-5
                            // Agar 1 ta bo'lsa: col-md-10
                            if($ageRangesCount == 3) {
                                $colClass = 'col-md-3';
                            } elseif($ageRangesCount == 2) {
                                $colClass = 'col-md-5';
                            } elseif($ageRangesCount == 1) {
                                $colClass = 'col-md-10';
                            } else {
                                $colClass = 'col-md-2';
                            }
                        @endphp
                        @foreach($ageRanges as $index => $ageRange)
                            @php
                                // Ushbu age_range uchun seasonlarni olish
                                $seasons = isset($seasonsByAgeRange[$ageRange->id]) ? $seasonsByAgeRange[$ageRange->id] : collect();
                                // Select ID-larni dinamik yaratish
                                $selectId = 'menu_' . $ageRange->id;
                            @endphp
                            <div class="{{ $colClass }}">
                                <div class="product-select">
                                    <select id="{{ $selectId }}" class="form-select age-range-select"
                                            data-age-range-id="{{ $ageRange->id }}"
                                            onchange="changeFunc({{ $ageRange->id }});"
                                            aria-label="Default select example">
                                        <option value="">{{ $ageRange->age_name }} меню</option>
                                        @foreach($seasons as $season)
                                            @if($season->titlemenus && $season->titlemenus->count() > 0)
                                                <optgroup label="━━ {{ $season->season_name }} ━━">
                                                    @foreach($season->titlemenus as $parent)
                                                        <option value="{{ $parent->id }}">{{ $parent->menu_name }}</option>
                                                        @if($parent->children && $parent->children->count() > 0)
                                                            @foreach($parent->children as $child)
                                                                @php
                                                                    // Child menyuni faqat agar u shu age_range ga tegishli bo'lsa ko'rsatish
                                                                    $hasAgeRange = $child->age_range->contains('id', $ageRange->id);
                                                                @endphp
                                                                @if($hasAgeRange)
                                                                    <option value="{{ $child->id }}">&nbsp;&nbsp;&nbsp;└─ {{ $child->menu_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="col-md-2">
                        <div class="product-select">
                            <i class="fas fa-plus me-2" style="color:#23b242; cursor: pointer; padding-top: 10px"></i>
                        </div>
                    </div>
                    <div class="afternoon col-md-12">
                    </div>
                </div>  
            </div>
            <div class="modal-body">
                <div class="table">     
                    <table style="width:100%">
                        <thead style="background-color: floralwhite;">
                            <tr>
                                <th scope="col">...</th>
                            @if(isset($ageRanges) && $ageRanges->count() > 0)
                                @foreach($ageRanges as $index => $ageRange)
                                    <th scope="col" style="text-align: center;">{{ $ageRange->age_name }}</th>
                                @endforeach
                            @endif
                                <th scope="col" style="text-align: center;">Ходимлар</th>
                            </tr>
                        </thead>
                        <tbody class="addfood">    
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-body foodcomposition"> 
                <div class="mb-3">
                    <!-- crete checkbox for all -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="products_select_all">
                        <label class="form-check-label" for="products_select_all">
                            Barcha kategoriyalar
                        </label>
                    </div>
                </div>
                
                <!-- Yashirin kategoriyalar ro'yxati -->
                <select id="products_select" multiple style="display: none;">
                    @foreach($product_categories as $category)
                        <option value="cat-{{ $category->id }}" 
                                data-type="category" 
                                data-category-id="{{ $category->id }}" 
                                data-category-name="{{ $category->pro_cat_name }}" 
                                data-limit-quantity="0">
                            {{ $category->pro_cat_name }}
                        </option>
                    @endforeach
                </select>
                
                <div id="selected_products_container">
                    <!-- Tanlangan mahsulotlar bu yerda ko'rsatiladi -->
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- <input type="number" name="maxday" placeholder="2-3 кунлик" class="form-control" required> -->
                    </div>
                    <!-- <div class="col-md-4">
                        <button type="button" class="btn btn-info btn-sm w-100" onclick="shareToTelegram()">
                            <i class="fab fa-telegram"></i> Telegram
                        </button>
                    </div> -->
                </div>
                Боғчаларни танлаш
                <select id='testSelect1' name="gardens[]" class="form-select" aria-label="Default select example" multiple required>
                    @foreach($gardens as $row)
                        <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                    @endforeach
                </select>
                <br>
                Kun tanlang
                <select name="day" class="form-select" aria-label="Default select example" required>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                    @endforeach
                </select>
                <br>
                Sarlavha
                <input type="text" name="note" placeholder="Sarlavha" class="form-control" required>
                <br>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <input type="checkbox" required style="padding-right: 10px;">
                Tасдиқлаш
                <button type="submit" class="btn editsub btn-success">Яратиш</button>
            </div>
        </form>
        </div>
    </div>
</div>
{{-- Report --}}
<div class="modal fade" id="modalsettings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Umumiy Xisobot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.allreport')}}" method="GET" target="_blank">
            <div class="row modal-body">
                @csrf
                <div class="col-sm-4">
                    <select name="garden" class="form-select" aria-label="Default select example" required>
                        @foreach($gardens as $row)
                            <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="start" aria-label="Default select example" required>
                        <option value="">-Sanadan-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required>
                        <option value="">-Sanaga-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    Ko'rish
                    <button type="submit" name="report" class="btn btn-info form-control">KIRIM-CHIQIM <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <div class="col-sm-6">
                    Ko'rish
                    <button type="submit" name="nakladnoy" class="btn btn-info form-control">Nakladnoy PDF <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <br/>
            </div>
            </form>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>
{{-- Report of increase --}}
<div class="modal fade" id="modalIncreased" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Oshib ketilgan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.increasedreport')}}" method="GET" target="_blank">
            <div class="row modal-body">
                @csrf
                <div class="col-sm-4">
                    <select name="gardenID" class="form-select" aria-label="Default select example" required>
                        @foreach($gardens as $row)
                            <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="start" aria-label="Default select example" required>
                        <option value="">-Sanadan-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required>
                        <option value="">-Sanaga-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div><br/>
                <div class="col-sm-6">
                    Yuklab olish
                    <button type="submit" class="btn btn-info form-control">PDF <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <br/>
            </div>
            </form>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>

{{-- Rasxod Modal --}}
<div class="modal fade" id="modalRasxod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yangi Buyurtma Yaratish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.addrasxodgroup')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Buyurtma nomi</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Buyurtma nomini kiriting" required>
                    </div>
                    <div class="mb-3">
                        <label for="kingar_name_id" class="form-label">Muassasalar</label>
                        <select class="form-select" id="testSelect2" name="kingar_name[]" aria-label="Default select example" required multiple>
                            @foreach($gardens as $row)
                                <option value="{{$row->id}}">{{$row->kingar_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_id" class="form-label">Sana</label>
                        <select class="form-select" id="date_id" name="date_id" required>
                            <option value="">Sanani tanlang</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Yaratish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Bozorlik yaratish</button>
        </div>
        <div class="col-md-3">
            <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalsettings">Ummumiy jo'natilgan Xisobot</button> -->
        </div>
        <div class="col-md-3">
            <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIncreased">Orttirilgan Xisobot</button> -->
        </div>
        <div class="col-md-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRasxod">+ Yaratish</button>
            <!-- Yetkazib beruvchilar linki -->
            <a href="/storage/shopsHistory" class="btn btn-outline-primary ms-2" title="Yetkazib beruvchilar">
                <i class="fas fa-truck me-1"></i>Yetkazib beruvchilar
            </a>
        </div>
    </div>
    <hr>
    <!-- @if(isset($orders[0]->day_number))
    <h4>Oyning {{ $orders[0]->day_number."-sanasi" }}</h4>
    @endif -->
    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Note</th>
                <th scope="col">Yaratilgan sana</th>
                <th style="width: 80px;">Umumiy</th>
                <th style="width: 80px;">Tumanlar</th>
                <th style="width: 80px;">Bog'chalar</th>
                <th style="width: 80px;">...</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($orders as $row)
            @if(!isset($bool[$row->order_title]))
                    @php $bool[$row->order_title] = 1 @endphp
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td><a href="/storage/onedaymulti/{{ $row->order_title }}">{{ $row->order_title }}</a></td>
                        <!-- <td>
                            <a href="#" class="order-title-link" data-order-title="{{ $row->order_title }}" style="text-decoration: none; color: #007bff;">
                                {{ $row->order_title }}
                            </a>
                        </td> -->
                        <td>{{ $row->note }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d.m.Y H:i') : '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/storage/onedaysvod/{{ $row->order_title }}" class="btn btn-sm btn-warning" target="_blank" title="PDF">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a href="/storage/onedaysvodexcel/{{ $row->order_title }}" class="btn btn-sm btn-success" target="_blank" title="Excel">
                                    <i class="fa fa-file-excel"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/storage/ordersvodAllRegions/{{ $row->order_title }}" class="btn btn-sm btn-warning" target="_blank" title="PDF">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a href="/storage/ordersvodAllRegionsExcel/{{ $row->order_title }}" class="btn btn-sm btn-success" target="_blank" title="Excel">
                                    <i class="fa fa-file-excel"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/storage/generate-order-title-pdf/{{ $row->order_title }}" class="btn btn-sm btn-warning" target="_blank" title="PDF">
                                    <i class="fa fa-file-pdf"></i>
                                </a>
                                <a href="/storage/generate-order-title-excel/{{ $row->order_title }}" class="btn btn-sm btn-success" target="_blank" title="Excel">
                                    <i class="fa fa-file-excel"></i>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="/storage/delete-order-product/{{ $row->order_title }}">O'chirish</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home/0/0">Orqaga</a>
</div>

<!-- Order Title Details Modal -->
<div class="modal fade" id="orderTitleModal" tabindex="-1" aria-labelledby="orderTitleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderTitleModalLabel">Order Title Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="orderTitleDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function changeFunc(ageRangeId) {
        var selectId = 'menu_' + ageRangeId;
        var selectBox = document.getElementById(selectId);
        if (!selectBox || ageRangeId != 4) return;
        
        var menuid = selectBox.options[selectBox.selectedIndex].value;
        var div = $('.afternoon');
        
        if (menuid && menuid != "") {
            $.ajax({
                method: "GET",
                url: '/storage/getworkerfoods',
                data: {
                    'menuid': menuid,
                },
                success: function(data) {
                    div.html(data);
                }
            });
        } else {
            div.html('');
        }
    }
    $(document).ready(function() {
        var tr = 0;
        $('.fa-plus').click(function() {
            var ageRanges = @json($ageRanges ?? []);
            var div = $('.addfood');
            var hasSelectedMenu = false;
            var selectedMenus = [];
            var workerMenuSelected = false;
            
            // Har bir yosh toifasi uchun tanlangan menyuni tekshirish
            ageRanges.forEach(function(ageRange) {
                var ageRangeId = ageRange.id;
                var ageRangeName = ageRange.age_name;
                var selectId = 'menu_' + ageRangeId;
                
                var menuId = $('#' + selectId).val();
                var menuText = $('#' + selectId + ' option:selected').text();
                
                if(menuId && menuId != "") {
                    hasSelectedMenu = true;
                    selectedMenus.push({
                        ageRangeId: ageRangeId,
                        ageRangeName: ageRangeName,
                        menuId: menuId,
                        menuText: menuText,
                        selectId: selectId
                    });
                }
            });
            
            // Xodimlar uchun tanlangan menyuni tekshirish
            // Xodimlar uchun birinchi yosh toifasidagi menyu ishlatiladi
            var workerMenuId = '';
            var workerMenuText = '';
            if(ageRanges.length > 0) {
                var firstAgeRangeId = ageRanges[1].id;
                var firstSelectId = 'menu_' + firstAgeRangeId;
                $("input:checkbox[id=vehicle]:checked").each(function(){
                    workerMenuSelected = true;
                    workerMenuId = $('#' + firstSelectId).val();
                    workerMenuText = $('#' + firstSelectId + ' option:selected').text();
                });
            }
            
            // Agar hech qanday menyu tanlanmagan bo'lsa
            if(!hasSelectedMenu && !workerMenuSelected) {
                alert("Kamida bitta menyu tanlang!");
                return;
            }
            
            // Jadvalga yangi qator qo'shish
            tr++;
            var rowHtml = '<tr>';
            rowHtml += '<td>' + tr + '-кун</td>';
            
            // Har bir yosh toifasi uchun ustun qo'shish
            ageRanges.forEach(function(ageRange) {
                var ageRangeId = ageRange.id;
                var selectId = 'menu_' + ageRangeId;
                
                var menuId = $('#' + selectId).val();
                var menuText = $('#' + selectId + ' option:selected').text();
                
                if(menuId && menuId != "") {
                    // Yangi format uchun - har bir yosh toifasi uchun alohida
                    rowHtml += '<td><input type="hidden" name="menus['+tr+']['+ageRangeId+']" value="'+menuId+'">'+menuText+'</td>';
                } else {
                    rowHtml += '<td>-</td>';
                }
            });
            
            // Xodimlar ustuni
            if(workerMenuSelected && workerMenuId) {
                rowHtml += '<td>+</td>';
                // Hidden input qo'shish
                $("input:checkbox[id=vehicle]:checked").each(function(){
                    div.append('<input type="hidden" name="workerfoods['+tr+']['+$(this).val()+']" value="'+workerMenuId+'">');
                });
            } else {
                rowHtml += '<td>-</td>';
            }
            
            rowHtml += '</tr>';
            div.append(rowHtml);
        });
    });
    
    // Bo'g'chalar multiselect
    document.multiselect('#testSelect1')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Bog'cha 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Bog'cha checkbox for item with value '1' was clicked and got value ", args.checked);
		});
    
    // Muassasalar multiselect
    document.multiselect('#testSelect2')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Muassasa 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Muassasa checkbox for item with value '1' was clicked and got value ", args.checked);
		});
    
    // Barcha kategoriyalar checkbox o'zgarishida
    $('#products_select_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        console.log('Barcha kategoriyalar checkbox:', isChecked);
        
        if (isChecked) {
            // Barcha kategoriyalarni tanlash
            $('#products_select option').prop('selected', true);
        } else {
            // Barcha kategoriyalarni bekor qilish
            $('#products_select option').prop('selected', false);
        }
        
        updateSelectedProducts();
    });
    
    // Faqat mahsulotlar multiselect o'zgarishida updateSelectedProducts() chaqiriladi
    $('#products_select').on('change', function() {
        console.log('Mahsulotlar multiselect o\'zgarish event');
        updateSelectedProducts();
    });
    
    function updateSelectedProducts() {
        console.log('=== updateSelectedProducts() ishga tushdi ===');
        
        // Faqat mahsulot kategoriyalari multiselect-dan olinadi
        var selectedCategories = [];
        var options = document.querySelectorAll('#products_select option:checked');
        
        console.log('Tanlangan option-lar soni:', options.length);
        
        options.forEach(function(option) {
            var type = option.getAttribute('data-type');
            var value = option.value;
            
            console.log('Option value:', value, 'Type:', type);
            
            // Agar bu kategoriya bo'lsa (cat- bilan boshlansa)
            if (value.startsWith('cat-')) {
                var categoryId = option.getAttribute('data-category-id');
                var categoryName = option.getAttribute('data-category-name');
                var limitQuantity = option.getAttribute('data-limit-quantity') || 0;
                
                console.log('Kategoriya topildi:', categoryId, categoryName, 'Limit:', limitQuantity);
                
                // Duplikat kategoriyalarni oldini olish
                var exists = selectedCategories.find(function(cat) {
                    return cat.id === categoryId;
                });
                
                if (!exists) {
                    selectedCategories.push({
                        id: categoryId,
                        name: categoryName,
                        limitQuantity: limitQuantity
                    });
                }
            }
        });
        
        console.log('Tanlangan kategoriyalar:', selectedCategories);
        
        // Tanlangan mahsulotlarni ko'rsatish
        var container = document.getElementById('selected_products_container');
        container.innerHTML = '';
        
        console.log('Container tozalandi');
        
        var html = '';
        
        // Kategoriyalar ko'rsatiladi
        if (selectedCategories.length > 0) {
            html += '<div class="mt-3"><h6>Tanlangan kategoriyalar:</h6>';
            
            selectedCategories.forEach(function(category, index) {
                html += '<div class="card mb-2 category-card" data-category-id="' + category.id + '">';
                html += '<div class="card-header d-flex justify-content-between align-items-center">';
                html += '<span style="cursor: pointer;" onclick="toggleCategory(' + category.id + ')">' + category.name + '</span>';
                html += '<div class="d-flex align-items-center" style="gap: 15px;">';
                html += '<span style="font-weight: 500; color: #495057;">kun</span>';
                html += '<input type="number" name="category_quantity[' + category.id + '][total]" class="form-control" style="width: 150px; height: 40px; font-size: 14px;" placeholder="Umumiy miqdori" value="' + category.limitQuantity + '" required>';
                
                // Qoldiqni hisobga olish toggle switch (default active)
                html += '<div class="d-flex align-items-center" style="gap: 8px;">';
                html += '<span style="font-size: 12px; color: #28a745; white-space: nowrap; font-weight: 600;">Qoldiq</span>';
                html += '<div class="form-check form-switch" style="margin: 0;">';
                html += '<input class="form-check-input" type="checkbox" id="qoldiq_' + category.id + '" name="category_quantity[' + category.id + '][qoldiq]" style="width: 40px; height: 20px; cursor: pointer;" checked onchange="updateQoldiqStatus(' + category.id + ', this.checked)">';
                html += '</div>';
                html += '</div>';
                
                html += '<i class="fas fa-chevron-down toggle-icon" id="toggle-icon-' + category.id + '" style="font-size: 16px; color: #6c757d; cursor: pointer;" onclick="toggleCategory(' + category.id + ')"></i>';
                html += '<button type="button" class="btn btn-danger" style="height: 40px; padding: 8px 16px; font-size: 14px;" onclick="removeCategory(' + category.id + ')">O\'chir</button>';
                html += '</div>';
                html += '</div>';
                html += '<div class="card-body category-products" id="category-products-' + category.id + '" style="display: none;">';
                
                // Kategoriyaga tegishli mahsulotlarni AJAX orqali olish
                $.ajax({
                    method: "GET",
                    url: '/storage/get-category-products',
                    data: {
                        'category_id': category.id,
                    },
                    async: false,
                    success: function(data) {
                        if (data.products && data.products.length > 0) {
                            html += '<div class="table-responsive">';
                            html += '<table class="table table-sm table-hover">';
                            html += '<thead><tr><th><i class="fas fa-box me-2"></i>Mahsulot nomi</th><th><i class="fas fa-cogs me-2"></i></th></tr></thead>';
                            html += '<tbody>';
                            data.products.forEach(function(product, index) {
                                html += '<tr class="product-row" data-product-id="' + product.id + '">';
                                html += '<td><i class="fas fa-circle me-2" style="color: #28a745; font-size: 8px;"></i>' + product.product_name + '</td>';
                                html += '<td>';
                                html += '<div class="d-flex align-items-center justify-content-between">';
                                html += '<input type="hidden" name="category_quantity[' + category.id + '][' + product.id + ']" class="form-control form-control-sm me-2" placeholder="Miqdori" style="width: 80px;" min="0" value="1">';
                                html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProduct(' + product.id + ', ' + category.id + ')" title="Mahsulotni o\'chirish">';
                                html += '<i class="fas fa-trash-alt"></i>';
                                html += '</button>';
                                html += '</div>';
                                html += '</td>';
                                html += '</tr>';
                            });
                            html += '</tbody></table>';
                            html += '</div>';
                        } else {
                            html += '<p class="text-muted">Bu kategoriyada mahsulot mavjud emas</p>';
                        }
                    },
                    error: function() {
                        html += '<p class="text-danger">Mahsulotlarni yuklashda xatolik yuz berdi</p>';
                    }
                });
                
                html += '</div></div>';
            });
            
            html += '</div>';
        }
        
        container.innerHTML = html;
    }
    
    function removeProduct(productId, categoryId) {
        console.log("Mahsulot o'chirilmoqda: ", productId, "Kategoriya:", categoryId);
        
        // Mahsulot qatorini topish va o'chirish
        var productRow = document.querySelector('.product-row[data-product-id="' + productId + '"]');
        if (productRow) {
            productRow.remove();
            console.log('Mahsulot qatori o\'chirildi');
        }
        
        // Kategoriyada qolgan mahsulotlarni tekshirish
        var categoryCard = document.querySelector('.category-card[data-category-id="' + categoryId + '"]');
        if (categoryCard) {
            var remainingProducts = categoryCard.querySelectorAll('.product-row');
            console.log('Kategoriyada qolgan mahsulotlar soni:', remainingProducts.length);
            
            // Agar kategoriyada mahsulot qolmagan bo'lsa
            if (remainingProducts.length === 0) {
                console.log('Kategoriyada mahsulot qolmagan, mahsulotlar jadvali yashiriladi');
                
                // Mahsulotlar jadvalini yashirish
                var productsDiv = categoryCard.querySelector('.category-products');
                if (productsDiv) {
                    productsDiv.innerHTML = '<p class="text-muted mb-0">Bu kategoriyada mahsulot mavjud emas</p>';
                }
            }
        }
    }
    
    function removeCategory(categoryId) {
        console.log('Kategoriya o\'chirilmoqda:', categoryId);
        
        // Multiselect-dan kategoriyani o'chirish
        var option = document.querySelector('#products_select option[value="cat-' + categoryId + '"]');
        if (option) {
            option.selected = false;
            console.log('Kategoriya multiselect-dan o\'chirildi');
        }
        
        // Kategoriya card-ini o'chirish
        var categoryCard = document.querySelector('.category-card[data-category-id="' + categoryId + '"]');
        if (categoryCard) {
            categoryCard.remove();
            console.log('Kategoriya card o\'chirildi');
        }
        
        // "Barcha kategoriyalar" checkboxni o'chirish
        var allCategories = document.querySelectorAll('#products_select option');
        var selectedCategories = document.querySelectorAll('#products_select option:checked');
        if (selectedCategories.length < allCategories.length) {
            $('#products_select_all').prop('checked', false);
            console.log('Barcha kategoriyalar checkbox o\'chirildi');
        }
        
        // Agar boshqa kategoriyalar qolmagan bo'lsa, container-ni tozalash
        var remainingCards = document.querySelectorAll('.category-card');
        if (remainingCards.length === 0) {
            var container = document.getElementById('selected_products_container');
            container.innerHTML = '';
            $('#products_select_all').prop('checked', false);
            console.log('Barcha kategoriyalar o\'chirildi, container tozalandi');
        }
    }
    
    function toggleCategory(categoryId) {
        var productsDiv = document.getElementById('category-products-' + categoryId);
        var toggleIcon = document.getElementById('toggle-icon-' + categoryId);
        
        if (productsDiv.style.display === 'none') {
            productsDiv.style.display = 'block';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
        } else {
            productsDiv.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
    
    function shareToTelegram() {
        var selectedCategories = [];
        var options = document.querySelectorAll('#products_select option:checked');
        
        options.forEach(function(option) {
            var type = option.getAttribute('data-type');
            
            if (type === 'category') {
                var categoryId = option.getAttribute('data-category-id');
                var categoryName = option.getAttribute('data-category-name');
                var limitQuantity = option.getAttribute('data-limit-quantity') || 0;
                
                selectedCategories.push({
                    id: categoryId,
                    name: categoryName,
                    limitQuantity: limitQuantity
                });
            }
        });
        
        if (selectedCategories.length === 0) {
            alert('Iltimos, kamida bitta kategoriyani tanlang!');
            return;
        }
        
        // Telegram uchun xabar tayyorlash
        var message = '🛒 *Mahsulot buyurtmasi*\n\n';
        message += '📅 Sana: ' + new Date().toLocaleDateString('uz-UZ') + '\n\n';
        
        selectedCategories.forEach(function(category, index) {
            message += (index + 1) + '. *' + category.name + '*\n';
            
            // Kategoriyaga tegishli mahsulotlarni olish
            $.ajax({
                method: "GET",
                url: '/storage/get-category-products',
                data: {
                    'category_id': category.id,
                },
                async: false,
                success: function(data) {
                    if (data.products && data.products.length > 0) {
                        data.products.forEach(function(product, productIndex) {
                            message += '   • ' + product.product_name + '\n';
                        });
                    }
                }
            });
            message += '\n';
        });
        
        message += '📞 Bog\'lanish uchun: +998 XX XXX XX XX';
        
        // Telegram share URL yaratish
        var telegramUrl = 'https://t.me/share/url?url=' + encodeURIComponent(window.location.href) + '&text=' + encodeURIComponent(message);
        
        // Yangi oynada ochish
        window.open(telegramUrl, '_blank', 'width=600,height=400');
    }
    

    
    function updateQoldiqStatus(categoryId, isChecked) {
        console.log('Kategoriya', categoryId, 'uchun qoldiq holati:', isChecked ? 'yoqildi' : 'o\'chirildi');
        
        // Toggle switch holatini saqlash
        var qoldiqInput = document.querySelector('input[name="category_quantity[' + categoryId + '][qoldiq]"]');
        if (qoldiqInput) {
            qoldiqInput.checked = isChecked;
        }
        
        // Visual feedback
        var qoldiqLabel = qoldiqInput ? qoldiqInput.closest('.d-flex').querySelector('span') : null;
        if (qoldiqLabel) {
            if (isChecked) {
                qoldiqLabel.style.color = '#28a745';
                qoldiqLabel.style.fontWeight = '600';
            } else {
                qoldiqLabel.style.color = '#6c757d';
                qoldiqLabel.style.fontWeight = '500';
            }
        }
        
        // Form data-ga qoldiq holatini yozish
        var formData = {
            categoryId: categoryId,
            qoldiq: isChecked ? 1 : 0
        };
        console.log('Form data:', formData);
    }
    
    	function enable() {
		document.multiselect('#testSelect1').setIsEnabled(true);
	}

	function disable() {
		document.multiselect('#testSelect1').setIsEnabled(false);
	}
	
	// Order title link click event
	$(document).on('click', '.order-title-link', function(e) {
		e.preventDefault();
		var orderTitle = $(this).data('order-title');
		loadOrderTitleDetails(orderTitle);
	});
	
	function loadOrderTitleDetails(orderTitle) {
		$.ajax({
			url: '/storage/get-order-title-details/' + encodeURIComponent(orderTitle),
			method: 'GET',
			success: function(response) {
				displayOrderTitleDetails(response);
				$('#orderTitleModal').modal('show');
			},
			error: function(xhr) {
				alert('Ma\'lumotlarni yuklashda xatolik yuz berdi');
			}
		});
	}
	
	function displayOrderTitleDetails(data) {
		var html = '<div class="container-fluid">';
		html += '<h4 class="mb-3">' + data.order_title + '</h4>';
		
		// Regions
		html += '<div class="mb-3">';
		html += '<h6>Regions:</h6>';
		html += '<ul>';
		Object.values(data.regions).forEach(function(region) {
			html += '<li>' + region + '</li>';
		});
		html += '</ul>';
		html += '</div>';
		
		// Products table
		html += '<div class="table-responsive">';
		html += '<table class="table table-sm table-bordered">';
		html += '<thead><tr>';
		html += '<th>Maxsulot nomi</th>';
		html += '<th>Birligi</th>';
		
		// Bog'cha ustunlari
		data.orders.forEach(function(order) {
			html += '<th>' + (order.kinggarden.number_of_org || order.kinggarden.kingar_name) + '</th>';
		});
		
		html += '<th>JAMI</th>';
		html += '</tr></thead><tbody>';
		
		// Maxsulot qatorlari
		Object.values(data.products).forEach(function(product) {
			html += '<tr>';
			html += '<td>' + product.name + '</td>';
			html += '<td>' + product.unit + '</td>';
			
			// Har bir bog'cha uchun miqdor
			data.orders.forEach(function(order) {
				var weight = product.kindergartens[order.kingar_name_id] || 0;
				html += '<td>' + weight + '</td>';
			});
			
			html += '<td><strong>' + product.total + '</strong></td>';
			html += '</tr>';
		});
		
		html += '</tbody></table>';
		html += '</div>';
		
		html += '</div>';
		
		$('#orderTitleDetailsContent').html(html);
	}
	
	// Order title link click event
	$(document).on('click', '.order-title-link', function(e) {
		e.preventDefault();
		var orderTitle = $(this).data('order-title');
		loadOrderTitleDetails(orderTitle);
	});
	
	function loadOrderTitleDetails(orderTitle) {
		$.ajax({
			url: '/storage/get-order-title-details/' + encodeURIComponent(orderTitle),
			method: 'GET',
			success: function(response) {
				displayOrderTitleDetails(response);
				$('#orderTitleModal').modal('show');
			},
			error: function(xhr) {
				alert('Ma\'lumotlarni yuklashda xatolik yuz berdi');
			}
		});
	}
	
	function displayOrderTitleDetails(data) {
		var html = '<div class="container-fluid">';
		html += '<h4 class="mb-3">' + data.order_title + '</h4>';
		
		// Regions
		html += '<div class="mb-3">';
		html += '<h6>Regions:</h6>';
		html += '<ul>';
		Object.values(data.regions).forEach(function(region) {
			html += '<li>' + region + '</li>';
		});
		html += '</ul>';
		html += '</div>';
		
		// Products table
		html += '<div class="table-responsive">';
		html += '<table class="table table-sm table-bordered">';
		html += '<thead><tr>';
		html += '<th>Maxsulot nomi</th>';
		html += '<th>Birligi</th>';
		
		// Bog'cha ustunlari
		data.orders.forEach(function(order) {
			html += '<th>' + (order.kinggarden.number_of_org || order.kinggarden.kingar_name) + '</th>';
		});
		
		html += '<th>JAMI</th>';
		html += '</tr></thead><tbody>';
		
		// Maxsulot qatorlari
		Object.values(data.products).forEach(function(product) {
			html += '<tr>';
			html += '<td>' + product.name + '</td>';
			html += '<td>' + product.unit + '</td>';
			
			// Har bir bog'cha uchun miqdor
			data.orders.forEach(function(order) {
				var weight = product.kindergartens[order.kingar_name_id] || 0;
				html += '<td>' + weight + '</td>';
			});
			
			html += '<td><strong>' + product.total + '</strong></td>';
			html += '</tr>';
		});
		
		html += '</tbody></table>';
		html += '</div>';
		
		html += '</div>';
		
		$('#orderTitleDetailsContent').html(html);
	}
</script>
@endsection