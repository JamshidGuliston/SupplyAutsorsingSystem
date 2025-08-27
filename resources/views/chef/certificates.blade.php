@extends('layouts.app')

@section('css')
<style>
    .certificate-card {
        height: 100%;
        transition: transform 0.2s;
    }
    .certificate-card:hover {
        transform: translateY(-5px);
    }
    .certificate-image {
        max-width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        transition: opacity 0.3s;
    }
    .certificate-image:hover {
        opacity: 0.9;
    }
    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }
    .status-active {
        background-color: #28a745;
        color: white;
    }
    .status-warning {
        background-color: #fd7e14;
        color: white;
    }
    .product-badge {
        display: inline-block;
        padding: 3px 8px;
        margin: 2px;
        background-color: #17a2b8;
        color: white;
        border-radius: 12px;
        font-size: 11px;
    }
    .alert-expiring {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 5px;
    }
    .certificate-footer {
        border-top: 1px solid #eee;
        padding-top: 10px;
        margin-top: 10px;
    }
    .days-left {
        font-size: 0.9rem;
        color: #dc3545;
    }
    .pdf-link {
        color: #dc3545;
        text-decoration: none;
    }
    .pdf-link:hover {
        text-decoration: underline;
    }

    /* Modal styles */
    .modal-image {
        max-width: 100%;
        height: auto;
    }
    .image-modal .modal-content {
        background-color: transparent;
        border: none;
    }
    .image-modal .modal-header {
        border: none;
        padding: 0.5rem;
        position: absolute;
        right: 0;
        z-index: 1;
    }
    .image-modal .btn-close {
        background-color: white;
        opacity: 0.8;
        padding: 0.5rem;
        margin: 0;
    }
    .image-modal .btn-close:hover {
        opacity: 1;
    }
    .image-modal .modal-body {
        padding: 0;
    }
</style>
@endsection

@section('leftmenu')
    @include('chef.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4">Sertifikatlar</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('chef.home') }}">Bosh sahifa</a></li>
                <li class="breadcrumb-item active">Sertifikatlar</li>
            </ol>

            @if($expiringCertificates->isNotEmpty())
                <div class="alert-expiring mb-4">
                    <h5 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Diqqat! Quyidagi sertifikatlar muddati yaqinlashmoqda:</h5>
                    <ul class="mb-0">
                        @foreach($expiringCertificates as $cert)
                            <li>
                                <strong>{{ $cert->name }}</strong> ({{ $cert->certificate_number }}) - 
                                <span class="text-danger">{{ $cert->days_left }} kun qoldi</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                @forelse($certificates as $certificate)
                    <div class="col-md-4 mb-4">
                        <div class="card certificate-card">
                            <div class="card-body">
                                <span class="status-badge {{ $certificate->status['class'] }}">
                                    {{ $certificate->status['text'] }}
                                </span>
                                
                                @if($certificate->image_file)
                                    <img src="{{ Storage::url($certificate->image_file) }}" 
                                         alt="{{ $certificate->name }}" 
                                         class="certificate-image mb-3"
                                         onclick="showImageModal(this.src, '{{ $certificate->name }}')">
                                @else
                                    <div class="certificate-image mb-3 bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-certificate fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <h5 class="card-title">{{ $certificate->name }}</h5>
                                <p class="card-text text-muted mb-2">
                                    <small>Sertifikat raqami: {{ $certificate->certificate_number }}</small>
                                </p>

                                @if($certificate->description)
                                    <p class="card-text">{{ $certificate->description }}</p>
                                @endif

                                <div class="mb-3">
                                    <strong class="d-block mb-2">Maxsulotlar:</strong>
                                    @foreach($certificate->products as $product)
                                        <span class="product-badge">{{ $product->product_name }}</span>
                                    @endforeach
                                </div>

                                <div class="certificate-footer">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt"></i> 
                                                {{ $certificate->end_date->format('d.m.Y') }}
                                            </small>
                                            @if($certificate->days_left <= 30)
                                                <small class="days-left d-block">
                                                    <i class="fas fa-clock"></i> 
                                                    {{ $certificate->days_left }} kun qoldi
                                                </small>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ Storage::url($certificate->pdf_file) }}" 
                                               target="_blank" 
                                               class="pdf-link">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Hozircha faol sertifikatlar mavjud emas.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 

<!-- Image Modal -->
<div class="modal fade image-modal" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" class="modal-image" id="modalImage" alt="">
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
    function showImageModal(src, title) {
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.getElementById('modalImage');
        modalImage.src = src;
        modalImage.alt = title;
        modal.show();
    }

    // Rasmni ESC tugmasi bilan yopish
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
            if (modal) {
                modal.hide();
            }
        }
    });

    // Modalni rasmning tashqarisiga bosganda yopish
    document.getElementById('imageModal').addEventListener('click', function(event) {
        if (event.target === this) {
            const modal = bootstrap.Modal.getInstance(this);
            modal.hide();
        }
    });
</script>
@endsection 