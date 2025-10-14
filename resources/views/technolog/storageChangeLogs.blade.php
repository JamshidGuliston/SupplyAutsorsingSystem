@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    .log-table {
        font-size: 0.9rem;
    }
    .log-table th {
        background-color: #f0f0f0;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .badge-plus {
        background-color: #28a745;
    }
    .badge-minus {
        background-color: #dc3545;
    }
    .badge-residual {
        background-color: #17a2b8;
    }
    .positive {
        color: #28a745;
        font-weight: bold;
    }
    .negative {
        color: #dc3545;
        font-weight: bold;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="date">
    <div class="year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="{{ route('technolog.storageChangeLogs', ['id' => $kingar->id, 'monthid' => 0]) }}" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $m)
            <a href="{{ route('technolog.storageChangeLogs', ['id' => $kingar->id, 'monthid' => $m->id]) }}" class="month__item {{ ($m->id == $month->id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $m->month_name }}</a>
        @endforeach
        <a href="{{ route('technolog.storageChangeLogs', ['id' => $kingar->id, 'monthid' => 0]) }}" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>

<div class="py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4>O'zgarishlar tarixi</h4>
            <p><strong>Боғча:</strong> {{ $kingar->kingar_name }}</p>
            <p><strong>Ой:</strong> {{ $month->month_name }} {{ $year->year_name }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('technolog.plusmultistorage', ['id' => $kingar->id, 'monthid' => $monthid]) }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Ortga qaytish
            </a>
        </div>
    </div>

    @if($logs->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover log-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 200px;">Mahsulot</th>
                    <th style="width: 80px;">Kun</th>
                    <th style="width: 100px;">Turi</th>
                    <th style="width: 100px;">Eski qiymat</th>
                    <th style="width: 100px;">Yangi qiymat</th>
                    <th style="width: 100px;">Farqi</th>
                    <th style="width: 150px;">Foydalanuvchi</th>
                    <th style="width: 150px;">Sana va vaqt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->product->product_name ?? 'N/A' }}</td>
                    <td>{{ $log->day->day_number ?? 'N/A' }}</td>
                    <td>
                        @if($log->type == 'plus')
                            <span class="badge badge-plus">Kirim</span>
                        @elseif($log->type == 'minus')
                            <span class="badge badge-minus">Sarflangan</span>
                        @else
                            <span class="badge badge-residual">Qoldiq</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($log->old_value, 2) }}</td>
                    <td class="text-end">{{ number_format($log->new_value, 2) }}</td>
                    <td class="text-end {{ $log->difference > 0 ? 'positive' : ($log->difference < 0 ? 'negative' : '') }}">
                        {{ $log->difference > 0 ? '+' : '' }}{{ number_format($log->difference, 2) }}
                    </td>
                    <td>{{ $log->user_name }}</td>
                    <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        <p class="text-muted">
            <strong>Jami o'zgarishlar:</strong> {{ $logs->count() }} ta
        </p>
    </div>
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Bu oy uchun hali o'zgarishlar tarixi yo'q.
    </div>
    @endif
</div>
@endsection

