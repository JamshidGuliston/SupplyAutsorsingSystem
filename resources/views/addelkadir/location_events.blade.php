@extends('addelkadir._layout')
@section('title', 'Lokatsiya tarixi')
@section('content')
<h1 class="mb-4">Lokatsiya tarixi</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="date" value="{{ $date }}" class="form-control">
    </div>
    <div class="col-auto">
        <select name="chef_id" class="form-select">
            <option value="">Barcha oshpazlar</option>
            @foreach ($chefs as $c)
                <option value="{{ $c->id }}" {{ (int) $chefId === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select name="event_type" class="form-select">
            <option value="">Barcha hodisalar</option>
            <option value="exit" {{ $eventType === 'exit' ? 'selected' : '' }}>Chiqish</option>
            <option value="enter" {{ $eventType === 'enter' ? 'selected' : '' }}>Kirish</option>
            <option value="beacon" {{ $eventType === 'beacon' ? 'selected' : '' }}>Heartbeat</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-primary">Filtr</button></div>
</form>

<div class="card mb-3">
    <div class="card-body">
        <strong>{{ $date }}</strong> uchun: <strong>{{ $counts['exit'] }}</strong> chiqish hodisasi &middot;
        Jami tashqarida: <strong>{{ $minutesOutside }}</strong> daq &middot;
        <strong>{{ $counts['beacon'] }}</strong> ta heartbeat &middot;
        <strong>{{ $counts['enter'] }}</strong> ta kirish
    </div>
</div>

<table class="table table-striped">
    <thead><tr>
        <th>Vaqt</th><th>Oshpaz</th><th>Bog'cha</th><th>Hodisa</th>
        <th>Masofa</th><th>Holat</th><th>Xaritada</th>
    </tr></thead>
    <tbody>
    @forelse ($events as $e)
        @php
            $radius = optional($e->kindgarden)->geofence_radius ?: 200;
            $inside = $summary->isInside((int) $e->distance_m, (int) $radius);
        @endphp
        <tr>
            <td>{{ $e->happened_at->copy()->setTimezone('Asia/Tashkent')->format('H:i') }}</td>
            <td>{{ optional($e->user)->name }}</td>
            <td>{{ optional($e->kindgarden)->kingar_name }}</td>
            <td>
                @if($e->event_type === 'enter')<span class="badge bg-success">🟢 Kirish</span>
                @elseif($e->event_type === 'exit')<span class="badge bg-danger">🔴 Chiqish</span>
                @else<span class="badge bg-secondary">🔄 Heartbeat</span>@endif
                @if($e->is_mock)<span class="badge bg-warning text-dark">soxta GPS</span>@endif
            </td>
            <td>{{ $e->distance_m }}m</td>
            <td>
                @if($inside)<span class="text-success">Ichida</span>
                @else<span class="text-danger fw-bold">Tashqarida</span>@endif
            </td>
            <td>
                <a target="_blank" href="https://yandex.uz/maps/?ll={{ $e->lng }},{{ $e->lat }}&z=18&pt={{ $e->lng }},{{ $e->lat }},pm2rdm">📍</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted">Bu kun uchun lokatsiya hodisalari yo'q.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $events->links() }}
@endsection
