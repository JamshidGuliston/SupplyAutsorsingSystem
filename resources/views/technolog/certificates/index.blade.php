@extends('layouts.app')

@section('css')
<style>
    .certificate-image {
        max-width: 100px;
        height: auto;
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
    .status-warning {
        background-color: #fd7e14;
        color: white;
    }
    .alert-expiring {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 5px;
    }
    .alert-expiring ul {
        margin-bottom: 0;
        padding-left: 20px;
    }
    .alert-expiring li {
        margin-bottom: 5px;
    }
    .alert-expiring li:last-child {
        margin-bottom: 0;
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
            <h1 class="mt-4">Sertifikatlar</h1>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('technolog.home') }}">Bosh sahifa</a></li>
                    <li class="breadcrumb-item active">Sertifikatlar</li>
                </ol>
                <a href="{{ route('technolog.certificates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yangi sertifikat
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($expiringCertificates->isNotEmpty())
                <div class="alert-expiring">
                    <h5 class="mb-3"><i class="fas fa-exclamation-triangle"></i> Diqqat! Quyidagi sertifikatlar muddati yaqinlashmoqda:</h5>
                    <ul>
                        @foreach($expiringCertificates as $cert)
                            <li>
                                <strong>{{ $cert->name }}</strong> ({{ $cert->certificate_number }}) - 
                                <span class="text-danger">{{ $cert->days_left }} kun qoldi</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Sertifikat raqami</th>
                                <th>Nomi</th>
                                <th>Maxsulotlar</th>
                                <th>Boshlanish sanasi</th>
                                <th>Tugash sanasi</th>
                                <th>Holati</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->certificate_number }}</td>
                                <td>{{ $certificate->name }}</td>
                                <td>
                                    @foreach($certificate->products as $product)
                                        <span class="badge bg-info">{{ $product->product_name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $certificate->start_date->format('d.m.Y') }}</td>
                                <td>{{ $certificate->end_date->format('d.m.Y') }}</td>
                                <td>
                                    <span class="status-badge {{ $certificate->status['class'] }}">
                                        {{ $certificate->status['text'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('technolog.certificates.show', $certificate->id) }}" 
                                           class="btn btn-sm btn-info" title="Ko'rish">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('technolog.certificates.edit', $certificate->id) }}" 
                                           class="btn btn-sm btn-primary" title="Tahrirlash">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('technolog.certificates.destroy', $certificate->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Sertifikatni o\'chirishni tasdiqlaysizmi?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="O'chirish">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 