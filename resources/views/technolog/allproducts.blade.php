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
                    <th scope="col">Birligi</th>
                    <th scope="col">Do'konlar</th>
                    <th scope="col">Yuborilish kunlari</th>
                    <th scope="col">Norma</th>
                    <th scope="col">Sozlamalar</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 0; ?>
                @foreach($products as $row)
                <tr>
                    <th scope="row">{{ ++$i }}</th>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->size_name }}</td>
                    <td>
                        @foreach($row->shop as $shop)
                            <span class="badge bg-success">{{ $shop->shop_name }}</span>
                            @if(!$loop->last) {{ ' ' }} @endif
                        @endforeach
                    </td>
                    <td>
                        @php
                            $colors = [
                                0 => 'bg-warning',
                                1 => 'bg-primary',    // Ko'k
                                2 => 'bg-success',    // Yashil
                                3 => 'bg-info',    // Sariq
                                4 => 'bg-danger',      // Moviy
                                5 => 'bg-secondary',  // Kulrang
                                6 => 'bg-dark',       // Qora
                                7 => 'bg-light'       // Och kulrang
                            ];
                            $color = $colors[$row->pro_cat_id] ?? 'bg-warning';
                        @endphp
                        <span class="badge {{ $color }}">{{ $row->pro_cat_name }}</span>
                    </td>
                    <td>{{ $row->norm_name }}</td>
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