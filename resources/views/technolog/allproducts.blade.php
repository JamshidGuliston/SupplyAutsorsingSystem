@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="container-fluid px-4">
	<div style="text-align: end; margin-bottom: 20px;">
        <a href="/technolog/pagecreateproduct" class="btn btn-success">+ Yangi maxsulot qo'shish</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-light table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Maxsulot nomi</th>
                    <th scope="col">Do'konlar</th>
                    <th scope="col">Oqsillar (100gr)</th>
                    <th scope="col">Yog'lar (100gr)</th>
                    <th scope="col">Uglevods (100gr)</th>
                    <th scope="col">Kaloriya (100gr)</th>
                    <th scope="col">Sozlamalar</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; ?>
                @foreach($products as $row)
                <tr>
                    <th scope="row">{{ ++$i }}</th>
                    <td>{{ $row->product_name }}</td>
                    <td>
                        @foreach($row->shop as $shop)
                            <span class="badge bg-success">{{ $shop->shop_name }}</span>
                            @if(!$loop->last) {{ ' ' }} @endif
                        @endforeach
                    </td>
                    <td>{{ $row->proteins }} gr</td>
                    <td>{{ $row->fats }} gr</td>
                    <td>{{ $row->carbohydrates }} gr</td>
                    <td>{{ $row->kcal }} kcal</td>
                    <td>
                        <a href="{{ route('technolog.settingsproduct',  ['id' => $row->id ]) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-cog"></i> Sozlash
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection