@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/activagecountedit" method="post">
		    @csrf
            <div class="modal-header bg-blue">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="edites_modal">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">O'zgartirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div class="date">
    <!-- <div class="lline"></div> -->
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($y_id != 1)
            <a href="/technolog/showdate/{{ $y_id-1 }}/0/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/technolog/showdate/{{ $y_id }}/{{ $month->id }}/0" class="month__item {{ ( $month->id == $m_id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/technolog/showdate/{{ $year->id+1 }}/0/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
    <div class="day">
        @foreach($days as $day)
            <a href="/technolog/showdate/{{ $day->year_id }}/{{ $day->month_id }}/{{ $day->id }}" class="day__item {{ ( $day->id == $aday) ? 'active' : null }}">{{ $day->day_number }}</a>
        @endforeach
    </div>
    <!-- <div class="lline"></div> -->
</div>
<div class="py-4 px-4">
<div class="row">
    <div class="col-md-6">
        @foreach($days as $day)
        @if($day->id == $aday)
            <b>{{ $day->day_number.":".$day->month_name.":".$day->year_name }}</b>
        @endif
        @endforeach
        <!-- <a href="/technolog/createnewdaypdf/{{ $day }}">
            <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
        </a> -->
    </div>
    <div class="col-md-3">
        
    </div>
    <div class="col-md-3">
    </div>
</div>
<hr>
<table class="table table-light py-4 px-4">
    <thead>
    <tr>
            <th scope="col" rowspan="2">ID</th>
            <th scope="col" rowspan="2">MTT-nomi</th>
            <th scope="col" rowspan="2">Xodimlar</th> 
            <th scope="col" rowspan="2">Yangi Menyu</th> 
            @foreach($ages as $age)
            <th scope="col" colspan="2"> 
                <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
            </th>
            @endforeach
            <th style="width: 70px;" rowspan="2">Nakladnoy</th>
        </tr>
        <tr style="color: #888888;">
            @foreach($ages as $age)
            <th><i class="fas fa-users"></i></th>
            <th><i class="fas fa-book-open"></i></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    <?php $t = 1;  ?>   
    @foreach($nextdayitem as $row)
        <tr>
            <td>{{ $t++ }}</td>
            <td>{{ $row['kingar_name'] }}</td>
            <td>{{ $row['workers_count'] }} </td>
            <td><a href="/activsecondmenuPDF/{{ $aday }}/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
            @foreach($ages as $age)
            @if(isset($row[$age->id]))
                <td>
                    {{ $row[$age->id][1]."  " }}
                    <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-agecount="{{ $row[$age->id][1] }}" data-dayid="{{ $aday }}" data-monthid = "{{ $day->month_id }}" data-yearid = "{{ $day->year_id }}" data-ageid="{{ $age->id }}" data-kinid="{{ $row['kingar_name_id'] }}" style="cursor: pointer; margin-right: 16px;"> </i>
                    @if($row[$age->id][2] != null)
                    <i class="far fa-envelope" style="color: #c40c0c"></i> 
                    @endif
                </td>
                <td><a href="/activmenuPDF/{{ $aday }}/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
            @else
                <td>{{ ' ' }}</td>
                <td>{{ ' ' }}</td>
            @endif
            @endforeach
            <td><a href="/activnakladPDF/{{ $aday }}/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.edites').click(function() {
            // alert(1);    
            var kinid = $(this).attr('data-kinid');
            var dayid = $(this).attr('data-dayid');
            var monthid = $(this).attr('data-monthid');
            var yearid = $(this).attr('data-yearid');
            var ageid = $(this).attr('data-ageid');
            var agecount = $(this).attr('data-agecount');
            var modaledite = $('.edites_modal');
            modaledite.html("<input type='hidden' name='dayid' value="+dayid+"><input type='hidden' name='monthid' value="+monthid+"><input type='hidden' name='yearid' value="+yearid+"><input type='hidden' name='kinid' value="+kinid+"><input type='hidden' name='ageid' value="+ageid+"><input type='text' class='form-control' name='agecount' value="+agecount+">");
        });
    });
</script>
@endsection