@extends('layouts.app')

@section('leftmenu')
    @include('storage.sidemenu');
@endsection

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    /* Modern card styles */
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }

    .badge {
        border-radius: 5px;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 6px 12px;
    }

    /* Date navigation */
    .date-navigation {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .year {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #495057;
        margin-bottom: 10px;
    }

    .month,
    .day {
        margin: 10px 0;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .month__item {
        padding: 8px 16px;
        text-align: center;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background-color: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .month__item:hover {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
        transform: translateY(-2px);
    }

    .month__item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .btn-add {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-qoldiq {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-qoldiq:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(240, 147, 251, 0.4);
        color: white;
    }

    /* Table improvements */
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 12px 8px;
    }

    .table tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    /* Modal improvements */
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-title {
        font-weight: 600;
    }

    /* Form controls */
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Export buttons */
    .export-buttons .btn {
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .export-buttons .btn:hover {
        transform: translateY(-2px);
    }

    /* Statistics badges */
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
    }

    .stat-badge i {
        font-size: 20px;
    }

    /* Search and filter */
    .search-filter-section {
        background-color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Link styling */
    a.text-primary:hover {
        color: #764ba2 !important;
        text-decoration: underline;
    }

    /* Icon buttons */
    .icon-btn {
        cursor: pointer;
        color: #667eea;
        transition: all 0.3s ease;
        font-size: 18px;
    }

    .icon-btn:hover {
        color: #764ba2;
        transform: scale(1.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .month__item {
            font-size: 12px;
            padding: 6px 10px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Modals -->
    @include('storage.partials.addedproducts_modals')

    <!-- Date Navigation -->
    <div class="date-navigation">
        <div class="year">
            {{ $year->year_name }}
        </div>
        <div class="month">
            @if($year->id != 1)
                <a href="/storage/addedproducts/{{ $year->id-1 }}/0" class="month__item">
                    <i class="fas fa-chevron-left"></i> {{ $year->year_name - 1 }}
                </a>
            @endif
            @foreach($months as $month)
                <a href="/storage/addedproducts/{{ $year->id }}/{{ $month->id }}"
                   class="month__item {{ ($month->id == $id) ? 'active' : '' }}">
                   {{ $month->month_name }}
                </a>
            @endforeach
            <a href="/storage/addedproducts/{{ $year->id+1 }}/0" class="month__item">
                {{ $year->year_name + 1 }} <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            <button class="btn btn-qoldiq w-100" onclick="hideModal(1)" data-bs-toggle="modal" data-bs-target="#addresidual">
                <i class="fas fa-box"></i> Qoldiq qo'shish
            </button>
        </div>
        <div class="col-md-6">
            <button class="btn btn-add w-100" onclick="hideModal(2)" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus-circle"></i> Mahsulot qo'shish
            </button>
        </div>
    </div>

    <!-- Kirim guruhlari Card -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h5 class="mb-0">
                <i class="fas fa-list-ul me-2"></i>Kirim guruhlari
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nomi</th>
                            <th scope="col">Sana</th>
                            <th scope="col" class="text-center">PDF</th>
                            <th scope="col" class="text-center">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($group as $item)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $item['id'] }}</span></td>
                            <td>
                                <a href="/storage/ingroup/{{ $item->id }}" class="text-primary fw-bold">
                                    {{ $item['group_name'] }}
                                </a>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $item['day_number'].".".$item['month_name'].".".$item['year_name'] }}
                            </td>
                            <td class="text-center">
                                <a href="/storage/document/{{ $item->id }}" target="_blank" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-edit icon-btn"
                                    data-title="{{ $item['group_name'] }}"
                                    data-id="{{ $item['id'] }}"
                                    data-dayid="{{ $item['dayid'] }}"
                                    data-yearid="{{ $year->id }}"
                                    data-monthid="{{ $id }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    title="Tahrirlash"></i>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p>Hech qanday kirim guruhlari topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Maxsulotlar qoldiqlari Card -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>Maxsulotlar qoldiqlari
                    </h5>
                </div>
                <div class="col-md-8">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" id="searchProduct" class="form-control form-control-sm"
                                   placeholder="🔍 Maxsulot qidirish..." style="background-color: rgba(255,255,255,0.9);">
                        </div>
                        <div class="col-md-4">
                            <select id="filterCategory" class="form-select form-select-sm" style="background-color: rgba(255,255,255,0.9);">
                                <option value="">Barcha kategoriyalar</option>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->pro_cat_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group btn-group-sm w-100 export-buttons">
                                <button onclick="exportToExcel()" class="btn btn-success" title="Excel yuklab olish">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button onclick="exportToPDF()" class="btn btn-danger" title="PDF yuklab olish">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="productsTable" class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Maxsulot nomi</th>
                            <th scope="col" class="text-center">O'lchov</th>
                            <th scope="col" class="text-center" style="background-color: #28a745; color: white;">
                                <i class="fas fa-arrow-down"></i> Kirim
                            </th>
                            <th scope="col" class="text-center" style="background-color: #dc3545; color: white;">
                                <i class="fas fa-arrow-up"></i> Chiqim
                            </th>
                            <th scope="col" class="text-center" style="background-color: #17a2b8; color: white;">
                                <i class="fas fa-box-open"></i> Qoldiq
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @php $counter = 1; @endphp
                        @forelse($productsData as $key => $item)
                        <tr class="product-row"
                            data-product-name="{{ strtolower($item['p_name']) }}"
                            data-category-id="{{ $item['category_id'] ?? '' }}">
                            <td class="text-center">{{ $counter++ }}</td>
                            <td><strong>{{ $item['p_name'] }}</strong></td>
                            <td class="text-center"><span class="badge bg-light text-dark">{{ $item['size_name'] }}</span></td>
                            <td class="text-center kirim-value">
                                <span class="text-success fw-bold">{{ number_format($item['kirim'], 2, '.', ' ') }}</span>
                            </td>
                            <td class="text-center chiqim-value">
                                <span class="text-danger fw-bold">{{ number_format($item['chiqim'], 2, '.', ' ') }}</span>
                            </td>
                            <td class="text-center qoldiq-value" data-qoldiq="{{ $item['qoldiq'] }}">
                                @if($item['qoldiq'] > 0)
                                    <span class="badge bg-success" style="font-size: 13px; padding: 6px 10px;">
                                        <i class="fas fa-plus-circle"></i> {{ number_format($item['qoldiq'], 2, '.', ' ') }}
                                    </span>
                                @elseif($item['qoldiq'] < 0)
                                    <span class="badge bg-danger" style="font-size: 13px; padding: 6px 10px;">
                                        <i class="fas fa-minus-circle"></i> {{ number_format($item['qoldiq'], 2, '.', ' ') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 13px; padding: 6px 10px;">
                                        <i class="fas fa-equals"></i> 0.00
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="noDataRow">
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                <p>Maxsulotlar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($productsData) > 0)
                    <tfoot class="table-light">
                        <tr style="border-top: 3px solid #667eea;">
                            <td colspan="3" class="text-end"><strong style="font-size: 16px;">JAMI:</strong></td>
                            <td class="text-center">
                                <strong class="text-success" style="font-size: 15px;">
                                    {{ number_format(array_sum(array_column($productsData, 'kirim')), 2, '.', ' ') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <strong class="text-danger" style="font-size: 15px;">
                                    {{ number_format(array_sum(array_column($productsData, 'chiqim')), 2, '.', ' ') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                @php
                                    $jamiQoldiq = array_sum(array_column($productsData, 'qoldiq'));
                                @endphp
                                @if($jamiQoldiq > 0)
                                    <strong class="text-success" style="font-size: 15px;">
                                        <i class="fas fa-plus-circle"></i> {{ number_format($jamiQoldiq, 2, '.', ' ') }}
                                    </strong>
                                @elseif($jamiQoldiq < 0)
                                    <strong class="text-danger" style="font-size: 15px;">
                                        <i class="fas fa-minus-circle"></i> {{ number_format($jamiQoldiq, 2, '.', ' ') }}
                                    </strong>
                                @else
                                    <strong class="text-secondary" style="font-size: 15px;">0.00</strong>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mb-4">
        <a href="/storage/home" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
@include('storage.partials.addedproducts_scripts')
@endsection
