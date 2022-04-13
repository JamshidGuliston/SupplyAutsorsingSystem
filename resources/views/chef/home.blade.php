@extends('layouts.app')

@section('leftmenu')
@include('chef.sidemenu'); 
@endsection

@section('content')
<!-- EDD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Menyu bo'yicha kerakli maxsulotlar</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('chef.minusproducts')}}" method="POST">
                @csrf
                <input type="hidden" name="titleid" value="{{ $kindgarden->id }}">
                <div class="modal-body">
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Og'irligi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $all->product_name }}</td>
                                <td style="width: 50px;"><input type="text" name="orders[{{ $all->id }}]" placeholder="{{ $all->size_name }}" required></td>
                            </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn add-age btn-info text-white">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End -->

<div class="container-fluid px-4">
    <div class="row g-3 my-2">
    @if(intval(date("H")) >= 8 and intval(date("H")) < 10 and $sendchildcount->count() == 0)
    <form method="POST" action="{{route('chef.sendnumbers')}}">
        @csrf
        <input type="hidden" name="kingar_id" value="{{ $kindgarden->id }}">
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
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <form action="#" method="get">
                    <p><b>Menyu: </b> sana: 12.04.2022</p>
                    <button type="submit" class="btn btn-success" style="width: 100%;">Menyu</button>
                </form>
                <form action="#" method="get">
                    <br>
                    <p><b>Omborxona: </b>Omborxonadan olingan maxsulot ro'yxatini yuboring. </p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#Modalsadd" style="width: 100%;">Maxsulotlar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row g-3 my-2">
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm align-items-center rounded">
                <form action="#" method="get">
                    <p><b>Taxminiy menyu: </b></p>
                    <button type="submit" class="btn btn-success" style="width: 100%;">Menyu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection