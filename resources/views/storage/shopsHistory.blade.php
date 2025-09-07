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
    }
    
    .shop-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .order-item {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .order-item:last-child {
        margin-bottom: 0;
    }
    
    .product-name {
        font-weight: 600;
        color: #495057;
    }
    
    .product-quantity {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .no-orders {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px;
    }
    
    .shop-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
    }
    
    .date-navigation {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
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
        <div class="year first-text fw-bold">
            {{ $day->year->year_name }}
        </div>
        <div class="month">
            @if($day->year->id != 1)
                <a href="/storage/shops-history/{{ $day->id-1 }}/" class="month__item">{{ $day->year->year_name - 1 }}</a>
            @endif
            @foreach($months as $month)
                <a href="/storage/shops-history/{{ $day->id }}" 
                   class="month__item {{ ( $month->month_active == 1 ) ? 'active first-text' : 'second-text' }} fw-bold">
                   {{ $month->month_name }}
                </a>
            @endforeach
            <a href="/storage/shops-history/{{ $day->id+1 }}/" class="month__item">{{ $day->year->year_name + 1 }}</a>
        </div>
        <div class="day">
            <!-- -->
        </div>
    </div>

    <!-- Yetkazuvchilar jadvali -->
    <div class="row">
        @forelse($shops as $shop)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shop-card">
                <div class="card-header shop-header">
                    <h6 class="mb-0">
                        <i class="fas fa-store me-2"></i>{{ $shop->shop_name }}
                    </h6>
                    @if($shop->kindgarden)
                        <small class="opacity-75">{{ "" }}</small>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($orders[$shop->id]) && $orders[$shop->id]->count() > 0)
                        <div class="mb-2">
                            <span class="badge bg-success">
                                {{ $orders[$shop->id]->count() }} ta buyurtma
                            </span>
                        </div>
                        
                        @foreach($orders[$shop->id] as $order)
                        <div class="order-item">
                            <div class="product-name">
                                {{ $order->product->product_name ?? 'Noma\'lum mahsulot' }}
                            </div>
                            <div class="product-quantity">
                                <i class="fas fa-weight me-1"></i>
                                {{ number_format($order->weight, 2) }} {{ $order->product->unit ?? 'kg' }}
                            </div>
                            @if($order->created_at)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $order->created_at->format('H:i') }}
                                </small>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="no-orders">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <br>Bu kunda buyurtma yo'q
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <h5>Hech qanday yetkazuvchi topilmadi</h5>
                <p>Bu kunda faol yetkazuvchilar mavjud emas.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Umumiy statistika -->
    @if($shops->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Kunlik statistika
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 class="text-primary">{{ $shops->count() }}</h4>
                                <small class="text-muted">Jami yetkazuvchilar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 class="text-success">
                                    {{ collect($orders)->filter(function($order) { return $order->count() > 0; })->count() }}
                                </h4>
                                <small class="text-muted">Faol yetkazuvchilar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 class="text-info">
                                    {{ collect($orders)->sum(function($order) { return $order->count(); }) }}
                                </h4>
                                <small class="text-muted">Jami buyurtmalar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h4 class="text-warning">
                                    {{ number_format(collect($orders)->flatten()->sum('weight'), 2) }} kg
                                </h4>
                                <small class="text-muted">Jami og'irlik</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
    // Sana tanlash funksiyalari
    $(document).ready(function() {
        // Faol sana ustunini belgilash
        $('.day__item.active').addClass('fw-bold');
        
        // Hover effektlari
        $('.shop-card').hover(
            function() {
                $(this).find('.shop-header').css('background', 'linear-gradient(135deg, #764ba2 0%, #667eea 100%)');
            },
            function() {
                $(this).find('.shop-header').css('background', 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)');
            }
        );
    });
</script>
@endsection 