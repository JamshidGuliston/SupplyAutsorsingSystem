@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="py-4 px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-4">
            <a href="#">
                <i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
            <b>{{ $shop['shop_name'] }}</b>
        </div>
        <div class="col-md-3">
            
        </div>
        <div class="col-md-3" style="text-align: center;">
            <b>PDF </b>
            <a href="/technolog/nextdayshoppdf/{{ $shop['id'] }}" target="_blank" title="PDF ko'chirish">
                <i class="far fa-file-pdf" style="color: #dc3545; font-size: 18px; margin-right: 10px;"></i>
            </a>
            <b>Excel </b>
            <a href="/technolog/nextdayshopexcel/{{ $shop['id'] }}" title="Excel ko'chirish (formatlangan)">
                <i class="far fa-file-excel" style="color: #198754; font-size: 18px;"></i>
            </a>
        </div>
        <div class="col-md-2" style="text-align: right;">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                PDF va CSV fayllar regionlar bo'yicha guruhlangan
            </small>
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                @foreach($shop->product as $age)
                <th scope="col" colspan="2">{{ $age->product_name}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <?php 
                $tr =1; 
                $allm = array();
            ?>
            @foreach($shopproducts as $row)
            <tr>
                <th scope="row">{{ $tr++ }}</th>
                <td>{{ $row['name'] }}</td>
                @foreach($shop->product as $age)
                <?php
                    $result = $row[$age->id];
                    if($age->size_name_id == 3 or $age->size_name_id == 2){ 
                        $result = round($result);
                    }
                    else{
                        $result = round($result, 1);
                    }
                ?>
                    <td scope="col"><?php printf("%01.1f", $result); ?></td>
                    <td scope="col"><?php printf("%01.3f", $row[$age->id]); ?></td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

@section('script')
<script>
function confirmOrder() {
    var shopName = "{{ $shop['shop_name'] }}";
    var message = "Siz " + shopName + " uchun order yaratmoqchimisiz?\n\n" +
                  "Bu amal:\n" +
                  "• order_product jadvaliga yangi yozuv qo'shadi\n" +
                  "• order_product_structure jadvaliga barcha maxsulotlarni qo'shadi\n" +
                  "• Hozirgi kundagi barcha maxsulotlar hisoblanadi\n\n" +
                  "Davom etishni xohlaysizmi?";
    
    return confirm(message);
}

// Form submit oldidan qo'shimcha tekshirish
document.getElementById('orderForm').addEventListener('submit', function(e) {
    var shopName = "{{ $shop['shop_name'] }}";
    var finalMessage = "⚠️ OGOHLANTIRISH ⚠️\n\n" +
                       "Siz " + shopName + " uchun order yaratmoqdasiz!\n\n" +
                       "Bu amalni bekor qilish mumkin emas.\n" +
                       "Order yaratilgandan keyin uni tahrirlash yoki o'chirish mumkin emas.\n\n" +
                       "Rostdan ham davom etmoqchimisiz?";
    
    if (!confirm(finalMessage)) {
        e.preventDefault();
        return false;
    }
    
    // Loading ko'rsatish
    var button = e.target.querySelector('button[type="submit"]');
    var icon = button.querySelector('i');
    var originalIcon = icon.className;
    
    button.disabled = true;
    icon.className = 'fas fa-spinner fa-spin';
    button.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: #28a745; font-size: 18px;"></i>';
    
    // 2 soniyadan keyin form yuborish
    setTimeout(function() {
        button.disabled = false;
        icon.className = originalIcon;
        button.innerHTML = '<i class="fas fa-paper-plane" style="color: #28a745; font-size: 18px;"></i>';
    }, 2000);
});

// Excel yuklash holatini kuzatish
document.querySelector('a[href*="nextdayshopexcel"]').addEventListener('click', function(e) {
    var icon = this.querySelector('i');
    var originalClass = icon.className;
    
    // Loading animatsiyasi
    icon.className = 'fas fa-spinner fa-spin';
    
    // 3 soniyadan keyin asl holatga qaytarish
    setTimeout(function() {
        icon.className = originalClass;
    }, 3000);
});
</script>
@endsection