@extends('layouts.app')

@section('leftmenu')
@include('chef.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row g-3 my-2">
    @if(intval(date("H")) >= 8 and intval(date("H")) < 10 and $sendchildcount->count() == 0)
    <form method="POST" action="{{route('chef.sendnumbers')}}">
        @csrf
        <input type="hidden" name="kingar_id" value="{{ $kindgarden->id }}"
        <p><b>Бугунги болалар сонини юборинг</b></p>
        @foreach($kindgarden->age_range as $row)
            <div class="col-md-3">
                <div class="p-3 bg-white shadow-sm align-items-center rounded">
                    <p><b>{{ $row->age_name }}</b></p>
                    <div class="user-box">
                        <input type="number" name="agecount[{{ $row->id }}][]" placeholder="Болалар сони" class="form-control" required>
                    </div>
                </div>
            </div>
        @endforeach
        <button type="submit" class="btn btn-success">Yuborish</button>
    </form>
    @else
        <p><b>Бугунги болалар сони юборилди</b></p>
    @endif
    </div>
</div>
@endsection