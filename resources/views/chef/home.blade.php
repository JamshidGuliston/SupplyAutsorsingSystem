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

<!-- Taxminiy menyu rasm ko'rinishi uchun modal -->
<div class="modal fade" id="menuPreviewModal" tabindex="-1" aria-labelledby="menuPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="menuPreviewModalLabel">Taxminiy menyu ko'rinishi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding: 10px; overflow: auto;">
                <div id="menuPreviewContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yuklanmoqda...</span>
                    </div>
                    <p class="mt-2">Menyu yuklanmoqda...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                <button type="button" class="btn btn-primary" onclick="downloadMenuFromPreview()">
                    <i class="fas fa-download"></i> Yuklab olish
                </button>
            </div>
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
    <a href="/chef/home" ><i class="fas fa-tachometer-alt me-3"></i>Qayta yuklash</a>
    <br>
    <!-- Bog'cha nomini ko'rsatish -->
    <h3><b>Bog'cha: {{ $kindgarden->kingar_name }}</b></h3>
    <div class="row g-3 my-2">
    @if(intval(date("H")) >= 8 and intval(date("H")) < 16 and $sendchildcount->count() == 0)
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
                    <p><small class="text-muted"><i class="fas fa-info-circle"></i> PDF faylni yuklab olish uchun tugmani bosing</small></p>
                    <div class="d-flex gap-2">
                        <a href="/activmenuPDF/{{ $day->id }}/{{ $kindgarden->id }}" 
                            class="btn btn-success d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            download>
                                <i class="fas fa-download"></i> Yuklab olish
                        </a>
                        <button type="button" 
                            class="btn btn-info d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            onclick="showActiveMenuPreview('{{ $day->id }}', '{{ $kindgarden->id }}', '{{ $day->day_number.".".$day->month_name.".".$day->year_name }}')">
                            <i class="fas fa-eye"></i> Ko'rish
                        </button>
                        
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
                            onclick="showMenuPreview('{{ $kindgarden->id }}', '{{ $row->id }}', '{{ $row->age_name }}')">
                            <i class="fas fa-eye"></i> Ko'rish
                        </button>
                        <!-- <button type="button" 
                            class="btn btn-info d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            onclick="shareTaxminiyMenuToTelegram('{{ $kindgarden->kingar_name }}', '/nextdaysecondmenuPDF/{{ $kindgarden->id }}')">
                            <i class="fab fa-telegram"></i> Share
                        </button> -->
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
    
    // Taxminiy menyu rasm ko'rinishini ko'rsatish
    function showMenuPreview(gardenId, ageId, ageName) {
        // Modal oynani ochish
        var modal = new bootstrap.Modal(document.getElementById('menuPreviewModal'));
        modal.show();
        
        // Modal sarlavhasini yangilash
        document.getElementById('menuPreviewModalLabel').textContent = 'Taxminiy menyu ko\'rinishi - ' + ageName;
        
        // Loading ko'rsatish
        var container = document.getElementById('menuPreviewContainer');
        container.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <p class="mt-2">Menyu yuklanmoqda...</p>
        `;
        
        // Rasm URL yaratish
        var imageUrl = '/nextdaymenuPDF/' + gardenId + '/' + ageId + '/image';
        
        // Rasmni yuklash
        var img = new Image();
        img.onload = function() {
            container.innerHTML = `         
                <div class="menu-preview-container">
                    <img src="${imageUrl}" 
                         class="img-fluid menu-preview-image" 
                         alt="Taxminiy menyu" 
                         style="width: 100%; height: auto; border: none; border-radius: 4px;"
                         onclick="zoomImage(this)">
                    <div class="mt-2">
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
            container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Rasm yuklanmadi. Iltimos, qaytadan urinib ko'ring.
                </div>
            `;
        };
        
        img.src = imageUrl;
    }
    
    // Rasmni kattalashtirish
    function zoomImage(img) {
        // Mavjud zoom modal mavjudligini tekshirish
        var existingModal = document.getElementById('imageZoomModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Yangi zoom modal yaratish
        var zoomModal = document.createElement('div');
        zoomModal.id = 'imageZoomModal';
        zoomModal.className = 'modal fade';
        zoomModal.setAttribute('tabindex', '-1');
        zoomModal.innerHTML = `
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bg-light">
                    <div class="modal-header border-0 bg-primary">
                        <h5 class="modal-title text-white">Taxminiy menyu - Katta ko'rinish</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center justify-content-center p-0 bg-light">
                        <img src="${img.src}" 
                             class="img-fluid" 
                             alt="Taxminiy menyu - Katta ko'rinish"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                        <button type="button" class="btn btn-primary" onclick="downloadMenuFromPreview()">
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
    
    // PDF yuklab olish
    function downloadMenuFromPreview() {
        if (window.currentMenuPdfUrl) {
            window.open(window.currentMenuPdfUrl, '_blank');
        } else {
            showNotification('PDF fayl topilmadi!', 'error');
        }
    }
    
    // Haqiqiy menyu rasm ko'rinishini ko'rsatish
    function showActiveMenuPreview(dayId, gardenId, menuDate) {
        // Modal oynani ochish
        var modal = new bootstrap.Modal(document.getElementById('menuPreviewModal'));
        modal.show();
        
        // Modal sarlavhasini yangilash
        document.getElementById('menuPreviewModalLabel').textContent = 'Haqiqiy menyu ko\'rinishi - ' + menuDate;
        
        // Loading ko'rsatish
        var container = document.getElementById('menuPreviewContainer');
        container.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yuklanmoqda...</span>
            </div>
            <p class="mt-2">Haqiqiy menyu yuklanmoqda...</p>
        `;
        
        // Rasm URL yaratish
        var imageUrl = '/activmenuPDF/' + dayId + '/' + gardenId + '/image';
        
        // Rasmni yuklash
        var img = new Image();
        img.onload = function() {
            container.innerHTML = `
                <div class="menu-preview-container">
                    <img src="${imageUrl}" 
                         class="img-fluid menu-preview-image" 
                         alt="Haqiqiy menyu" 
                         style="width: 100%; height: auto; border: none; border-radius: 4px;"
                         onclick="zoomImage(this)">
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Rasmni kattalashtirish uchun ustiga bosing
                        </small>
                    </div>
                </div>
            `;
            
            // PDF yuklab olish URL ni saqlash
            window.currentMenuPdfUrl = '/activmenuPDF/' + dayId + '/' + gardenId;
        };
        
        img.onerror = function() {
            container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Haqiqiy menyu rasmi yuklanmadi. Iltimos, qaytadan urinib ko'ring.
                </div>
            `;
        };
        
        img.src = imageUrl;
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