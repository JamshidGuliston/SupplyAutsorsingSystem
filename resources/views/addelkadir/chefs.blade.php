@extends('addelkadir._layout')
@section('title', 'Oshpazlar')
@section('content')
<h1 class="mb-4">Oshpazlar ro'yxati</h1>
<table class="table">
    <thead><tr>
        <th>Ism</th><th>Email</th><th>Bog'cha</th><th>Qurilma</th><th>App ver.</th><th>Oxirgi faollik</th>
    </tr></thead>
    <tbody>
    @foreach ($chefs as $c)
    @php $d = $devices->get($c->id); @endphp
    <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
        <td>{{ optional($c->kindgarden->first())->kingar_name ?? '—' }}</td>
        <td>{{ $d ? "{$d->platform} ({$d->device_model})" : '—' }}</td>
        <td>{{ $d->app_version ?? '—' }}</td>
        <td>{{ optional($d?->last_seen_at)->diffForHumans() ?? '—' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection
