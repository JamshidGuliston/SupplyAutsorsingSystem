@extends('addelkadir._layout')
@section('title', 'Davomat tarixi')
@section('content')
<h1 class="mb-4">Davomat tarixi</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
    <div class="col-auto"><button class="btn btn-primary">Filtr</button></div>
</form>

<table class="table table-striped">
    <thead><tr><th>Sana</th><th>Oshpaz</th><th>Bog'cha</th><th>Keldi</th><th>Ketdi</th><th>Selfilar</th></tr></thead>
    <tbody>
    @foreach ($rows as $r)
    <tr>
        <td>{{ $r->date->format('Y-m-d') }}</td>
        <td>{{ optional($r->user)->name }}</td>
        <td>{{ optional($r->kindgarden)->kingar_name }}</td>
        <td>{{ optional($r->check_in_at)->format('H:i') ?? '—' }}
            @if($r->check_in_is_late)<span class="badge bg-warning">kech</span>@endif
            @if($r->check_in_replaced_count > 0)<span class="badge bg-info">o'zg.{{$r->check_in_replaced_count}}</span>@endif
        </td>
        <td>{{ optional($r->check_out_at)->format('H:i') ?? '—' }}</td>
        <td>
            @if($r->check_in_selfie_path)
                <a target="_blank" href="{{ route('addelkadir.selfie', [$r->id, 'check_in']) }}">in</a>
            @endif
            @if($r->check_out_selfie_path)
                <a target="_blank" class="ms-2" href="{{ route('addelkadir.selfie', [$r->id, 'check_out']) }}">out</a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

{{ $rows->withQueryString()->links() }}
@endsection
