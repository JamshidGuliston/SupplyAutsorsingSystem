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
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- AddModal -->
<div class="modal editesmodal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                        <input type="number" name="maxday" placeholder="2-3 кунлик" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-info btn-sm w-100" onclick="shareToTelegram()">
                            <i class="fab fa-telegram"></i> Telegram
                        </button>
                    </div>
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
        });
    
    function updateSelectedProducts() {
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
        
        // Tanlangan mahsulotlarni ko'rsatish
        var container = document.getElementById('selected_products_container');
        container.innerHTML = '';
        
        var html = '';
        
        // Kategoriyalar ko'rsatiladi
        if (selectedCategories.length > 0) {
            html += '<div class="mt-3"><h6>Tanlangan kategoriyalar:</h6>';
            
            selectedCategories.forEach(function(category) {
                html += '<div class="card mb-2"><div class="card-header">' + category.name + '</div><div class="card-body">';
                
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
                            data.products.forEach(function(product) {
                                html += '<div class="row mb-2">';
                                html += '<div class="col-md-6">' + product.product_name + '</div>';
                                html += '<div class="col-md-4">';
                                html += '<input type="number" name="product_quantity[' + product.id + ']" class="form-control form-control-sm" placeholder="Miqdori" required>';
                                html += '</div>';
                                html += '<div class="col-md-2">';
                                html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(' + product.id + ')">O\'chir</button>';
                                html += '</div>';
                                html += '</div>';
                            });
                        } else {
                            html += '<div class="row mb-2">';
                            html += '<div class="col-md-6">Barcha mahsulotlar</div>';
                            html += '<div class="col-md-4">';
                            html += '<input type="number" name="category_quantity[' + category.id + ']" class="form-control form-control-sm" placeholder="Miqdori" required>';
                            html += '</div>';
                            html += '<div class="col-md-2">';
                            html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeCategory(' + category.id + ')">O\'chir</button>';
                            html += '</div>';
                            html += '</div>';
                        }
                    },
                    error: function() {
                        html += '<div class="row mb-2">';
                        html += '<div class="col-md-6">Barcha mahsulotlar</div>';
                        html += '<div class="col-md-4">';
                        html += '<input type="number" name="category_quantity[' + category.id + ']" class="form-control form-control-sm" placeholder="Miqdori" required>';
                        html += '</div>';
                        html += '<div class="col-md-2">';
                        html += '<button type="button" class="btn btn-sm btn-danger" onclick="removeCategory(' + category.id + ')">O\'chir</button>';
                        html += '</div>';
                        html += '</div>';
                    }
                });
                
                html += '</div></div>';
            });
            
            html += '</div>';
        }
        
        container.innerHTML = html;
    }
    
    function removeProduct(productId) {
        // Mahsulotni o'chirish logikasi
        console.log("Mahsulot o'chirildi: ", productId);
    }
    
    function removeCategory(categoryId) {
        var option = document.querySelector('#products_select option[value="cat-' + categoryId + '"]');
        if (option) {
            option.selected = false;
            updateSelectedProducts();
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