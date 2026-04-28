@extends('addelkadir._layout')
@section('title', 'Bog\'chalar')
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@section('content')
<h1 class="mb-4">Bog'chalar va geofence</h1>

@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

<table class="table">
    <thead><tr><th>Nomi</th><th>Lat</th><th>Lng</th><th>Radius</th><th></th></tr></thead>
    <tbody>
    @foreach ($items as $kg)
    <tr>
        <td>{{ $kg->kingar_name }}</td>
        <td>{{ $kg->lat ?? '—' }}</td>
        <td>{{ $kg->lng ?? '—' }}</td>
        <td>{{ $kg->geofence_radius ?? 200 }} m</td>
        <td>
            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#row-{{$kg->id}}">Tahrirlash</button>
        </td>
    </tr>
    <tr class="collapse" id="row-{{$kg->id}}">
        <td colspan="5">
            <form method="POST" action="{{ route('addelkadir.kindgardens.update', $kg->id) }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-3"><label>Lat</label><input name="lat" id="lat-{{$kg->id}}" value="{{ $kg->lat ?? 41.3111 }}" class="form-control" type="number" step="0.0000001" required></div>
                <div class="col-md-3"><label>Lng</label><input name="lng" id="lng-{{$kg->id}}" value="{{ $kg->lng ?? 69.2797 }}" class="form-control" type="number" step="0.0000001" required></div>
                <div class="col-md-2"><label>Radius (m)</label><input name="geofence_radius" value="{{ $kg->geofence_radius ?? 200 }}" class="form-control" type="number" min="50" max="1000" required></div>
                <div class="col-md-2"><button class="btn btn-success">Saqlash</button></div>
                <div class="col-12">
                    <div id="map-{{$kg->id}}" style="height:300px;border:1px solid #ddd"></div>
                </div>
            </form>
            <script>
            (function() {
                const id = {{ $kg->id }};
                const lat = parseFloat(document.getElementById('lat-' + id).value);
                const lng = parseFloat(document.getElementById('lng-' + id).value);
                const map = L.map('map-' + id).setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {attribution: '© OSM'}).addTo(map);
                let marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', e => {
                    const p = e.target.getLatLng();
                    document.getElementById('lat-' + id).value = p.lat.toFixed(7);
                    document.getElementById('lng-' + id).value = p.lng.toFixed(7);
                });
                map.on('click', e => {
                    marker.setLatLng(e.latlng);
                    document.getElementById('lat-' + id).value = e.latlng.lat.toFixed(7);
                    document.getElementById('lng-' + id).value = e.latlng.lng.toFixed(7);
                });
            })();
            </script>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
