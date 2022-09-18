@extends('layouts.app')
@section('css')
<style>
    .table{
        width: auto;
    }
    .loader-box {
        width: 100%;
        background-color: #80afc68a;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        display: none;
        justify-content: center;
    }
    b{
        color: #3c7a7c;
    }
    .loader {
        border: 9px solid #f3f3f3;
        border-radius: 50%;
        border-top: 9px solid #3498db;
        width: 60px;
        display: block;
        height: 60px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: absolute;
        left: 353px;
        top: 153px;
    }
    th, td{
        font-size: 0.8rem;
        margin: .3rem .3rem;
        text-align: center;
        vertical-align: middle;
        border-bottom-color: currentColor;
        border-right: 1px solid #c2b8b8;
    }
    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    .year {
        text-align: center;
    }
    .month,
    .day {
        margin: 10px 20px;
        display: flex;
        justify-content: left;
    }

    .month__item{
        width: calc(100% / 12);
        text-align: center;
        border-bottom: 1px solid #000;
    }

    .month__item + .month__item {
        /* border-left: 1px solid #000; */
    }
    .day__item{
        background-color: #ecf6f1;
        text-align: center;
        vertical-align: middle;
        min-width: 34px;
        padding: 5px;
        margin-left: 5px;
        border-radius: 50%;
    }

    .month__item, .day__item{
        color: black;
        cursor: context-menu;
        /* border: 1px solid #87706a; */
        text-decoration: none;
    }
    .active{
        background-color: #23b242;
        color: #fff;
    }
    .month__item:hover,
    .day__item:hover{
        background-color: #23b242;
        color: #fff;
        transition: all .5s;
        cursor: pointer;
    }
</style>
@endsection
@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection
@section('content')
<!-- EDD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot narxlarini yangilash</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
        </div>
    </div>
</div>

<div class="date">
    <!-- <div class="year">2020</div> -->
    <div class="month">
        @foreach($months as $month)
            <a href="/accountant/income/{{ $month->id }}" class="month__item {{ (Request::is('accountant/income/'.$month->id) or ($month->month_active == 1 and $id == 0)) ? 'active' : null }}">{{ $month->month_name }}</a>
        @endforeach
    </div>
</div>
<div class="py-4 px-4">
    
    <table class="table table-light py-4 px-4">
        <thead>
            <th style="background-color: #92e1e1; width: 30px;">Махсулотлар</th>
            <th style="background-color: #92e1e1;">KG</th>
            <th style="background-color: #92e1e1;">Jami summa</th>
            <th style="background-color: #92e1e1;">O'rtacha narx</th>
            @foreach($regions as $region)
                <th style="background-color: #92e1e1;">{{ $region->region_name }}</th>
                <th style="background-color: #92e1e1;">O'tkazish</th>
                <th style="background-color: #92e1e1;">Summa</th>
            @endforeach
            <th style="background-color: #92e1e1;">Sotilgan</th>
            <th style="background-color: #92e1e1;">Summa jami</th>
            <th style="background-color: #92e1e1;">Farqi</th>
            <th colspan="3" style="background-color: #92e1e1;">Qoldiq</th>
            <th style="background-color: #92e1e1;">Summa</th>
            <th style="background-color: #92e1e1;">Daromad</th>
            <th style="background-color: #92e1e1;">Marja %</th>
        </thead>
        <tbody>
            <?php
                $allsums = 0;
                $plus = 0;
            ?>
            @foreach($incomes as $value)
            <?php
                if(!isset($mods[$value["p_id"]])){
                    $mods[$value["p_id"]] = 0;
                }
                $pay = 0;
                $allsum = 0;
            ?>
            <tr>
                <td style="background-color: #a7e9a7;">{{ $value["p_name"] }}</td>
                <td style="background-color: #aed3ae;">{{ $value["weight"] }}</td>
                <td style="background-color: #aed3ae;">{{ $value["p_sum"] }}</td>
                <td style="background-color: #aed3ae;">{{ $value["count"] ? round($value["p_cost"] / $value["count"], 1) : 0 }}</td>
                @foreach($regions as $region)
                @if(isset($inregions[$region->id][$value["p_id"]."kg"]))
                    <?php 
                        $pay += round($inregions[$region->id][$value["p_id"]."kg"], 1);
                        $allsum += round($inregions[$region->id][$value["p_id"]."kg"] * $inregions[$region->id][$value["p_id"]."cost"], 1);
                    ?>
                    <td style="background-color: #aebfd9;">{{ round($inregions[$region->id][$value["p_id"]."kg"], 1) }}</td>
                    <td style="background-color: #aebfd9;">{{ round($inregions[$region->id][$value["p_id"]."cost"], 1) }}</td>
                    <td style="background-color: #aebfd9;">{{ round($inregions[$region->id][$value["p_id"]."kg"] * $inregions[$region->id][$value["p_id"]."cost"], 1)  }}</td>
                @else
                    <td style="background-color: #aebfd9;">0</td>
                    <td style="background-color: #aebfd9;">0</td>
                    <td style="background-color: #aebfd9;">0</td>
                @endif
                @endforeach
                <?php 
                    $allsums += round($allsum, 1);
                    $plus += round($allsum - $value["p_cost"], 1);
                ?>
                <td style="background-color: #86a3d9;">{{ round($pay, 1) }}</td>
                <td style="background-color: #86a3d9;">{{ round($allsum, 1) }}</td>
                <td style="background-color: #7bc264;">{{ round($allsum - $value["p_sum"], 1) }}</td>
                <td style="background-color: #ffff8e;">{{ round($value["weight"] - $value["minusweight"], 1) }}</td>
                <td style="background-color: #ffff8e;">{{ round($mods[$value["p_id"]], 1) }}</td>
                <td style="background-color: #ffff8e;">{{ round($value["weight"] - $value["minusweight"] + $mods[$value["p_id"]], 1) }}</td>
                <td style="background-color: #ffff8e;">{{ round(($value["weight"] - $value["minusweight"] + $mods[$value["p_id"]]) * ($value["count"] ? round($value["p_cost"] / $value["count"], 1) : 0), 1) }}</td>
                <td style="background-color: #f5c390;">{{ round(($allsum - $value["p_sum"]) + ($value["weight"] - $value["minusweight"] + $mods[$value["p_id"]]) * ($value["count"] ? round($value["p_cost"] / $value["count"], 1) : 0), 1) }}</td>
                <td style="background-color: #f5c390;">{{ $allsum ? round(($allsum - $value["p_cost"]) / $allsum * 100, 1) : "0" }}</td>
            </tr>
            @endforeach
            <tr>
                <td><b>JAMI:</b></td>
                <td colspan="{{ count($regions)*3 + 7 }}"></td>
                <td>{{ round($allsums) }}</td>
                <td>{{ round($plus) }}</td>
                <td>{{ round(round($plus) / round($allsums) * 100, 1) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"><a href="/accountant/income/">Orqaga</a></label>
        <div class="col-sm-6">
        <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
        </div>
    </div>
    
</div>
@endsection

@section('script')
<script>
    
</script>
@endsection