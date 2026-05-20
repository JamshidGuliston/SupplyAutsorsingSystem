@extends('addelkadir._layout')
@section('title', 'Davomat tarixi')
@section('content')
<h1 class="mb-4">Davomat tarixi</h1>

@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
    <div class="col-auto"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
    <div class="col-auto"><button class="btn btn-primary">Filtr</button></div>
</form>

<table class="table table-striped">
    <thead><tr><th>Sana</th><th>Oshpaz</th><th>Bog'cha</th><th>Keldi</th><th>Ketdi</th><th>Selfilar</th><th>Amal</th></tr></thead>
    <tbody>
    @foreach ($rows as $r)
    <tr>
        <td>{{ $r->date->format('Y-m-d') }}</td>
        <td>{{ optional($r->user)->name }}</td>
        <td>{{ optional($r->kindgarden)->kingar_name }}</td>
        <td>{{ $r->check_in_at ? $r->check_in_at->copy()->setTimezone('Asia/Tashkent')->format('H:i') : '—' }}
            @if($r->check_in_is_late)<span class="badge bg-warning">kech</span>@endif
            @if($r->check_in_replaced_count > 0)<span class="badge bg-info">o'zg.{{$r->check_in_replaced_count}}</span>@endif
        </td>
        <td>{{ $r->check_out_at ? $r->check_out_at->copy()->setTimezone('Asia/Tashkent')->format('H:i') : '—' }}
            @if($r->check_out_undo_count > 0)<span class="badge bg-secondary">bekor x{{$r->check_out_undo_count}}</span>@endif
        </td>
        <td>
            @if($r->check_in_selfie_path)
                <a target="_blank" href="{{ route('addelkadir.selfie', [$r->id, 'check_in']) }}">in</a>
            @endif
            @if($r->check_out_selfie_path)
                <a target="_blank" class="ms-2" href="{{ route('addelkadir.selfie', [$r->id, 'check_out']) }}">out</a>
            @endif
        </td>
        <td>
            @if($r->check_out_at)
                <form method="POST"
                      action="{{ route('addelkadir.attendance.undo_check_out', $r->id) }}"
                      onsubmit="return confirm('Ketishni bekor qilishni tasdiqlaysizmi? Selfi rasmi o\'chiriladi.')"
                      class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning">Ketishni bekor qilish</button>
                </form>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

{{ $rows->withQueryString()->links() }}
@endsection
