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
</style>
@endsection
@section('leftmenu')
@include('accountant.sidemenu'); 
@endsection
@section('content')
<!-- Modal -->
<div class="modal editesmodal fade" id="modalsettings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hisobot: {{ $kindgar->kingar_name}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                    <div class="col-sm-4">
                        <input type="hidden" id="kind" name="kindid" value="{{ $kindgar->id }}" /> 
                        <select class="form-select" id="startdayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Sanadan</option>
                            @foreach($yeardays as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-select" id="enddayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Sanaga</option>
                            @foreach($yeardays as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select class="form-select" id="costdayid" onchange="changeFunc();" aria-label="Default select example" required>
                            <option value="">Narx sanasi</option>
                            @foreach($costs as $row)
                                <option value="{{$row['day_id']}}">{{ sprintf("%02d", $row['day_number']).'.'.sprintf("%02d", $row['month_id']).'.'.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                <h5 class="menutitle"></h5>
                <div class="urlpdf">
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-9">
            <b>{{ $kindgar->kingar_name .": ". $days[0]['month_name']; }}</b>
        </div>
        <div class="col-md-3">
            <!-- <b>Bog'chalarga so'rov yuborish</b>
            <a href="">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a> -->
            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalsettings">Hisobot qurish</button>
        </div>
        <div class="col-md-3">
            <b></b>
            <!-- <i class="fas fa-plus-circle" style="color: #3c7a7c; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#Modalsadd"></i> -->
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <th style="width: 30px;">Махсулотлар/{{ $days[0]['month_name']; }}</th>
            <th style="width: 30px;">Нарх</th>
            @foreach($days as $day)
                <th scope="col">{{ $day->day_number; }}</th>
            @endforeach
            <th>Жами</th>
            <th>Сумма</th>
        </thead>
        <tbody>
            @foreach($nakproducts as $key => $row)
            <tr>
                <td>{{ $row['product_name'] }}</td>
                <td>{{ "1" }}</td>
                <?php 
                    $summ = 0;
                ?>
                @foreach($days as $day)
                    @if(isset($row[$day['id']]))
                        <td>
                        <?php  
                            printf("%01.2f", $row[$day['id']]); 
                            $summ += $row[$day['id']];
                        ?>
                        </td>
                    @else
                        <td>
                            {{ '0' }}
                        </td>
                    @endif
                @endforeach
                <td><?php printf("%01.2f", $summ) ?></td>
                <td><?php printf("%01.2f", $summ*1) ?></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"><a href="/accountant/reports">Orqaga</a></label>
        <div class="col-sm-6">
        <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
        </div>
    </div>
    
</div>
@endsection

@section('script')
<script>
    function changeFunc() {
        var selectBox = document.getElementById("startdayid");
        var start = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("enddayid");
        var end = selectBox.options[selectBox.selectedIndex].value;
        var selectBox = document.getElementById("costdayid");
        var cost = selectBox.options[selectBox.selectedIndex].value;
        var kindid = document.getElementById("kind").value; 
        var div = $('.urlpdf');
        if(start != "" && end != "" && cost != ""){
            var html = "Ходимлар <br><p>Накапител: <a href='/accountant/nakapitworker/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/nakapitworkerexcel/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a> Cчёт фактура: <a href='/accountant/schotfakturworker/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-pdf text-info' style='cursor: pointer; margin-right: 16px;'></i></a> <a href='/accountant/schotfakturworkerexcel/"+kindid+"/"+1+"/"+start+"/"+end+"/"+cost+"' target='_blank' ><i class='far fa-file-excel text-info' style='cursor: pointer; margin-right: 16px;'></i></a>";

            div.html(html);
        }
    }

</script>
@endsection