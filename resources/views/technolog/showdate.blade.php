@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
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

<div class="date">
    <!-- <div class="lline"></div> -->
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year_id != 1)
            <a href="/technolog/showdate/{{ $year_id-1 }}/0/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/technolog/showdate/{{ $year_id }}/{{ $month->id }}/0" class="month__item {{ ( $month->id == $month_id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/technolog/showdate/{{ $year->id+1 }}/0/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
    <div class="day">
        @foreach($days as $day)
            <a href="/technolog/showdate/{{ $day->year_id }}/{{ $day->month_id }}/{{ $day->id }}" class="day__item {{ ( $day->id == $day_id) ? 'active' : null }}">{{ $day->day_number }}</a>
        @endforeach
    </div>
    <!-- <div class="lline"></div> -->
</div>
<div class="py-4 px-4">
<div class="row">
    <div class="col-md-6">
        @foreach($days as $day)
        @if($day->id == $day_id)
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
<hr>
<table class="table table-light py-4 px-4">
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
            <!-- <th style="width: 70px;" rowspan="2">Nakladnoy</th> -->
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
    <?php $t = 1;  ?>   
    @foreach($nextdayitem as $row)
        <tr>
            <td>{{ $t++ }}</td>
            <td>{{ $row['kingar_name'] }}</td>
            <td>{{ $row['workers_count'] }} </td>
            <td><a href="/activsecondmenuPDF/{{ $day_id }}/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
            @foreach($ages as $age)
            @if(isset($row[$age->id]))
                <td>
                    {{ $row[$age->id][1]."  " }}
                    <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-agecount="{{ $row[$age->id][1] }}" data-dayid="{{ $day_id }}" data-monthid = "{{ $month_id }}" data-yearid = "{{ $year_id }}" data-ageid="{{ $age->id }}" data-kinid="{{ $row['kingar_name_id'] }}" style="cursor: pointer; margin-right: 16px;"> </i>
                    @if($row[$age->id][2] != null)
                    <i class="far fa-envelope" style="color: #c40c0c"></i> 
                    @endif
                </td>
                <td><a href="/activmenuPDF/{{ $day_id }}/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
            @else
                <td>{{ ' ' }}</td>
                <td>{{ ' ' }}</td>
            @endif
            @endforeach
            <!-- <td><a href="/activnakladPDF/{{ $day_id }}/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td> -->
            <td>
                @if($usage_status[$row['kingar_name_id']] == 'Sarflangan')
                    <i class="fas fa-check-circle" style="color: green;"></i>
                @else
                    <i class="fas fa-times-circle" style="color: red;"></i>
                    <i class="fas fa-carrot expense-btn" style="color: dodgerblue; font-size: 18px; margin-left: 10px; cursor: pointer;" 
                       data-dayid="{{ $day_id }}" 
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
</script>
@endsection