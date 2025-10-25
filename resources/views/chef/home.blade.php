@extends('layouts.app')

@section('css')
<style>
    /* Share notification uchun */
    .share-notification {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        animation: slideInRight 0.3s ease-out;
    }
    
    .share-notification.alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .share-notification.alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
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
    
    /* Button hover effektlari */
    .btn-info:hover {
        background-color: #17a2b8;
        border-color: #17a2b8;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .btn-success:hover {
        background-color: #28a745;
        border-color: #28a745;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    /* Share button uchun maxsus stillar */
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
        font-weight: 500;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn-info:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    

    
    /* Menu preview uchun stillar */
    .menu-preview-image {
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .menu-preview-image:hover {
        transform: scale(1.02);
    }
    
    .menu-preview-container {
        position: relative;
    }
    
    /* Zoom modal uchun stillar */
    .modal-fullscreen .modal-body {
        background-color: #f8f9fa;
    }
    
    .modal-fullscreen img {
        max-width: 100vw;
        max-height: 100vh;
        object-fit: contain;
    }
    
    /* Modal oynaning atrofi uchun */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.3) !important;
    }
    
    /* Gap responsive */
    @media (max-width: 768px) {
        .d-flex.gap-2 {
            flex-direction: column;
        }
        .d-flex.gap-2 .btn {
            width: 100% !important;
        }
    }
    
    /* Card ko'rinishi uchun qo'shimcha stillar */
    .card.border-primary {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card.border-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .card-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }
    
    .card-footer.bg-light {
        background-color: #f8f9fa !important;
        border-top: 1px solid #dee2e6;
    }
    
    .badge.bg-success {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }
    
    /* Responsive card layout */
    @media (max-width: 768px) {
        .col-md-6.col-lg-4 {
            margin-bottom: 1rem;
        }
    }
    
    /* Inline menyu ko'rinishi uchun stillar */
    .menu-inline-image {
        cursor: pointer;
        transition: transform 0.2s ease;
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .menu-inline-image:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .menu-inline-container {
        position: relative;
        overflow: hidden;
    }
    
    /* Zoom modal uchun stillar */
    .zoom-modal .modal-body {
        background-color: #f8f9fa;
        padding: 0;
    }
    
    .zoom-modal img {
        max-width: 100vw;
        max-height: 100vh;
        object-fit: contain;
        cursor: zoom-out;
    }
    
    /* Menu container animatsiyasi */
    .menu-container {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('leftmenu')
@include('chef.sidemenu'); 
@endsection

@section('content')
<!-- EDD -->
<div class="modal fade" id="productsshowModal" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Kelgan maxsulotlar</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('chef.right')}}" method="POST">
                @csrf
                <input type="hidden" name="orderid" value="{{ isset($inproducts[0]->order_product_name_id) ? $inproducts[0]->order_product_name_id : '' }}">
                <div class="modal-body">
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Miqdori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($inproducts as $all)
                                <tr>
                                    <th scope="row">{{ ++$i }}</th>
                                    <td>{{ $all->product_name }}</td>
                                    <td>{{ $all->product_weight." ".$all->size_name }}</td>
                                </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn add-age btn-primary text-white">Qabul qilish</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Bolalar sonini o'zgartirish modal -->
<div class="modal fade" id="editChildrenCountModal" tabindex="-1" aria-labelledby="editChildrenCountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('chef.update_children_count_by_chef') }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-white" id="editChildrenCountModalLabel">Bolalar sonini o'zgartirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="age_name_display" class="form-label">Yosh guruhi:</label>
                        <input type="text" class="form-control" id="age_name_display" readonly>
                        <input type="hidden" name="age_id" id="age_id">
                    </div>
                    <div class="mb-3">
                        <label for="current_count_display" class="form-label">Joriy son:</label>
                        <input type="text" class="form-control" id="current_count_display" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="new_count" class="form-label">Yangi son: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="new_count" id="new_count" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Sabab:</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="O'zgartirish sababini yozing..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-warning">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Menyu bo'yicha kerakli maxsulotlar</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('chef.minusproducts')}}" method="POST">
                @csrf
                <input type="hidden" name="kindgarid" value="{{ $kindgarden->id }}">
                <input type="hidden" name="dayid" value="{{ $day->id }}">
                <div class="modal-body">
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Og'irligi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                                @if(isset($all['yes']))
                                    <tr>
                                        <th scope="row">{{ ++$i }}</th>
                                        <td>{{ $all->product_name }}</td>
                                        <td style="width: 50px;"><input type="text" name="orders[{{ $all->id }}]"  placeholder="{{ $all->size_name }}" required></td>
                                    </tr>
                                @endif
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">x</button> -->
                    <button type="submit" class="btn add-age btn-primary text-white">Tasdiqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end -->
<div class="container-fluid px-4">
    <br>
    <!-- Bog'cha nomini ko'rsatish -->
    <h3><b>Bog'cha: {{ $kindgarden->kingar_name }}</b></h3>
    <div class="row g-3 my-2">
    @if(intval(date("H")) >= 3 and intval(date("H")) < 21 and $sendchildcount->count() == 0)
    <form method="POST" action="{{route('chef.sendnumbers')}}">
        @csrf
        <input type="hidden" name="kingar_id" value="{{ $kindgarden->id }}">
        <p><b>Бугунги болалар сонини юборинг</b></p>
        @foreach($kindgarden->age_range as $row)
            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm align-items-center rounded">
                    <p><b>{{ $row->age_name }}</b></p>
                    <div class="user-box">
                        <input type="number" name="agecount[{{ $row->id }}]" placeholder="Болалар сони" class="form-control" required>
                    </div>
                </div>
            </div>
        @endforeach
        <br>
        <button type="submit" class="btn btn-success" style="width: 100%;">Yuborish</button>
    </form>
    @else
        <p><b>Бугунги болалар сони қабул қилинди</b></p>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Bugungi kun uchun bolalar soni ko'rsatish va o'zgartirish -->
        @if($todayChildrenCount->count() > 0)
        <div class="row g-3 my-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($todayChildrenCount->groupBy('king_age_name_id') as $ageId => $history)
                                @php
                                    $latestRecord = $history->first();
                                    $ageRange = $latestRecord->ageRange;
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-primary h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-users me-2"></i>{{ $ageRange->age_name ?? 'Noma\'lum' }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <small class="text-muted">Oxirgi son:</small>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <span class="badge bg-success fs-6">{{ $latestRecord->new_children_count }}</span>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <small class="text-muted">O'zgartirgan:</small>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <small class="text-muted">{{ $latestRecord->changedBy->name ?? 'Noma\'lum' }}</small>
                                                </div>
                                            </div>
                                            @if($latestRecord->change_reason)
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <small class="text-muted">Sabab:</small>
                                                    <p class="mb-0 small text-muted">{{ $latestRecord->change_reason }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-light">
                                            <button class="btn btn-sm btn-outline-primary w-100" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editChildrenCountModal"
                                                    data-age-id="{{ $ageId }}"
                                                    data-age-name="{{ $ageRange->age_name ?? 'Noma\'lum' }}"
                                                    data-current-count="{{ $latestRecord->new_children_count }}"
                                                    title="Bolalar sonini o'zgartirish">
                                                <i class="fas fa-edit me-1"></i> O'zgartirish
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
    </div>
    @if(isset($inproducts[0]))
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <!-- <form action="/nextdaysecondmenuPDF/{{ $kindgarden->id }}" method="get" download> -->
                    <!-- <p><b>Maxsulotlarni qabul qilish</b></p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productsshowModal" style="width: 100%;">Maxsulotlar</button> -->
                <!-- </form> -->
            </div>
        </div>
    </div>
    @endif
    @if(intval(date("H")) > 9 || (intval(date("H")) == 9 && intval(date("i")) >= 30))
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <!-- <form action="/activsecondmenuPDF/{{ $day->id }}/{{ $kindgarden->id }}" method="get"> -->
                    <p><b>Haqiqiy Menyu: </b>sana: {{ $day->day_number.".".$day->month_name.".".$day->year_name }}</p>
                    <p><i>Eslatma: menyu har kuni soat 10 dan keyin yangilanadi</i></p>
                    <!-- Shu joyda menyu rasmi  ko'rinsin -->
                    <p><small class="text-muted"><i class="fas fa-info-circle"></i> PDF faylni yuklab olish uchun tugmani bosing</small></p>
                    <div class="d-flex gap-2">
                        <a href="/activmenuPDF/{{ $day->id }}/{{ $kindgarden->id }}/4" 
                            class="btn btn-success d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            download>
                                <i class="fas fa-download"></i> Yuklab olish
                        </a>
                        <button type="button" 
                            class="btn btn-info d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            onclick="showActiveMenuInline('{{ $day->id }}', '{{ $kindgarden->id }}', '{{ $day->day_number.".".$day->month_name.".".$day->year_name }}')">
                            <i class="fas fa-eye"></i> Ko'rish
                        </button>
                        
                    </div>
                    
                    <!-- Inline menyu ko'rinishi -->
                    <div id="activeMenuContainer" style="display: none; margin-top: 20px;">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-utensils me-2"></i>Haqiqiy menyu ko'rinishi
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-light" onclick="hideActiveMenu()">
                                    <i class="fas fa-times"></i> Yopish
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div id="activeMenuImageContainer" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Yuklanmoqda...</span>
                                    </div>
                                    <p class="mt-2">Menyu yuklanmoqda...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
    
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <!-- <form action="/nextdaysecondmenuPDF/{{ $kindgarden->id }}" method="get" download> -->
                @foreach($kindgarden->age_range as $row)
                    <p><b>Keyingi ish kuni uchun taomnoma: {{$row->age_name}}</b></p>
                    <p><small class="text-muted"><i class="fas fa-info-circle"></i> PDF faylni yuklab olish uchun tugmani bosing</small></p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="/nextdaymenuPDF/{{ $kindgarden->id }}/{{ $row->id }}" 
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            download>
                                <i class="fas fa-download"></i> Yuklab olish
                        </a>
                        <button type="button" 
                            class="btn btn-info d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            onclick="showMenuInline('{{ $kindgarden->id }}', '{{ $row->id }}', '{{ $row->age_name }}')">
                            <i class="fas fa-eye"></i> Ko'rish
                        </button>
                        <!-- <button type="button" 
                            class="btn btn-info d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            onclick="shareTaxminiyMenuToTelegram('{{ $kindgarden->kingar_name }}', '/nextdaysecondmenuPDF/{{ $kindgarden->id }}')">
                            <i class="fab fa-telegram"></i> Share
                        </button> -->
                    </div>
                    
                    <!-- Inline taxminiy menyu ko'rinishi -->
                    <div id="menuContainer_{{ $row->id }}" style="display: none; margin-top: 20px;">
                        <div class="card">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>Taxminiy menyu - {{ $row->age_name }}
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-light" onclick="hideMenu('{{ $row->id }}')">
                                    <i class="fas fa-times"></i> Yopish
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div id="menuImageContainer_{{ $row->id }}" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Yuklanmoqda...</span>
                                    </div>
                                    <p class="mt-2">Menyu yuklanmoqda...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <!-- </form> -->
                @if($bool->count() == 0)
                <!-- <form action="#" method="get"> -->
                    <!--<br>-->
                    <!--<p><b>Omborxona: </b>Omborxonadan olingan maxsulot ro'yxatini yuboring. </p>-->
                    <!--<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#Modalsadd" style="width: 100%;">Maxsulotlar</button>-->
                <!-- </form> -->
                @endif
                <p></p>
                <p><b>Nakladnoy, non va sud maxsulotlari </b></p>
                <p><small class="text-muted"><i class="fas fa-info-circle"></i> PDF faylni yuklab olish uchun tugmani bosing</small></p>
                <div class="d-flex gap-2 mt-2">
                    <a href="/nextdaysomenakladnoyPDF/{{ $kindgarden->id }}" 
                        class="btn btn-warning d-flex align-items-center justify-content-center gap-2" 
                        style="width: 100%;" 
                        download>
                        <i class="fas fa-file-invoice"></i> Yuklab olish
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    

    
    // Telegram Web App orqali yuborish
    function shareToTelegramWebApp(file, fileName) {
        // Telegram Web App mavjud bo'lsa
        if (window.Telegram && window.Telegram.WebApp) {
            window.Telegram.WebApp.sendData(JSON.stringify({
                type: 'file',
                fileName: fileName,
                fileData: file
            }));
        } else {
            // Fallback: Telegram bot orqali
            shareToTelegramBot(file, fileName);
        }
    }
    
    // Telegram bot orqali yuborish (agar Web App ishlamasa)
    function shareToTelegramBot(file, fileName) {
        // Faylni base64 ga o'tkazish
        var reader = new FileReader();
        reader.onload = function() {
            var base64Data = reader.result.split(',')[1];
            
            // Telegram bot API orqali yuborish
            var botToken = 'YOUR_BOT_TOKEN'; // Bot token ni qo'shish kerak
            var chatId = 'YOUR_CHAT_ID'; // Chat ID ni qo'shish kerak
            
            var formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('document', file);
            formData.append('caption', fileName);
            
            fetch(`https://api.telegram.org/bot${botToken}/sendDocument`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    showNotification('Fayl Telegram ga yuborildi!', 'success');
                } else {
                    throw new Error('Telegram API xatosi');
                }
            })
            .catch(error => {
                console.error('Telegram bot xatosi:', error);
                // Eng oxirgi fallback: faylni yuklab olish
                downloadAndShare(file, fileName);
            });
        };
        reader.readAsDataURL(file);
    }
    

    

    
    // Notification ko'rsatish funksiyasi
    function showNotification(message, type) {
        // Mavjud notification ni o'chirish
        $('.share-notification').remove();
        
        var alertClass = 'alert-info';
        var icon = 'fas fa-info-circle';
        
        if (type === 'success') {
            alertClass = 'alert-success';
            icon = 'fas fa-check-circle';
        } else if (type === 'error') {
            alertClass = 'alert-danger';
            icon = 'fas fa-exclamation-triangle';
        }
        
        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show share-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<i class="' + icon + '" style="margin-right: 8px;"></i>' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>');
        
        $('body').append(notification);
        
        // Success va error xabarlarini uzoqroq ko'rsatish
        var timeout = (type === 'success' || type === 'error') ? 6000 : 4000;
        
        setTimeout(function() {
            notification.fadeOut();
        }, timeout);
    }
    
    // Mobile qurilmalar uchun Web Share API
    function shareToMobile(fileUrl, fileName) {
        if (navigator.share) {
            navigator.share({
                title: fileName,
                text: fileName,
                url: window.location.origin + fileUrl
            })
            .then(() => {
                setTimeout(() => {
                    showNotification('Fayl muvaffaqiyatli yuborildi!', 'success');
                }, 1000);
            })
            .catch((error) => {
                console.log('Share xatosi:', error);
                // Fallback: Telegram ga yuborish
                shareToTelegram(fileUrl, fileName);
            });
        } else {
            // Web Share API mavjud emas bo'lsa, Telegram ga yuborish
            shareToTelegram(fileUrl, fileName);
        }
    }
    
    // Haqiqiy menyu share funksiyasi
    function shareMenuToTelegram(menuDate, fileUrl) {
        // Menyu uchun xabar tayyorlash
        var message = '🍽️ *Oshpazlar uchun haqiqiy menyu*\n\n';
        message += '📅 Sana: ' + menuDate + '\n';
        message += '🏫 Bog\'cha: ' + '{{ $kindgarden->kingar_name }}' + '\n\n';
        message += '📋 *Menyu tarkibi:*\n';
        message += '• Non va sut mahsulotlari\n';
        message += '• Sabzavotlar va mevalar\n';
        message += '• Go\'sht va baliq mahsulotlari\n';
        message += '• Yog\'lar va qandolat mahsulotlari\n\n';
        message += '📞 Bog\'lanish: +998 XX XXX XX XX\n';
        message += '🔗 Fayl: ' + window.location.origin + fileUrl;
        
        // Telegram share URL yaratish
        var telegramUrl = 'https://t.me/share/url?url=' + encodeURIComponent(window.location.origin + fileUrl) + '&text=' + encodeURIComponent(message);
        
        // Yangi oynada ochish
        var newWindow = window.open(telegramUrl, '_blank', 'width=600,height=400');
        
        if (newWindow) {
            showNotification('Telegram ochildi! Haqiqiy menyuni yuborish uchun "Send" tugmasini bosing.', 'success');
        } else {
            // Agar popup bloklangan bo'lsa
            showNotification('Popup bloklangan! Iltimos, brauzer sozlamalarini tekshiring.', 'error');
            
            // Fallback: faylni yuklab olish
            setTimeout(() => {
                window.open(window.location.origin + fileUrl, '_blank');
            }, 2000);
        }
    }
    
    // Taxminiy menyu share funksiyasi
    function shareTaxminiyMenuToTelegram(bogchaName, fileUrl) {
        // Taxminiy menyu uchun xabar tayyorlash
        var message = '📋 *Oshpazlar uchun taxminiy menyu*\n\n';
        message += '🏫 Bog\'cha: ' + bogchaName + '\n';
        message += '📅 Keyingi kun uchun\n\n';
        message += '📋 *Taxminiy menyu tarkibi:*\n';
        message += '• Non va sut mahsulotlari\n';
        message += '• Sabzavotlar va mevalar\n';
        message += '• Go\'sht va baliq mahsulotlari\n';
        message += '• Yog\'lar va qandolat mahsulotlari\n\n';
        message += '⚠️ *Eslatma:* Bu taxminiy menyu, haqiqiy menyu kun boshida tasdiqlanadi\n';
        message += '📞 Bog\'lanish: +998 XX XXX XX XX\n';
        message += '🔗 Fayl: ' + window.location.origin + fileUrl;
        
        // Telegram share URL yaratish
        var telegramUrl = 'https://t.me/share/url?url=' + encodeURIComponent(window.location.origin + fileUrl) + '&text=' + encodeURIComponent(message);
        
        // Yangi oynada ochish
        var newWindow = window.open(telegramUrl, '_blank', 'width=600,height=400');
        
        if (newWindow) {
            showNotification('Telegram ochildi! Taxminiy menyuni yuborish uchun "Send" tugmasini bosing.', 'success');
        } else {
            // Agar popup bloklangan bo'lsa
            showNotification('Popup bloklangan! Iltimos, brauzer sozlamalarini tekshiring.', 'error');
            
            // Fallback: faylni yuklab olish
            setTimeout(() => {
                window.open(window.location.origin + fileUrl, '_blank');
            }, 2000);
        }
    }
    
    
    // Bolalar sonini o'zgartirish modal
    $('#editChildrenCountModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var ageId = button.data('age-id');
        var ageName = button.data('age-name');
        var currentCount = button.data('current-count');
        
        var modal = $(this);
        modal.find('#age_id').val(ageId);
        modal.find('#age_name_display').val(ageName);
        modal.find('#current_count_display').val(currentCount);
        modal.find('#new_count').val(currentCount);
        modal.find('#reason').val('');
    });
    
    // Haqiqiy menyu inline ko'rinishini ko'rsatish
    function showActiveMenuInline(dayId, gardenId, menuDate) {
        // Container ni ko'rsatish
        var container = document.getElementById('activeMenuContainer');
        container.style.display = 'block';
        container.classList.add('menu-container');
        
        // Loading ko'rsatish
        var imageContainer = document.getElementById('activeMenuImageContainer');
        imageContainer.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <p class="mt-2">Haqiqiy menyu yuklanmoqda...</p>
        `;
        
        // Rasm URL yaratish
        var imageUrl = '/activmenuPDF/' + dayId + '/' + gardenId + '/4/image';
        
        // Rasmni yuklash
        var img = new Image();
        img.onload = function() {
            imageContainer.innerHTML = `
                <div class="menu-inline-container">
                    <img src="${imageUrl}" 
                         class="menu-inline-image" 
                         alt="Haqiqiy menyu" 
                         onclick="zoomImageInline(this, 'Haqiqiy menyu - ${menuDate}')">
                    <div class="mt-2 p-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Rasmni kattalashtirish uchun ustiga bosing
                        </small>
                    </div>
                </div>
            `;
            
            // PDF yuklab olish URL ni saqlash
            window.currentActiveMenuPdfUrl = '/activmenuPDF/' + dayId + '/' + gardenId + '/4';
        };
        
        img.onerror = function() {
            imageContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Haqiqiy menyu rasmi yuklanmadi. Iltimos, qaytadan urinib ko'ring.
                </div>
            `;
        };
        
        img.src = imageUrl;
        
        // Sahifani rasmga scroll qilish
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Haqiqiy menyuni yashirish
    function hideActiveMenu() {
        var container = document.getElementById('activeMenuContainer');
        container.style.display = 'none';
    }
    
    // Taxminiy menyu inline ko'rinishini ko'rsatish
    function showMenuInline(gardenId, ageId, ageName) {
        // Container ni ko'rsatish
        var container = document.getElementById('menuContainer_' + ageId);
        container.style.display = 'block';
        container.classList.add('menu-container');
        
        // Loading ko'rsatish
        var imageContainer = document.getElementById('menuImageContainer_' + ageId);
        imageContainer.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <p class="mt-2">Taxminiy menyu yuklanmoqda...</p>
        `;
        
        // Rasm URL yaratish
        var imageUrl = '/nextdaymenuPDF/' + gardenId + '/' + ageId + '/image';
        
        // Rasmni yuklash
        var img = new Image();
        img.onload = function() {
            imageContainer.innerHTML = `
                <div class="menu-inline-container">
                    <img src="${imageUrl}" 
                         class="menu-inline-image" 
                         alt="Taxminiy menyu - ${ageName}" 
                         onclick="zoomImageInline(this, 'Taxminiy menyu - ${ageName}')">
                    <div class="mt-2 p-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Rasmni kattalashtirish uchun ustiga bosing
                        </small>
                    </div>
                </div>
            `;
            
            // PDF yuklab olish URL ni saqlash
            window.currentMenuPdfUrl = '/nextdaymenuPDF/' + gardenId + '/' + ageId;
        };
        
        img.onerror = function() {
            imageContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Taxminiy menyu rasmi yuklanmadi. Iltimos, qaytadan urinib ko'ring.
                </div>
            `;
        };
        
        img.src = imageUrl;
        
        // Sahifani rasmga scroll qilish
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    // Taxminiy menyuni yashirish
    function hideMenu(ageId) {
        var container = document.getElementById('menuContainer_' + ageId);
        container.style.display = 'none';
    }
    
    // Inline rasmni kattalashtirish
    function zoomImageInline(img, title) {
        // Mavjud zoom modal mavjudligini tekshirish
        var existingModal = document.getElementById('imageZoomModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Yangi zoom modal yaratish
        var zoomModal = document.createElement('div');
        zoomModal.id = 'imageZoomModal';
        zoomModal.className = 'modal fade zoom-modal';
        zoomModal.setAttribute('tabindex', '-1');
        zoomModal.innerHTML = `
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bg-light">
                    <div class="modal-header border-0 bg-primary">
                        <h5 class="modal-title text-white">${title} - Katta ko'rinish</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center justify-content-center p-0 bg-light">
                        <img src="${img.src}" 
                             class="img-fluid" 
                             alt="${title} - Katta ko'rinish"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                        <button type="button" class="btn btn-primary" onclick="downloadCurrentMenu()">
                            <i class="fas fa-download"></i> PDF Yuklab olish
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Modal ni body ga qo'shish
        document.body.appendChild(zoomModal);
        
        // Modal ni ko'rsatish
        var modal = new bootstrap.Modal(zoomModal);
        modal.show();
        
        // Modal yopilganda uni o'chirish
        zoomModal.addEventListener('hidden.bs.modal', function() {
            zoomModal.remove();
        });
    }
    
    // Joriy menyuni yuklab olish
    function downloadCurrentMenu() {
        if (window.currentMenuPdfUrl) {
            window.open(window.currentMenuPdfUrl, '_blank');
        } else if (window.currentActiveMenuPdfUrl) {
            window.open(window.currentActiveMenuPdfUrl, '_blank');
        } else {
            showNotification('PDF fayl topilmadi!', 'error');
        }
    }
</script>
@if(session('status'))
<script> 
    // alert('{{ session("status") }}');
    swal({
        title: "Ajoyib!",
        text: "{{ session('status') }}",
        icon: "success",
        button: "ok",
    });
</script>
@endif
@endsection