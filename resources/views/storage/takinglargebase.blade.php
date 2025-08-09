@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
/* Pagination styles fix */
.pagination { margin: 0; }
.pagination .page-link { color: #0d6efd; border: 1px solid #dee2e6; padding: .375rem .6rem; font-size: .875rem; }
.pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
.pagination .page-link:hover { color: #0a58ca; background-color: #e9ecef; }
.pagination .page-link svg { width: 16px; height: 16px; }
</style>
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
        <form action="/storage/addtakinglargebase" method="post">
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
                        <option value="{{$row['id']}}">{{$row['name']}}</option>
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
                    <td><a href="/storage/intakinglargebase/{{ $row->gid }}">{{ $row->title }}</a></td>
                    <td>{{ $row->outside_name }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
                    <td><a href="#">PDF</a></td>
                    <!-- <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $row->title }}" data-group-id="{{ $row->gid }}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i></td> -->
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="row">
        <div class="col-md-6">
            <p class="text-muted small">{{ $res->firstItem() }} dan {{ $res->lastItem() }} gacha, jami {{ $res->total() }} yozuv</p>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            {{ $res->links('pagination::bootstrap-4') }}
        </div>
    </div>
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