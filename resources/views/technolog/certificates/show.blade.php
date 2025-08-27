@extends('layouts.app')

@section('css')
<style>
    .certificate-details {
        margin-bottom: 20px;
    }
    .certificate-details dt {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .certificate-details dd {
        margin-bottom: 15px;
    }
    .certificate-image {
        max-width: 300px;
        height: auto;
        margin: 20px 0;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }
    .status-active {
        background-color: #28a745;
        color: white;
    }
    .status-inactive {
        background-color: #dc3545;
        color: white;
    }
    .status-expired {
        background-color: #ffc107;
        color: black;
    }
    .product-badge {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        background-color: #17a2b8;
        color: white;
        border-radius: 15px;
        font-size: 12px;
    }
</style>
@endsection

@section('leftmenu')
    @include('technolog.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4">Sertifikat ma'lumotlari</h1>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('technolog.home') }}">Bosh sahifa</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technolog.certificates.index') }}">Sertifikatlar</a></li>
                    <li class="breadcrumb-item active">Ko'rish</li>
                </ol>
                <div>
                    <a href="{{ route('technolog.certificates.edit', $certificate->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <dl class="certificate-details">
                        <dt>Sertifikat raqami</dt>
                        <dd>{{ $certificate->certificate_number }}</dd>

                        <dt>Nomi</dt>
                        <dd>{{ $certificate->name }}</dd>

                        <dt>Holati</dt>
                        <dd>
                            @if(!$certificate->is_active)
                                <span class="status-badge status-inactive">Nofaol</span>
                            @elseif($certificate->end_date < now())
                                <span class="status-badge status-expired">Muddati tugagan</span>
                            @else
                                <span class="status-badge status-active">Faol</span>
                            @endif
                        </dd>

                        <dt>Boshlanish sanasi</dt>
                        <dd>{{ $certificate->start_date->format('d.m.Y') }}</dd>

                        <dt>Tugash sanasi</dt>
                        <dd>{{ $certificate->end_date->format('d.m.Y') }}</dd>

                        <dt>Maxsulotlar</dt>
                        <dd>
                            @foreach($certificate->products as $product)
                                <span class="product-badge">{{ $product->product_name }}</span>
                            @endforeach
                        </dd>

                        @if($certificate->description)
                            <dt>Qo'shimcha ma'lumot</dt>
                            <dd>{{ $certificate->description }}</dd>
                        @endif

                        <dt>PDF fayl</dt>
                        <dd>
                            <a href="{{ Storage::url($certificate->pdf_file) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf"></i> Ko'rish
                            </a>
                        </dd>

                        @if($certificate->image_file)
                            <dt>Rasm</dt>
                            <dd>
                                <img src="{{ Storage::url($certificate->image_file) }}" alt="Certificate image" class="certificate-image">
                            </dd>
                        @endif

                        <dt>Yaratilgan sana</dt>
                        <dd>{{ $certificate->created_at->format('d.m.Y H:i') }}</dd>

                        <dt>So'nggi yangilanish</dt>
                        <dd>{{ $certificate->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 