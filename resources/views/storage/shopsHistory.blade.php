@extends('layouts.app')

@section('leftmenu')
    @include('storage.sidemenu');
@endsection
@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    .shop-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .shop-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .shop-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .delivery-item {
        background-color: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .delivery-item .kindergarten-name {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    .product-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .product-row:last-child {
        border-bottom: none;
    }

    .no-orders {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 30px;
    }

    .date-navigation {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .product-input-row {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .product-input-row .product-name {
        flex: 1;
        font-weight: 500;
    }

    .product-input-row input {
        width: 120px;
    }

    /* Collapse icon animation */
    .collapse-icon {
        transition: transform 0.3s ease;
    }

    .collapse-icon.rotated {
        transform: rotate(90deg);
    }

    .shop-header:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .shop-card .collapse {
        border-top: 1px solid rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Sana navigatsiyasi -->
    <div class="date-navigation">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-0">
                    <i class="fas fa-history me-2"></i>Yetkazuvchilar tarixi
                </h4>
                <small class="text-muted">
                    {{ $day->day_number }} - {{ $day->month->month_name }} - {{ $day->year->year_name }}
                </small>
            </div>
            <div class="col-md-4 text-end">
                <a href="/storage/addmultisklad" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Orqaga
                </a>
            </div>
        </div>
    </div>

    <!-- Sana tanlash -->
    <div class="date mb-4">
        <div class="year first-text fw-bold text-center">
            {{ $day->year->year_name }}
        </div>
        <div class="month">
            @if($day->year->id != 1)
                <a href="/storage/shopsHistory/0" class="month__item">{{ $day->year->year_name - 1 }}</a>
            @endif
            @foreach($months as $month)
                <a href="/storage/shopsHistory/{{ \App\Models\Day::where('month_id', $month->id)->first()->id ?? $day->id }}"
                   class="month__item {{ ( $month->id == $day->month_id ) ? 'active first-text' : 'second-text' }} fw-bold">
                   {{ $month->month_name }}
                </a>
            @endforeach
        </div>
        <div class="day" style="flex-wrap: wrap;">
            @foreach($days as $d)
                <a href="/storage/shopsHistory/{{ $d->id }}"
                   class="day__item {{ ( $d->id == $day->id ) ? 'active' : '' }}">
                   {{ $d->day_number }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Hisobot filterlari -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Hisobot parametrlari
            </h6>
        </div>
        <div class="card-body">
            <form id="reportFilterForm">
                <div class="row">
                    <!-- Sana turi -->
                    <div class="col-md-3 mb-3">
                        <label for="date_type" class="form-label">Hisobot turi</label>
                        <select class="form-select" id="date_type" name="date_type">
                            <option value="daily">Kunlik</option>
                            <option value="monthly">Oylik</option>
                            <option value="range">Sanadan-sanaga</option>
                        </select>
                    </div>

                    <!-- Boshlang'ich sana -->
                    <div class="col-md-3 mb-3" id="start_date_container">
                        <label for="start_date" class="form-label">Sana / Boshlanish</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Tugash sanasi (faqat range uchun) -->
                    <div class="col-md-3 mb-3" id="end_date_container" style="display: none;">
                        <label for="end_date" class="form-label">Tugash sanasi</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Tuman filter -->
                    <div class="col-md-3 mb-3">
                        <label for="region_id" class="form-label">Tuman</label>
                        <select class="form-select" id="region_id" name="region_id">
                            <option value="">Barchasi</option>
                            @php
                                $regions = \App\Models\Region::orderBy('region_name')->get();
                            @endphp
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Do'kon filter -->
                    <div class="col-md-3 mb-3">
                        <label for="shop_id" class="form-label">Yetkazuvchi</label>
                        <select class="form-select" id="shop_id" name="shop_id">
                            <option value="">Barchasi</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->shop_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Hisobot tugmalari -->
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-danger" id="generatePdfBtn">
                            <i class="fas fa-file-pdf me-1"></i>PDF yuklash
                        </button>
                        <button type="button" class="btn btn-success" id="generateExcelBtn">
                            <i class="fas fa-file-excel me-1"></i>Excel yuklash
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Yetkazuvchilar ro'yxati -->
    <div class="row">
        @forelse($shops as $shop)
        <div class="col-md-6 col-lg-4">
            <div class="card shop-card">
                <div class="shop-header" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#shopCollapse{{ $shop->id }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chevron-right collapse-icon me-2" id="collapseIcon{{ $shop->id }}"></i>
                        <div>
                            <h6 class="mb-0">
                                <i class="fas fa-store me-2"></i>{{ $shop->shop_name }}
                            </h6>
                            @if($shop->phone)
                                <small class="opacity-75">{{ $shop->phone }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        @if(isset($orders[$shop->id]) && $orders[$shop->id]->count() > 0)
                            <span class="badge bg-success me-2">{{ $orders[$shop->id]->count() }}</span>
                        @else
                            <span class="badge bg-secondary me-2">0</span>
                        @endif
                        <button type="button" class="btn btn-light btn-sm add-product-btn"
                                data-shop-id="{{ $shop->id }}"
                                data-shop-name="{{ $shop->shop_name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#addProductModal"
                                title="Maxsulot qo'shish"
                                onclick="event.stopPropagation();">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="shopCollapse{{ $shop->id }}">
                    <div class="card-body">
                        @if(isset($orders[$shop->id]) && $orders[$shop->id]->count() > 0)
                            @foreach($orders[$shop->id] ?? [] as $order)
                            <div class="card mb-2 order-card" data-order-id="{{ $order->id }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $order->kinggarden->kingar_name ?? 'Noma\'lum' }}</h6>

                                    @foreach($order->orderProductStructures as $structure)
                                        <div class="d-flex align-items-center justify-content-between mb-2 product-row" id="structure-{{ $structure->id }}">
                                            <div>
                                                <strong>{{ $structure->product->product_name ?? 'Noma\'lum' }}</strong>
                                                <div class="small text-muted">{{ $structure->product->size->size_name ?? '' }}</div>
                                            </div>

                                            <div class="text-end">
                                                <span class="badge bg-primary me-2 structure-weight" data-id="{{ $structure->id }}">{{ number_format($structure->product_weight, 2, '.', '') }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-structure" data-id="{{ $structure->id }}" data-weight="{{ $structure->product_weight }}" title="Tahrirlash">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-structure" data-id="{{ $structure->id }}" title="O'chirish">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="no-orders">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <br>Bu kunda yetkazma yo'q
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <h5>Hech qanday yetkazuvchi topilmadi</h5>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Maxsulot qo'shish Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addProductForm">
                @csrf
                <input type="hidden" name="shop_id" id="modal_shop_id">
                <input type="hidden" name="day_id" value="{{ $day->id }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Maxsulot qo'shish
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong id="modal_shop_name"></strong> uchun
                        <strong>{{ $day->day_number }}.{{ $day->month->month_name }}.{{ $day->year->year_name }}</strong> sanasiga maxsulot qo'shish
                    </div>

                    <!-- Yuklanmoqda -->
                    <div id="modal-loading" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Ma'lumotlar yuklanmoqda...</p>
                    </div>

                    <!-- Kontent -->
                    <div id="modal-content" style="display: none;">
                        <!-- Bog'cha tanlash -->
                        <div class="mb-3">
                            <label for="kingar_name_id" class="form-label">Bog'cha tanlang *</label>
                            <select class="form-select" id="kingar_name_id" name="kingar_name_id" required>
                                <option value="">-- Bog'chani tanlang --</option>
                            </select>
                        </div>

                        <!-- Izoh -->
                        <div class="mb-3">
                            <label for="note" class="form-label">Izoh</label>
                            <input type="text" class="form-control" id="note" name="note" placeholder="Ixtiyoriy izoh...">
                        </div>

                        <!-- Maxsulotlar ro'yxati -->
                        <div class="mb-3">
                            <label class="form-label">Maxsulotlar va miqdori *</label>
                            <div id="products-container" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <!-- Dinamik to'ldiriladi -->
                            </div>
                        </div>
                    </div>

                    <!-- Xatolik -->
                    <div id="modal-error" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="modal-error-text"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary" id="modal-submit-btn">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit modal -->
<div class="modal fade" id="editStructureModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Maxsulotni tahrirlash</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editStructureForm">
          <input type="hidden" name="id" id="edit-structure-id">
          <div class="mb-2">
            <label class="form-label">Og'irlik</label>
            <input type="number" step="0.01" min="0" class="form-control" name="weight" id="edit-structure-weight" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
        <button type="button" class="btn btn-primary" id="saveEditStructure">Saqlash</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Collapse ochilganda/yopilganda ikonkani aylantirish
        $('.collapse').on('show.bs.collapse', function() {
            const shopId = $(this).attr('id').replace('shopCollapse', '');
            $('#collapseIcon' + shopId).addClass('rotated');
        });

        $('.collapse').on('hide.bs.collapse', function() {
            const shopId = $(this).attr('id').replace('shopCollapse', '');
            $('#collapseIcon' + shopId).removeClass('rotated');
        });

        // Sana turi o'zgarganda
        $('#date_type').on('change', function() {
            const dateType = $(this).val();

            if (dateType === 'range') {
                $('#end_date_container').show();
                $('#start_date_container label').text('Boshlanish sanasi');
            } else {
                $('#end_date_container').hide();
                if (dateType === 'daily') {
                    $('#start_date_container label').text('Sana');
                } else if (dateType === 'monthly') {
                    $('#start_date_container label').text('Oy');
                }
            }
        });

        // PDF yuklash
        $('#generatePdfBtn').on('click', function() {
            const formData = $('#reportFilterForm').serialize();
            const url = '/storage/shops-history-report-pdf?' + formData;
            window.open(url, '_blank');
        });

        // Excel yuklash
        $('#generateExcelBtn').on('click', function() {
            const formData = $('#reportFilterForm').serialize();
            const url = '/storage/shops-history-report-excel?' + formData;
            window.location.href = url;
        });

        // Maxsulot qo'shish tugmasi bosilganda
        $('.add-product-btn').on('click', function() {
            const shopId = $(this).data('shop-id');
            const shopName = $(this).data('shop-name');

            $('#modal_shop_id').val(shopId);
            $('#modal_shop_name').text(shopName);
            $('input[name="day_id"]').val('{{ $day->id }}');

            // Modal holatini tozalash
            $('#modal-loading').show();
            $('#modal-content').hide();
            $('#modal-error').hide();
            $('#modal-submit-btn').prop('disabled', true);

            // Shop maxsulotlari va bog'chalarini yuklash
            $.ajax({
                url: '{{ route("storage.getShopProductsAndKindergartens") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    shop_id: shopId
                },
                success: function(response) {
                    if (response.success) {
                        // Bog'chalar ro'yxatini to'ldirish
                        const kindergartenSelect = $('#kingar_name_id');
                        kindergartenSelect.empty();
                        kindergartenSelect.append('<option value="">-- Bog\'chani tanlang --</option>');

                        if (response.kindergartens.length > 0) {
                            response.kindergartens.forEach(function(kg) {
                                kindergartenSelect.append('<option value="' + kg.id + '">' + kg.kingar_name + '</option>');
                            });
                        } else {
                            kindergartenSelect.append('<option value="" disabled>Bu shopga bog\'cha biriktirilmagan</option>');
                        }

                        // Maxsulotlar ro'yxatini to'ldirish
                        const productsContainer = $('#products-container');
                        productsContainer.empty();

                        if (response.products.length > 0) {
                            response.products.forEach(function(product, index) {
                                const productRow = '<div class="product-input-row">' +
                                    '<span class="product-name">' + product.product_name +
                                    ' <small class="text-muted">(' + product.size_name + ')</small></span>' +
                                    '<input type="hidden" name="products[' + index + '][product_id]" value="' + product.id + '">' +
                                    '<input type="number" class="form-control form-control-sm product-weight" ' +
                                    'name="products[' + index + '][weight]" value="0" min="0" step="0.01" placeholder="0.00">' +
                                    '</div>';
                                productsContainer.append(productRow);
                            });
                        } else {
                            productsContainer.html('<div class="text-center text-muted py-3">' +
                                '<i class="fas fa-box-open fa-2x mb-2"></i><br>' +
                                'Bu shopga maxsulot biriktirilmagan</div>');
                        }

                        $('#modal-loading').hide();
                        $('#modal-content').show();
                        $('#modal-submit-btn').prop('disabled', false);
                    } else {
                        $('#modal-loading').hide();
                        $('#modal-error-text').text(response.message || 'Xatolik yuz berdi');
                        $('#modal-error').show();
                    }
                },
                error: function(xhr) {
                    $('#modal-loading').hide();
                    let errorMessage = 'Xatolik yuz berdi!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    $('#modal-error-text').text(errorMessage);
                    $('#modal-error').show();
                }
            });
        });

        // Maxsulot qo'shish formasi
        $('#addProductForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Kamida bitta maxsulot kiritilganligini tekshirish
            let hasProduct = false;
            $('.product-weight').each(function() {
                if (parseFloat($(this).val()) > 0) {
                    hasProduct = true;
                    return false;
                }
            });

            if (!hasProduct) {
                alert('Kamida bitta maxsulot miqdorini kiriting!');
                return;
            }

            if (!$('#kingar_name_id').val()) {
                alert('Bog\'chani tanlang!');
                return;
            }

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saqlanmoqda...');

            $.ajax({
                url: '{{ route("storage.storeShopOrder") }}',
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addProductModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'Xatolik yuz berdi!');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Xatolik yuz berdi!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // CSRF uchun
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Edit tugma
        $(document).on('click', '.btn-edit-structure', function(){
            var id = $(this).data('id');
            var weight = $(this).data('weight');
            $('#edit-structure-id').val(id);
            $('#edit-structure-weight').val(weight);
            var editModal = new bootstrap.Modal(document.getElementById('editStructureModal'));
            editModal.show();
        });

        // Saqlash (update)
        $('#saveEditStructure').on('click', function(){
            var id = $('#edit-structure-id').val();
            var weight = parseFloat($('#edit-structure-weight').val());
            if (isNaN(weight) || weight < 0) {
                alert('Iltimos to\'g\'ri og\'irlik kiriting');
                return;
            }

            $.post("{{ route('storage.updateOrderProductStructure') }}", { id: id, weight: weight })
            .done(function(res){
                if(res.success){
                    $('#structure-' + id).find('.structure-weight').text(weight.toFixed(2));
                    $('#structure-' + id).find('.btn-edit-structure').data('weight', weight);
                    bootstrap.Modal.getInstance(document.getElementById('editStructureModal')).hide();
                } else {
                    alert(res.message || 'Xatolik yuz berdi');
                }
            }).fail(function(xhr){
                alert('So\'rov bajarilmadi: ' + (xhr.responseJSON?.message || xhr.statusText));
            });
        });

        // Delete tugma
        $(document).on('click', '.btn-delete-structure', function(){
            if(!confirm("Bu mahsulotni o'chirmoqchimisiz?")) return;
            var id = $(this).data('id');
            $.post("{{ route('storage.deleteOrderProductStructure') }}", { id: id })
            .done(function(res){
                if(res.success){
                    $('#structure-' + id).remove();
                } else {
                    alert(res.message || 'Xatolik yuz berdi');
                }
            }).fail(function(xhr){
                alert('So\'rov bajarilmadi: ' + (xhr.responseJSON?.message || xhr.statusText));
            });
        });

        // Inline add (client-side tekshiruv va AJAX submit)
        $(document).on('click', '.btn-add-structure', function(){
            var form = $(this).closest('form.inline-add-product');
            var orderId = form.data('order-id');
            var productId = form.find('select[name="product_id"]').val();
            var weight = parseFloat(form.find('input[name="weight"]').val());

            if(!productId){
                alert('Maxsulot tanlang');
                return;
            }
            if(isNaN(weight) || weight <= 0){
                alert('Iltimos musbat og\'irlik kiriting');
                return;
            }

            // AJAX qo'shish: to'liq struktura kerak bo'lsa, serverdagi route-ni moslang
            $.ajax({
                url: "{{ route('storage.storeShopOrder') }}",
                method: "POST",
                data: {
                    shop_id: {{ $shop->id ?? 'null' }},
                    day_id: {{ $day->id ?? 'null' }},
                    kingar_name_id: '{{ $order->kingar_name_id ?? '' }}',
                    products: [{ product_id: productId, weight: weight }]
                },
                success: function(resp){
                    if(resp.success){
                        // qaytgan ma'lumotga qarab UIni yangilang yoki sahifani refresh qiling
                        location.reload();
                    } else {
                        alert(resp.message || 'Qo\'shishda xatolik');
                    }
                },
                error: function(xhr){
                    alert('So\'rov bajarilmadi: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            });
        });
    });
</script>
@endsection
