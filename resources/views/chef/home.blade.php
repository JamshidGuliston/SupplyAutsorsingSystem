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
                        <a href="/activsecondmenuPDF/{{ $day->id }}/{{ $kindgarden->id }}" 
                            class="btn btn-success d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            download>
                                <i class="fas fa-download"></i> Yuklab olish
                        </a>
                    </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
    
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <!-- <form action="/nextdaysecondmenuPDF/{{ $kindgarden->id }}" method="get" download> -->
                    <p><b>Taxminiy menyu: </b></p>
                    <p><small class="text-muted"><i class="fas fa-info-circle"></i> PDF faylni yuklab olish uchun tugmani bosing</small></p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="/nextdaysecondmenuPDF/{{ $kindgarden->id }}" 
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-2" 
                            style="width: 100%;" 
                            download>
                                <i class="fas fa-download"></i> Yuklab olish
                        </a>
                    </div>
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