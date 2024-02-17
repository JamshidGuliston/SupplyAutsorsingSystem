@extends('layouts.app')

@section('leftmenu')
@include('boss.sidemenu'); 
@endsection
@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
@endsection
@section('content')
<div class="modal editesmodal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">Qabul qilish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body editesproduct">
                
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                    <button type="submit" class="btn editsub btn-warning">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="date">
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> 0]) }}" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> $month->id]) }}" class="month__item {{ ( $month->id == $id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="{{ route('boss.home', ['yearid'=> $year->id, 'monthid'=> 0]) }}" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>

<div class="container-fluid px-4">
    <hr>
    <table class="table table-light py-4 px-4">
        <tbody>
        	<?php
        		$total = 0;
        		$ndstotal = 0;
        		$usttotal = 0;
        		$diftotal = 0;
        		$saletotal = 0;
        		$bytotal = 0;
        	?>
            @foreach($regions as $region)
            <tr><td colspan="6" style="background-color:#5c605e63"><b>{{ $region->region_name }}</b></td></tr>
            <tr>
                <td>Olindi</td>
                <td>Sotildi</td>
                <td>Farqi</td>
                <td>Ustama {{$prt->where('region_id', $region->id)->first()->raise}} %</td>
                <td>NDS {{$prt->where('region_id', $region->id)->first()->nds}} %</td>
                <td>Jami</td>
            </tr>
            <tr>
            <?php 
                $sumbyregion[$region->id]['summ_by'] = isset($sumbyregion[$region->id]['summ_by']) ? $sumbyregion[$region->id]['summ_by'] : 0;
                $sumbyregion[$region->id]['summ_sale'] = isset($sumbyregion[$region->id]['summ_sale']) ? $sumbyregion[$region->id]['summ_sale'] : 0;
                $total = $total + $sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise + ($sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise) / 100 * $prt->where('region_id', $region->id)->first()->nds;
                $bytotal = $bytotal + $sumbyregion[$region->id]['summ_by'];
                $saletotal = $saletotal + $sumbyregion[$region->id]['summ_sale'];
                $usttotal = $usttotal + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise;
                $ndstotal = $ndstotal + ($sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise) / 100 * $prt->where('region_id', $region->id)->first()->nds;
            ?>
                <td>{{ $sumbyregion[$region->id]['summ_by'] }}</td>
                <td>{{ $sumbyregion[$region->id]['summ_sale'] }}</td>
                <td>{{ $sumbyregion[$region->id]['summ_sale'] - $sumbyregion[$region->id]['summ_by'] }}</td>
                <td>{{ $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise }}</td>
                <td>{{ ($sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise) / 100 * $prt->where('region_id', $region->id)->first()->nds }}</td>
                <td>{{ $sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise + ($sumbyregion[$region->id]['summ_sale'] + $sumbyregion[$region->id]['summ_sale'] / 100 * $prt->where('region_id', $region->id)->first()->raise) / 100 * $prt->where('region_id', $region->id)->first()->nds }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6"><b>Jami</b></td>
            </tr>
            <tr>
            	<td>{{ $bytotal }}</td>
            	<td>{{ $saletotal }}</td>
            	<td>{{ $diftotal }}</td>
            	<td>{{ $usttotal }}</td>
            	<td>{{ $ndstotal }}</td>
            	<td>{{ $total }}</td>
            </tr>
        </tbody>
        
    </table>
    <br>  
</div>
@endsection

@section('script')

@if(session('status'))
<script> 
    // alert('{{ session("status") }}');
    swal({
        title: "Ajoyib!",
        text: "{{ session('status') }}",
        icon: "success",
        button: "ok",
    });
</script>
@endif
@endsection