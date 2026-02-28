@extends('layouts.app')

@section('leftmenu')
@include('accountant.sidemenu');
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row my-3">
        <div class="col-md-9">
            <h4><i class="fas fa-file-contract me-2"></i>Shartnomalar</h4>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Yangi shartnoma
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Shartnoma raqami</th>
                        <th>Shartnoma sanasi</th>
                        <th>Amal qilish muddati</th>
                        <th>Tuman</th>
                        <th>Bog'chalar</th>
                        <th style="width:120px;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $contract->contract_number }}</strong></td>
                        <td>{{ $contract->contract_date->format('d.m.Y') }}</td>
                        <td>
                            <span class="text-success">{{ $contract->start_date->format('d.m.Y') }}</span>
                            &mdash;
                            <span class="text-danger">{{ $contract->end_date->format('d.m.Y') }}</span>
                        </td>
                        <td>
                            @if($contract->region)
                                <span class="badge bg-info text-dark">{{ $contract->region->region_name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($contract->kindgardens->count() > 0)
                                <span class="badge bg-secondary">{{ $contract->kindgardens->count() }} ta</span>
                                <small class="text-muted d-block">
                                    {{ $contract->kindgardens->pluck('number_of_org')->implode(', ') }}
                                </small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-warning me-1" title="Tahrirlash">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Shartnomani o\'chirishni tasdiqlaysizmi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            Hozircha shartnomalar yo'q. <a href="{{ route('contracts.create') }}">Yangi shartnoma qo'shing</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
