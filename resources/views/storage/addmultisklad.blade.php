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
    }
    
    .category-products .table {
        margin-bottom: 0;
    }
    
    .category-products .table th {
        background-color: #e9ecef;
        border-top: none;
        font-size: 0.875rem;
    }
    
    /* Mahsulotlar jadvali uchun yaxshilangan stillar */
    .category-products .table {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .category-products .table thead th {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        font-weight: 600;
        text-align: center;
        padding: 12px 8px;
        border: none;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .category-products .table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .category-products .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .category-products .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .category-products .table tbody td {
        padding: 10px 8px;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
        font-size: 0.875rem;
    }
    
    .category-products .table tbody td:first-child {
        font-weight: 500;
        color: #495057;
    }
    
    .category-products .table tbody td:last-child {
        text-align: center;
        width: 80px;
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
    
    /* Mahsulot qatorlari uchun qo'shimcha stillar */
    .product-row {
        transition: all 0.3s ease;
    }
    
    .product-row:hover {
        background-color: #e3f2fd !important;
        transform: translateX(2px);
    }
    
    .product-row td:first-child {
        position: relative;
    }
    
    .product-row td:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .product-row:hover td:first-child::before {
        opacity: 1;
    }
    
    /* Jadval animatsiyasi */
    .table-hover tbody tr {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
        transform: translateY(10px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Har bir qator uchun kechikish */
    .product-row:nth-child(1) { animation-delay: 0.1s; }
    .product-row:nth-child(2) { animation-delay: 0.2s; }
    .product-row:nth-child(3) { animation-delay: 0.3s; }
    .product-row:nth-child(4) { animation-delay: 0.4s; }
    .product-row:nth-child(5) { animation-delay: 0.5s; }
    
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
                <div class="row">
                    <div class="col-md-5">
                        <div class="product-select">
                            <select id="onemenu" class="form-select" onchange="changeFunc();" aria-label="Default select example">
                                <option value="">3-7 ёш меню</option>
                                @foreach($menus as $row)
                                <option value="{{$row['id']}}">{{$row['menu_name']}} ({{$row['season_name']}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="product-select">
                            <select id="twomenu" class="form-select" aria-label="Default select example">
                                <option value="">Қисқа гурух меню</option>
                                @foreach($menus as $row)
                                <option value="{{$row['id']}}">{{$row['menu_name']}} ({{$row['season_name']}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                                <th scope="col" style="text-align: center;">3-7 ёш</th>
                                <th scope="col" style="text-align: center;">Ходимлар</th>
                                <th scope="col" style="text-align: center;">Қисқа гурух</th>
                            </tr>
                        </thead>
                        <tbody class="addfood">    
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-body foodcomposition"> 
                <div class="mb-3">
                    <label for="products_select" class="form-label">Mahsulot kategoriyalarini tanlang</label>
                    <div class="multiselect-container">
                        <select id="products_select" name="selected_products[]" class="form-select" multiple required>
                            @foreach($product_categories as $category)
                                <option value="cat-{{ $category->id }}" data-type="category" data-category-id="{{ $category->id }}" data-category-name="{{ $category->pro_cat_name }}">{{ $category->pro_cat_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalsettings">Ummumiy jo'natilgan Xisobot</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIncreased">Orttirilgan Xisobot</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRasxod">+ Yaratish</button>
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
                <th scope="col">Date</th>
                <th scope="col">Yaratilgan sana</th>
                <th style="width: 80px;">Svod</th>
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
                        <td>{{ $row->day_id }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d.m.Y H:i') : '-' }}</td>
                        <td>
                            <a href="/storage/onedaysvod/{{ $row->day_id }}" class="btn btn-sm btn-warning" target="_blank">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                        </td>

                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home/0/0">Orqaga</a>
</div>

@endsection

@section('script')
<script>
    function changeFunc() {
        var selectBox = document.getElementById("onemenu");
        var menuid = selectBox.options[selectBox.selectedIndex].value;
        var div = $('.afternoon');
        $.ajax({
            method: "GET",
            url: '/storage/getworkerfoods',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    }
    $(document).ready(function() {
        var tr = 0;
        $('.fa-plus').click(function() {
            var onemenuid = $('#onemenu').val();
            var onemenutext = $('#onemenu option:selected').text();
            var div = $('.addfood');
            var twomenuid = $('#twomenu').val();
            var twomenutext = $('#twomenu option:selected').text();
            var chkArray = [];
            if(onemenuid == "" || twomenuid == "")
            {
                alert("Menyu tanlang!");
            }
            else{
                tr++;
                var bb = 0;
                $("input:checkbox[id=vehicle]:checked").each(function(){
                    bb = 1;
                    div.append("<input type='hidden' name='workerfoods["+tr+"]["+$(this).val()+"]' value="+onemenuid+">");
                });
                
                div.append("<tr><td>"+tr+"-кун</td><td><input type='hidden' name='onemenu["+tr+"][4]' value="+onemenuid+"><input type='hidden' name='onemenu["+tr+"][1]' value="+onemenuid+"><input type='hidden' name='onemenu["+tr+"][2]' value="+onemenuid+">"+onemenutext+"</td><td>"+(bb ? "+":"-")+"</td><td><input type='hidden' name='onemenu["+tr+"][3]' value="+twomenuid+">"+twomenutext+"</td></tr>");
            }
            
        });
    });
    
    // Bo'g'chalar multiselect
    document.multiselect('#testSelect1')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
		});
    
    // Muassasalar multiselect
    document.multiselect('#testSelect2')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
		});
    
    // Mahsulotlar multiselect
    document.multiselect('#products_select')
        .setCheckBoxClick("checkboxAll", function(target, args) {
            console.log("Mahsulotlar Select All clicked: ", args.checked);
            updateSelectedProducts();
        })
        .setCheckBoxClick("category", function(target, args) {
            console.log("Kategoriya clicked: ", target.value, args.checked);
            updateSelectedProducts();
        })
        .setCheckBoxClick("product", function(target, args) {
            console.log("Mahsulot clicked: ", target.value, args.checked);
            // Mahsulot tanlanganda ham update qilish
            updateSelectedProducts();
        });
    
    // Multiselect change event-ni ham qo'shamiz
    $('#products_select').on('change', function() {
        console.log('Multiselect o\'zgarish event');
        updateSelectedProducts();
    });
    
    function updateSelectedProducts() {
        console.log('=== updateSelectedProducts() ishga tushdi ===');
        
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
                
                console.log('Kategoriya topildi:', categoryId, categoryName);
                
                // Duplikat kategoriyalarni oldini olish
                var exists = selectedCategories.find(function(cat) {
                    return cat.id === categoryId;
                });
                
                if (!exists) {
                    selectedCategories.push({
                        id: categoryId,
                        name: categoryName
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
                html += '<div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleCategory(' + category.id + ')">';
                html += '<span>' + category.name + '</span>';
                html += '<div class="d-flex align-items-center">';
                html += '<input type="number" name="category_quantity[' + category.id + '][total]" class="form-control form-control-sm me-2" style="width: 100px;" placeholder="Umumiy miqdori" required>';
                html += '<i class="fas fa-chevron-down toggle-icon" id="toggle-icon-' + category.id + '"></i>';
                html += '<button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeCategory(' + category.id + ')">O\'chir</button>';
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
                            html += '<thead><tr><th><i class="fas fa-box me-2"></i>Mahsulot nomi</th><th><i class="fas fa-cogs me-2"></i>Miqdori va Amal</th></tr></thead>';
                            html += '<tbody>';
                            data.products.forEach(function(product, index) {
                                html += '<tr class="product-row" data-product-id="' + product.id + '">';
                                html += '<td><i class="fas fa-circle me-2" style="color: #28a745; font-size: 8px;"></i>' + product.product_name + '</td>';
                                html += '<td>';
                                html += '<div class="d-flex align-items-center justify-content-between">';
                                html += '<input type="number" name="category_quantity[' + category.id + '][' + product.id + ']" class="form-control form-control-sm me-2" placeholder="Miqdori" style="width: 80px;" min="0" step="0.01">';
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
        var productInput = document.querySelector('input[name="product_quantity[' + productId + ']"]');
        if (productInput) {
            var tableRow = productInput.closest('tr');
            if (tableRow) {
                tableRow.remove();
                console.log('Mahsulot qatori o\'chirildi');
            }
        }
        
        // Kategoriyada qolgan mahsulotlarni tekshirish
        var categoryCard = document.querySelector('.category-card[data-category-id="' + categoryId + '"]');
        if (categoryCard) {
            var remainingProducts = categoryCard.querySelectorAll('input[name^="product_quantity["]');
            console.log('Kategoriyada qolgan mahsulotlar soni:', remainingProducts.length);
            
            // Agar kategoriyada mahsulot qolmagan bo'lsa
            if (remainingProducts.length === 0) {
                console.log('Kategoriyada mahsulot qolmagan, kategoriya ham o\'chiriladi');
                
                // Kategoriyani multiselect-dan o'chirish
                var option = document.querySelector('#products_select option[value="cat-' + categoryId + '"]');
                if (option) {
                    option.selected = false;
                }
                
                // Kategoriya card-ini o'chirish
                categoryCard.remove();
                
                // Agar boshqa kategoriyalar qolmagan bo'lsa, container-ni tozalash
                var remainingCards = document.querySelectorAll('.category-card');
                if (remainingCards.length === 0) {
                    var container = document.getElementById('selected_products_container');
                    container.innerHTML = '';
                    console.log('Barcha kategoriyalar o\'chirildi, container tozalandi');
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
        
        // Agar boshqa kategoriyalar qolmagan bo'lsa, container-ni tozalash
        var remainingCards = document.querySelectorAll('.category-card');
        if (remainingCards.length === 0) {
            var container = document.getElementById('selected_products_container');
            container.innerHTML = '';
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
                
                selectedCategories.push({
                    id: categoryId,
                    name: categoryName
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
    

    
    function enable() {
		document.multiselect('#testSelect1').setIsEnabled(true);
	}

	function disable() {
		document.multiselect('#testSelect1').setIsEnabled(false);
	}
</script>
@endsection