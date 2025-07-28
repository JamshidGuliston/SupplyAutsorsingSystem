@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="container-fluid px-4">
    <div style="text-align: end;">
        <a href="/technolog/addshop">+ qo'shish</a>
    </div>
    <div class="row g-3 my-2">
        @foreach($shops as $row)
        <div class="col-md-2">
            <div class="p-3 shadow-sm d-flex flex-column justify-content-around align-items-center rounded {{ $row->hide == 1 ? 'bg-white' : 'bg-light' }}" 
                 style="{{ $row->hide == 0 ? 'opacity: 0.6; border: 2px dashed #ccc;' : '' }}">
                <i class="fas fa-store-alt fs-1 primary-text border rounded-full secondary-bg p-2" 
                   style="color: {{ $row->hide == 1 ? 'chocolate' : '#999' }}"></i>
                <div class="text-center">
                    <p class="fs-4" style="font-size: 18px !important; color: {{ $row->hide == 0 ? '#999' : 'inherit' }}">
                        {{$row['shop_name']}}
                        @if($row->hide == 0)
                            <small class="text-muted d-block">(Faol emas)</small>
                        @endif
                    </p>
                    <a href="{{ route('technolog.shopsettings',  ['id' => $row->id ]) }}" 
                       style="color: #959fa3; margin-right: 6px; font-size: 20px;">
                       <i class="fas fa-cog"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection