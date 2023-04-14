@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- DeleteModal -->
<div class="modal" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/deletetakinglargebase" method="post">
		    @csrf
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="grouptitle"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- AddModal -->
<div class="modal editesmodal" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/addtakingsmallbase" method="post">
		    @csrf
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="exampleModalLabel">Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="title" class="form-control" required>
                <br>
                <select class="form-select" name="day_id" required>
                    <option value="">--Sana--</option>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                    @endforeach
                </select>
                <br>
                <select class="form-select" name="user_id" required>
                    <option value="">--Xodim--</option>
                    @foreach($users as $row)
                        if(empty($row['kindgarden'][0]['kingar_name'])){
                            $row['kindgarden'][0]['kingar_name'] = "Ishdan ketgan";
                        }
                        <option value="{{$row['id']}}">{{$row['kindgarden'][0]['kingar_name'].", ".$row['name']}}</option>
                    @endforeach
                </select><br>
                <select class="form-select" name="outid" required aria-label="Default select example">
                    <option value="">--Sabab turi--</option>
                    @foreach($outtypes as $row)
                        <option value="{{ $row->id }}">{{ $row->outside_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div class="py-4 px-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Яратиш</button>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Izoh</th>
                <th scope="col">Sabab turi</th>
                <th scope="col">Shaxs</th>
                <th scope="col">Sana</th>
                <th style="width: 40px;">Svod</th>
                <!-- <th style="width: 40px;">...</th> -->
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($res as $row)
                <tr>
                    <td>{{ $row->gid }}</td>
                    <td><a href="/storage/intakingsmallbase/{{ $row->gid }}/{{ $users->find($row->uid)->kindgarden[0]['id'] }}">{{ $row->title }}</a></td>
                    <td>{{ $row->outside_name }}</td>
                    <td>{{ $users->find($row->uid)->kindgarden[0]['kingar_name'].', '.$users->find($row->uid)->name }}</td>
                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
                    <td><a href="#">PDF</a></td>
                    <!-- <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $row->title }}" data-group-id="{{ $row->gid }}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i></td> -->
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home/0/0">Orqaga</a>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.detete').click(function() {
            var title = $(this).attr('data-name-id');
            var gid = $(this).attr('data-group-id');
            var div = $('.grouptitle');
            div.html("<h3><b>"+title+"</b> maxsulotini o'chirish.</h3><input type='hidden' name='gid' value="+gid+">");

        });
    });
</script>
@endsection