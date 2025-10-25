@extends('layouts.app')

@section('css')
<style>
    .history-card {
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    
    .history-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .change-indicator {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: bold;
    }
    
    .change-increase {
        background-color: #d4edda;
        color: #155724;
    }
    
    .change-decrease {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .change-same {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }
    
    .timeline-item:last-child::before {
        bottom: 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: 6px;
        top: 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #007bff;
    }
</style>
@endsection

@section('leftmenu')
@include('chef.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><b>Bolalar soni o'zgartirish tarixi</b></h3>
                <a href="{{ route('chef.home') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($history->count() > 0)
                <div class="row">
                    @foreach($history as $record)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card history-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title text-primary mb-0">
                                            <i class="fas fa-child"></i> {{ $record->ageRange->age_name ?? 'Noma\'lum' }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $record->changed_at->format('H:i') }}
                                        </small>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Eski son:</span>
                                            <strong class="text-secondary">{{ $record->old_children_count ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Yangi son:</span>
                                            <strong class="text-primary">{{ $record->new_children_count }}</strong>
                                        </div>
                                    </div>
                                    
                                    @if($record->old_children_count !== null)
                                        @php
                                            $difference = $record->new_children_count - $record->old_children_count;
                                        @endphp
                                        <div class="mb-2">
                                            <span class="change-indicator 
                                                @if($difference > 0) change-increase
                                                @elseif($difference < 0) change-decrease
                                                @else change-same @endif">
                                                @if($difference > 0)
                                                    <i class="fas fa-arrow-up"></i> +{{ $difference }}
                                                @elseif($difference < 0)
                                                    <i class="fas fa-arrow-down"></i> {{ $difference }}
                                                @else
                                                    <i class="fas fa-equals"></i> O'zgarish yo'q
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $record->changedBy->name ?? 'Noma\'lum' }}
                                        </small>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $record->changed_at->format('d.m.Y') }}
                                        </small>
                                    </div>
                                    
                                    @if($record->change_reason)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-comment"></i> {{ $record->change_reason }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $history->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-history fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Hali hech qanday o'zgartirish tarixi yo'q</h5>
                    <p class="text-muted">Bolalar sonini o'zgartirganingizda, bu yerda tarix ko'rinadi</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Real-time yangilanish (agar kerak bo'lsa)
    // setInterval(function() {
    //     location.reload();
    // }, 30000); // 30 soniyada bir marta yangilash
</script>
@endsection

