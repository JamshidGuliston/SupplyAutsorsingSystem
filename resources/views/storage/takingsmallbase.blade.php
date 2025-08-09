@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
/* Pagination custom styles */
.pagination {
    margin: 0;
}
.pagination .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
/* Hide any large arrow elements */
.pagination .page-link svg {
    width: 16px;
    height: 16px;
}
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
                        @if(!isset($row['kindgarden'][0]['kingar_name']))
                            <option value="{{$row['id']}}">{{"Ishdan ketgan, ".$row['name']}}</option>
                        @else
                            <option value="{{$row['id']}}">{{$row['kindgarden'][0]['kingar_name'].", ".$row['name']}}</option>
                        @endif
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
                <th style="width: 40px;">...</th>
                <!-- <th style="width: 40px;">...</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($res as $row)
            	@php
            		$user = $users->find($row->uid);
            	@endphp
        		@if(isset($user) and count($user->kindgarden) > 0)
        			<tr>
	                    <td>{{ $row->gid }}</td>
	                    <td><a href="/storage/intakingsmallbase/{{ $row->gid }}/{{ $users->find($row->uid)->kindgarden[0]['id'] }}/{{ $row->day_id }}">{{ $row->title }}</a></td>
	                    <td>{{ $row->outside_name }}</td>
	                    <td>{{ $users->find($row->uid)->kindgarden[0]['kingar_name'].', '.$users->find($row->uid)->name }}</td>
	                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
	                    <td><a href="/storage/intakingsmallbasepdf/{{ $row->day_id }}/{{ $users->find($row->uid)->kindgarden[0]['id'] }}" target="_blank">PDF</a></td>
	                    <!-- <td style="text-align: end;"><i class="detete  fa fa-trash" aria-hidden="true" data-name-id="{{ $row->title }}" data-group-id="{{ $row->gid }}" data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i></td> -->
	                </tr>
        		@else
        			<tr>
	                    <td>{{ $row->gid }}</td>
	                    <td><a href="/storage/intakingsmallbase/{{ $row->gid }}/{{ 0 }}/{{ $row->day_id }}">{{ $row->title }}</a></td>
	                    <td>{{ $row->outside_name }}</td>
	                    <td>{{ 'Nomalum bogcha, Noamalum oshpaz' }}</td>
	                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
	                    <td><a href="#">PDF</a></td>
	                </tr>
        		@endif
            @endforeach
        </tbody>
    </table>
    
    <!-- Pagination Info -->
    <div class="row mt-3">
        <div class="col-md-6">
            <p class="text-muted small">
                {{ $res->firstItem() }} dan {{ $res->lastItem() }} gacha, jami {{ $res->total() }} ta natija
            </p>
        </div>
        <div class="col-md-6">
            <!-- Simple Pagination -->
            <div class="d-flex justify-content-end">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
                        @if ($res->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Oldingi</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $res->previousPageUrl() }}">Oldingi</a></li>
                        @endif

                        @php
                            $current = $res->currentPage();
                            $last = $res->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp

                        @if($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $res->url(1) }}">1</a></li>
                            @if($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $current)
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $res->url($page) }}">{{ $page }}</a></li>
                            @endif
                        @endfor

                        @if($end < $last)
                            @if($end < $last - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $res->url($last) }}">{{ $last }}</a></li>
                        @endif

                        @if ($res->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $res->nextPageUrl() }}">Keyingi</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Keyingi</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="/storage/home/0/0">Orqaga</a>
    </div>
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