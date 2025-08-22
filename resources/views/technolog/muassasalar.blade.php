@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deleteModalLabel">O'chirish tasdiqi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bu muassasani haqiqatan ham o'chirmoqchimisiz? Bu amal qaytarib bo'lmaydi.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <!-- <button type="button" class="btn btn-danger" id="confirmDelete">O'chirish</button> -->
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Muassasalar (Bog'chalar)</h2>
                <a href="{{ route('technolog.addmuassasa') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Yangi muassasa qo'shish
                </a>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-light table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Muassasa nomi</th>
                            <th scope="col">Muassasa kod</th>
                            <th scope="col">Tashkilot №</th>
                            <th scope="col">Tuman</th>
                            <th scope="col">Xodimlar soni</th>
                            <th scope="col">Yosh guruhlari</th>
                            <th scope="col">Holati</th>
                            <th scope="col">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kindgardens as $kindgarden)
                        <tr>
                            <td>{{ $kindgarden->id }}</td>
                            <td><strong>{{ $kindgarden->kingar_name }}</strong></td>
                            <td>{{ $kindgarden->short_name }}</td>
                            <td>{{ $kindgarden->number_of_org ?? '-' }}</td>
                            <td>
                                @php
                                    $region = $regions->firstWhere('id', $kindgarden->region_id);
                                @endphp
                                {{ $region ? $region->region_name : 'Noma\'lum' }}
                            </td>
                            <td>{{ $kindgarden->worker_count }} kishi</td>
                            <td>
                                @foreach($kindgarden->age_range as $age)
                                    <span class="badge bg-info">{{ $age->age_name }}</span>
                                    @if(!$loop->last) {{ ' ' }} @endif
                                @endforeach
                            </td>
                            <td>
                                @if($kindgarden->hide == 1)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('technolog.editmuassasa', $kindgarden->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Tahrirlash
                                    </a>
                                    <!-- <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $kindgarden->id }}">
                                        <i class="fas fa-trash"></i> O'chirish
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let deleteId = null;

    // Delete button click
    $('.delete-btn').click(function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDelete').click(function() {
        if(deleteId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("technolog.deletemuassasa") }}',
                type: 'DELETE',
                data: {
                    id: deleteId
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Xatolik yuz berdi!');
                }
            });
        }
        $('#deleteModal').modal('hide');
    });
});
</script>
@endsection 