@extends('addelkadir._layout')
@section('title', 'Bosh sahifa')
@section('content')
<h1 class="mb-4">Bugungi davomat — {{ $today }}</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">JAMI OSHPAZLAR</div>
        <div class="display-6">{{ $totalChefs }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-success"><div class="card-body">
        <div class="text-muted small">KELDI</div>
        <div class="display-6 text-success">{{ $cameCount }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-warning"><div class="card-body">
        <div class="text-muted small">KECHIKDI</div>
        <div class="display-6 text-warning">{{ $lateCount }}</div>
    </div></div></div>
    <div class="col-md-3"><div class="card border-danger"><div class="card-body">
        <div class="text-muted small">KELMADI</div>
        <div class="display-6 text-danger">{{ $absentCount }}</div>
    </div></div></div>
</div>

<div class="card"><div class="card-header"><strong>Bugungi ro'yxat</strong></div>
<table class="table mb-0">
    <thead><tr><th>Oshpaz</th><th>Bog'cha</th><th>Keldi</th><th>Ketdi</th><th>Holat</th></tr></thead>
    <tbody>
        @forelse ($todayRows as $r)
        <tr>
            <td>{{ optional($r->user)->name }}</td>
            <td>{{ optional($r->kindgarden)->kingar_name }}</td>
            <td>{{ optional($r->check_in_at)->format('H:i') ?? '—' }} @if($r->check_in_is_late)<span class="badge bg-warning">kechikdi</span>@endif</td>
            <td>{{ optional($r->check_out_at)->format('H:i') ?? '—' }}</td>
            <td>
                @if($r->check_in_at && !$r->check_out_at)<span class="badge bg-success">Bog'chada</span>
                @elseif($r->check_out_at)<span class="badge bg-secondary">Ketgan</span>
                @else<span class="badge bg-danger">—</span>@endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-muted text-center">Hech kim hali kelmagan</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
