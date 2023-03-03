@extends('layouts.app')

@section('leftmenu')
@include('boss.sidemenu'); 
@endsection

@section('content')
<div class="modal editesmodal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('boss.accepted')}}" method="post">
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
<div class="container-fluid px-4">
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Izoh</th>
                <th scope="col">Turi</th>
                <th scope="col">Sana</th>
                <th scope="col">So'm</th>
                <th scope="col">Holati</th>
                <th scope="col">...</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($cashes as $row)
                <tr>
                    <td>{{ $row->cashid }}</td>
                    <td>{{ $row->description }}</td>
                    <td>{{ $row->allcost_name }}</td>
                    <td>{{ $row->day_number.'/'.$row->month_name.'/'.$row->year_name }}</td>
                    <td>{{ $row->summ }} so'm</td>
                    @if($row->status == 1)
                        <td><p><i class="fas fa-clock"></i></p></td>
                        <td style="text-align: end;"><i class="editess  fas fa-check" aria-hidden="true" data-name-id="{{ $row->description }}" data-delet-id="{{ $row->cashid }}" data-bs-toggle="modal" style="cursor: pointer; color: blue" data-bs-target="#deleteModal"></i></td>
                    @else
                        <td><i class="fas fa-check"></i></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $cashes->links() }}
    <br>
    <a href="/boss/home">Orqaga</a>    
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.editess').click(function() {
            deleteid = $(this).attr('data-delet-id');
            pro_name = $(this).attr('data-name-id');
            var div = $('.editesproduct');
            div.html("<p>"+pro_name+"</p><input type='hidden' name='id' value="+deleteid+">");
        });

        $('.detete').click(function() {
            deleteid = $(this).attr('data-delet-id');
            pro_name = $(this).attr('data-name-id');
            var div = $('.deletename');
            // alert(deletes);
            div.html("<p>"+pro_name+".</p><input type='hidden' name='cashid' value="+deleteid+">");
            
        });
    });
    function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
</script>
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