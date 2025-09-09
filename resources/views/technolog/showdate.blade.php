@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
    
    .filter-notification {
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
    
    .edit-workers-btn {
        transition: all 0.2s ease;
    }
    
    .edit-workers-btn:hover {
        transform: scale(1.1);
        color: #0dcaf0 !important;
    }
    
    .modal-header.bg-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
        border: none;
        transition: all 0.2s ease;
    }
    
    .btn-info:hover {
        background: linear-gradient(135deg, #0aa2c0 0%, #0dcaf0 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 202, 240, 0.3);
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/activagecountedit" method="post">
		    @csrf
            <div class="modal-header bg-blue">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="edites_modal">

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

<!-- Sarflash Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Mahsulotlarni sarflash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="expense-loading" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Yuklanmoqda...</span>
                    </div>
                </div>
                <div id="expense-content" style="display: none;">
                    <h6 id="kindgarden-name" class="mb-3"></h6>
                    <form id="expense-form">
                        <div id="products-list" class="row">
                            <!-- Mahsulotlar ro'yxati bu yerda yuklanadi -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-success" id="save-expense">Sarflash</button>
            </div>
        </div>
    </div>
</div>

<!-- O'tgan kunlarga bog'chalarni biriktirish Modal -->
<div class="modal fade" id="assignPastDaysModal" tabindex="-1" aria-labelledby="assignPastDaysModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="assignPastDaysModalLabel">O'tgan kunlarga bog'chalarni biriktirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assign-past-days-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Boshlanish sanasi</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Tugash sanasi</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bog'chalarni tanlang</label>
                        <div class="row" id="kindergartens-list">
                            <!-- Bog'chalar ro'yxati bu yerda yuklanadi -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Yosh guruhlarini tanlang</label>
                        <div class="row" id="age-ranges-list">
                            <!-- Yosh guruhlari ro'yxati bu yerda yuklanadi -->
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Eslatma:</strong> Tanlangan kunlar va bog'chalar uchun Number_children jadvaliga ma'lumot qo'shiladi.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" id="assign-past-days">Biriktirish</button>
            </div>
        </div>
    </div>
</div>

<!-- Xodimlar sonini o'zgartirish Modal -->
<div class="modal fade" id="editWorkersModal" tabindex="-1" aria-labelledby="editWorkersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="edit-workers-form">
                @csrf
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="editWorkersModalLabel">Xodimlar sonini o'zgartirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kindgarden-name-display" class="form-label">Bog'cha nomi:</label>
                        <input type="text" class="form-control" id="kindgarden-name-display" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="current-workers-count" class="form-label">Joriy xodimlar soni:</label>
                        <input type="text" class="form-control" id="current-workers-count" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="new-workers-count" class="form-label">Yangi xodimlar soni:</label>
                        <input type="number" class="form-control" id="new-workers-count" min="0" required>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Eslatma:</strong> Bu o'zgarish barcha yosh guruhlari uchun amal qiladi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-info">O'zgartirish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="date">
    <!-- <div class="lline"></div> -->
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($y_id != 1)
            <a href="/technolog/showdate/{{ $y_id-1 }}/0/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/technolog/showdate/{{ $y_id }}/{{ $month->id }}/0" class="month__item {{ ( $month->id == $m_id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/technolog/showdate/{{ $year->id+1 }}/0/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
    <div class="day">
        @foreach($days as $day)
            <a href="/technolog/showdate/{{ $day->year_id }}/{{ $day->month_id }}/{{ $day->id }}" class="day__item {{ ( $day->id == $aday) ? 'active' : null }}">{{ $day->day_number }}</a>
        @endforeach
    </div>
    <!-- <div class="lline"></div> -->
</div>
<div class="py-4 px-4">
<div class="row">
    <div class="col-md-6">
        @foreach($days as $day)
        @if($day->id == $aday)
            <b>{{ $day->day_number.":".$day->month_name.":".$day->year_name }}</b>
        @endif
        @endforeach
        <!-- <a href="/technolog/createnewdaypdf/{{ $day }}">
            <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
        </a> -->
    </div>
    <div class="col-md-3">
        <!-- <button type="button" class="btn btn-warning btn-sm" onclick="addPastDaysData()">
            <i class="fas fa-plus"></i> O'tgan kunlar ma'lumoti
        </button>
        <button type="button" class="btn btn-primary btn-sm ms-2" onclick="openAssignPastDaysModal()">
            <i class="fas fa-link"></i> Bog'chalarni biriktirish
        </button> -->
    </div>
    <div class="col-md-3">
        <div id="past-days-status"></div>
    </div>
</div>
    
    <!-- Filter va qidiruv qismi -->
    <div class="row mb-3 filter-section">
        <div class="col-md-3">
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
        <div class="col-md-3">
            <label for="searchInput" class="form-label">
                <i class="fas fa-search me-1"></i>Qidiruv:
                <small class="text-muted d-block">Qidiruv avtomatik saqlanadi</small>
            </label>
            <input type="text" class="form-control" id="searchInput" placeholder="Bog'cha nomi yoki oshpaz nomi...">
        </div>
        <div class="col-md-2">
            <label for="clearFilters" class="form-label">Filterlarni tozalash</label><br>
            <button class="btn btn-secondary me-2" id="clearFilters" title="Filterlarni tozalash va saqlangan ma'lumotlarni o'chirish"> 
                <i class="fas fa-trash-alt text-white"></i>
            </button>
        </div>
        <div class="col-md-2">
            <label for="downloadShowdateMenus" class="form-label">Menyular ZIP</label><br>
            <button class="btn btn-success me-2" id="downloadShowdateMenus" title="Barcha bog'cha menyularini ZIP arxiv qilish">
                <i class="fas fa-file-archive text-white"></i>
            </button>
        </div>
        <div class="col-md-2">
            <!-- Boshqa tugmalar uchun joy -->
        </div>      
    </div>
    
    <hr>
    
    <table class="table table-light py-4 px-4" id="mainTable">
        <thead>
            <tr>
                <th scope="col" rowspan="2">ID</th>
                <th scope="col" rowspan="2">MTT-nomi</th>
                <th scope="col" rowspan="2">Xodimlar</th> 
                <th scope="col" rowspan="2">Yangi Menyu</th> 
                @foreach($ages as $age)
                <th scope="col" colspan="2"> 
                    <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                </th>
                @endforeach
                <th style="width: 70px;" rowspan="2">Maxsulotlar ishlatilganligi</th>
            </tr>
            <tr style="color: #888888;">
                @foreach($ages as $age)
                <th><i class="fas fa-users"></i></th>
                <th><i class="fas fa-book-open"></i></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php $t = 1; ?>   
            @foreach($nextdayitem as $row)
                @php
                    $kindgarden = \App\Models\Kindgarden::find($row['kingar_name_id']);
                    $user = $kindgarden ? $kindgarden->user->first() : null;
                @endphp
                <tr data-region-id="{{ $kindgarden ? $kindgarden->region_id : '' }}" data-user-name="{{ $user ? $user->name : '' }}">
                    <td>{{ $t++ }}</td>
                    <td>{{ $row['kingar_name'] }}</td>
                    <td>
                        {{ $row['workers_count'] }}
                        <i class="fas fa-edit text-info edit-workers-btn" 
                           style="cursor: pointer; margin-left: 8px;" 
                           data-bs-toggle="modal" 
                           data-bs-target="#editWorkersModal"
                           data-day-id="{{ $aday }}"
                           data-kingar-name-id="{{ $row['kingar_name_id'] }}"
                           data-kindgarden-name="{{ $row['kingar_name'] }}"
                           data-current-workers="{{ $row['workers_count'] }}"
                           title="Xodimlar sonini o'zgartirish">
                        </i>
                    </td>
                    <td><a href="/activsecondmenuPDF/{{ $aday }}/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
                    @foreach($ages as $age)
                    @if(isset($row[$age->id]))
                        <td>
                            {{ $row[$age->id][1]."  " }}
                            <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-agecount="{{ $row[$age->id][1] }}" data-dayid="{{ $aday }}" data-monthid = "{{ $day->month_id }}" data-yearid = "{{ $day->year_id }}" data-ageid="{{ $age->id }}" data-kinid="{{ $row['kingar_name_id'] }}" style="cursor: pointer; margin-right: 16px;"> </i>
                            @if($row[$age->id][2] != null)
                            <i class="far fa-envelope" style="color: #c40c0c"></i> 
                            @endif
                        </td>
                        <td><a href="/activmenuPDF/{{ $aday }}/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
                    @else
                        <td>{{ ' ' }}</td>
                        <td>{{ ' ' }}</td>
                    @endif
                    @endforeach
                    <td>
                        @if($usage_status[$row['kingar_name_id']] == 'Sarflangan')
                            <i class="fas fa-check-circle" style="color: green;"></i>
                        @else
                            <i class="fas fa-times-circle" style="color: red;"></i>
                            <i class="fas fa-carrot expense-btn" style="color: dodgerblue; font-size: 18px; margin-left: 10px; cursor: pointer;" 
                               data-dayid="{{ $aday }}" 
                               data-kingardenid="{{ $row['kingar_name_id'] }}" 
                               data-toggle="modal" 
                               data-target="#expenseModal" 
                               title="Sarflash">Sarflash</i>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.edites').click(function() {
            // alert(1);    
            var kinid = $(this).attr('data-kinid');
            var dayid = $(this).attr('data-dayid');
            var monthid = $(this).attr('data-monthid');
            var yearid = $(this).attr('data-yearid');
            var ageid = $(this).attr('data-ageid');
            var agecount = $(this).attr('data-agecount');
            var modaledite = $('.edites_modal');
            modaledite.html("<input type='hidden' name='dayid' value="+dayid+"><input type='hidden' name='monthid' value="+monthid+"><input type='hidden' name='yearid' value="+yearid+"><input type='hidden' name='kinid' value="+kinid+"><input type='hidden' name='ageid' value="+ageid+"><input type='text' class='form-control' name='agecount' value="+agecount+">");
        });

        // Sarflash tugmasi bosilganda
        $('.expense-btn').click(function() {
            var dayid = $(this).attr('data-dayid');
            var kingardenid = $(this).attr('data-kingardenid');
            
            // Modalni ochish
            $('#expenseModal').modal('show');
            $('#expense-loading').show();
            $('#expense-content').hide();
            
            // Mahsulotlar ro'yxatini yuklash
            $.ajax({
                url: '/technolog/getProductsForExpense/' + dayid + '/' + kingardenid,
                method: 'GET',
                success: function(response) {
                    $('#expense-loading').hide();
                    $('#expense-content').show();
                    
                    // Bog'cha nomini ko'rsatish
                    $('#kindgarden-name').text(response.kindgarden.kingar_name + ' dan sarflash');
                    
                    // Mahsulotlar ro'yxatini yaratish
                    var productsList = '';
                    response.products.forEach(function(product) {
                        productsList += `
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">${product.product_name}</h6>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="products[${product.id}]" 
                                                   step="0.001" 
                                                   min="0" 
                                                   value="${product.product_weight}">
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    $('#products-list').html(productsList);
                    
                    // Yashirin ma'lumotlarni saqlash
                    $('#expense-form').data('dayid', response.day_id);
                    $('#expense-form').data('kingardenid', kingardenid);
                },
                error: function() {
                    alert('Xatolik yuz berdi!');
                    $('#expenseModal').modal('hide');
                }
            });
        });

        // Saqlash tugmasi
        $('#save-expense').click(function() {
            var form = $('#expense-form');
            var dayid = form.data('dayid');
            var kingardenid = form.data('kingardenid');
            var products = {};
            
            // Mahsulot ma'lumotlarini yig'ish
            form.find('input[name^="products"]').each(function() {
                var name = $(this).attr('name');
                var match = name.match(/products\[(\d+)\]/);
                if (match) {
                    var productId = match[1];
                    var weight = $(this).val();
                    if (weight && parseFloat(weight) > 0) {
                        products[productId] = parseFloat(weight);
                    }
                }
            });
            
            if (Object.keys(products).length === 0) {
                alert('Kamida bitta mahsulot miqdorini kiriting!');
                return;
            }
            
            // Saqlash
            $.ajax({
                url: '/technolog/saveProductExpense',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    day_id: dayid,
                    kingarden_id: kingardenid,
                    products: products
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#expenseModal').modal('hide');
                        location.reload(); // Sahifani yangilash
                    }
                },
                error: function() {
                    alert('Saqlashda xatolik yuz berdi!');
                }
            });
        });

        // Filter va qidiruv funksionalligi
        function filterTable() {
            var regionFilter = $('#regionFilter').val();
            var searchText = $('#searchInput').val().toLowerCase();
            var table = $('#mainTable tbody tr');
            
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
            localStorage.setItem('showdate_regionFilter', regionFilter);
            localStorage.setItem('showdate_searchInput', searchText);
        }
        
        // Sahifa yuklanganda filter qiymatlarini tiklash
        function restoreFilters() {
            var savedRegionFilter = localStorage.getItem('showdate_regionFilter');
            var savedSearchText = localStorage.getItem('showdate_searchInput');
            
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
        restoreFilters();
        
        // Filter va qidiruv eventlari
        $('#regionFilter, #searchInput').on('change keyup', filterTable);
        
        // Filterlarni tozalash
        $('#clearFilters').click(function() {
            $('#regionFilter').val('');
            $('#searchInput').val('');
            
            // localStorage dan ham o'chirish
            localStorage.removeItem('showdate_regionFilter');
            localStorage.removeItem('showdate_searchInput');
            
            filterTable();
            
            // Tozalash haqida xabar berish
            var clearMessage = 'Filterlar muvaffaqiyatli tozalandi!';
            showNotification(clearMessage, 'success');
        });
        
        // Xabar ko'rsatish funksiyasi
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
        
        // Barcha menyularni ZIP arxiv qilish
        $('#downloadShowdateMenus').click(function() {
            // Region tanlanganligini tekshirish
            var selectedRegion = $('#regionFilter').val();
            
            if (!selectedRegion) {
                showNotification('Iltimos, avval hududni tanlang!', 'error');
                return;
            }
            
            // Loading ko'rsatish
            $(this).html('<i class="fas fa-spinner fa-spin text-white"></i> Yuklanmoqda...');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: '{{ route("technolog.downloadShowdateMenusPDF") }}',
                method: 'GET',
                data: {
                    region_id: selectedRegion,
                    day_id: {{ $aday }}
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
                    var fileName = 'showdate_menyular_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.zip';
                    
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
                    $('#downloadShowdateMenus').html('<i class="fas fa-file-archive text-white"></i>');
                    $('#downloadShowdateMenus').prop('disabled', false);
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
                    $('#downloadShowdateMenus').html('<i class="fas fa-file-archive text-white"></i>');
                    $('#downloadShowdateMenus').prop('disabled', false);
                }
            });
        });
        
        // O'tgan kunlar uchun ma'lumot qo'shish
        function addPastDaysData() {
            if (confirm('O\'tgan 30 kun uchun ma\'lumot qo\'shishni xohlaysizmi?')) {
                // Tugmani o'chirish
                $('button[onclick="addPastDaysData()"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Qo\'shilmoqda...');
                
                $.ajax({
                    url: '/technolog/add-past-days-data',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        days_back: 30
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#past-days-status').html('<div class="alert alert-success alert-sm">' + response.message + '</div>');
                            setTimeout(() => {
                                location.reload(); // Sahifani yangilash
                            }, 2000);
                        } else {
                            $('#past-days-status').html('<div class="alert alert-danger alert-sm">Xatolik: ' + response.message + '</div>');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Xatolik yuz berdi!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        $('#past-days-status').html('<div class="alert alert-danger alert-sm">' + message + '</div>');
                    },
                    complete: function() {
                        // Tugmani qayta yoqish
                        $('button[onclick="addPastDaysData()"]').prop('disabled', false).html('<i class="fas fa-plus"></i> O\'tgan kunlar ma\'lumoti');
                    }
                });
            }
        }

        // Bog'chalarni biriktirish modalini ochish
        function openAssignPastDaysModal() {
            $('#assignPastDaysModal').modal('show');
            loadModalData();
        }
        
        // Modal ma'lumotlarini yuklash
        function loadModalData() {
            // Bog'chalar ro'yxatini yuklash
            $.ajax({
                url: '/technolog/get-kindergartens',
                method: 'GET',
                success: function(response) {
                    var kindergartensHtml = '';
                    response.kindergartens.forEach(function(kindergarten) {
                        kindergartensHtml += `
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kindergartens[]" value="${kindergarten.id}" id="kg_${kindergarten.id}">
                                    <label class="form-check-label" for="kg_${kindergarten.id}">
                                        ${kindergarten.kingar_name}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    $('#kindergartens-list').html(kindergartensHtml);
                }
            });
            
            // Yosh guruhlari ro'yxatini yuklash
            $.ajax({
                url: '/technolog/get-age-ranges',
                method: 'GET',
                success: function(response) {
                    var ageRangesHtml = '';
                    response.age_ranges.forEach(function(ageRange) {
                        ageRangesHtml += `
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="age_ranges[]" value="${ageRange.id}" id="age_${ageRange.id}">
                                    <label class="form-check-label" for="age_${ageRange.id}">
                                        ${ageRange.age_name}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    $('#age-ranges-list').html(ageRangesHtml);
                }
            });
        }
        
        // Bog'chalarni biriktirish
        $('#assign-past-days').click(function() {
            var form = $('#assign-past-days-form');
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            
            if (!startDate || !endDate) {
                alert('Iltimos, boshlanish va tugash sanalarini kiriting!');
                return;
            }
            
            var selectedKindergartens = [];
            form.find('input[name="kindergartens[]"]:checked').each(function() {
                selectedKindergartens.push($(this).val());
            });
            
            var selectedAgeRanges = [];
            form.find('input[name="age_ranges[]"]:checked').each(function() {
                selectedAgeRanges.push($(this).val());
            });
            
            if (selectedKindergartens.length === 0) {
                alert('Iltimos, kamida bitta bog\'chani tanlang!');
                return;
            }
            
            if (selectedAgeRanges.length === 0) {
                alert('Iltimos, kamida bitta yosh guruhini tanlang!');
                return;
            }
            
            if (confirm('Tanlangan kunlar va bog\'chalar uchun ma\'lumot qo\'shishni xohlaysizmi?')) {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Biriktirilmoqda...');
                
                $.ajax({
                    url: '/technolog/assign-past-days',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        start_date: startDate,
                        end_date: endDate,
                        kindergartens: selectedKindergartens,
                        age_ranges: selectedAgeRanges
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#assignPastDaysModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Xatolik: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        var message = 'Xatolik yuz berdi!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    },
                    complete: function() {
                        $('#assign-past-days').prop('disabled', false).html('Biriktirish');
                    }
                });
            }
        });

        // Xodimlar sonini o'zgartirish funksionalligi
        $('.edit-workers-btn').click(function() {
            var dayId = $(this).data('day-id');
            var kingarNameId = $(this).data('kingar-name-id');
            var kindgardenName = $(this).data('kindgarden-name');
            var currentWorkers = $(this).data('current-workers');
            
            // Modal ma'lumotlarini to'ldirish
            $('#kindgarden-name-display').val(kindgardenName);
            $('#current-workers-count').val(currentWorkers);
            $('#new-workers-count').val(currentWorkers);
            
            // Form ma'lumotlarini saqlash
            $('#edit-workers-form').data('day-id', dayId);
            $('#edit-workers-form').data('kingar-name-id', kingarNameId);
        });

        // Xodimlar sonini saqlash
        $('#edit-workers-form').submit(function(e) {
            e.preventDefault();
            
            var form = $(this);
            var dayId = form.data('day-id');
            var kingarNameId = form.data('kingar-name-id');
            var newWorkersCount = $('#new-workers-count').val();
            
            if (!newWorkersCount || newWorkersCount < 0) {
                showNotification('Iltimos, to\'g\'ri xodimlar sonini kiriting!', 'error');
                return;
            }
            
            // Saqlash tugmasini o'chirish
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saqlanmoqda...');
            
            // AJAX so'rov
            $.ajax({
                url: '{{ route("technolog.editWorkersCount") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    day_id: dayId,
                    kingar_name_id: kingarNameId,
                    workers_count: newWorkersCount
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        $('#editWorkersModal').modal('hide');
                        
                        // Sahifani yangilash
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
                    // Tugmani qayta tiklash
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endsection