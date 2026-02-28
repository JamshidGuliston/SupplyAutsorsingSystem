@extends('layouts.app')

@section('leftmenu')
@include('accountant.sidemenu');
@endsection

@section('css')
<style>
    .kindgarden-list {
        max-height: 340px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px;
    }
    .kindgarden-item {
        padding: 4px 6px;
        border-radius: 4px;
    }
    .kindgarden-item:hover {
        background: #f0f4ff;
    }
    .kindgarden-item.hidden-item {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row my-3">
        <div class="col">
            <h4>
                <i class="fas fa-file-contract me-2"></i>
                {{ isset($contract) ? 'Shartnomani tahrirlash' : 'Yangi shartnoma' }}
            </h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($contract) ? route('contracts.update', $contract) : route('contracts.store') }}"
                  method="POST">
                @csrf
                @if(isset($contract))
                    @method('PUT')
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row g-3">
                    {{-- Shartnoma raqami --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Shartnoma raqami <span class="text-danger">*</span></label>
                        <input type="text" name="contract_number" class="form-control @error('contract_number') is-invalid @enderror"
                               value="{{ old('contract_number', $contract->contract_number ?? '') }}"
                               placeholder="№ 25111006438231" required>
                        @error('contract_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Shartnoma sanasi --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Shartnoma sanasi <span class="text-danger">*</span></label>
                        <input type="date" name="contract_date" class="form-control @error('contract_date') is-invalid @enderror"
                               value="{{ old('contract_date', isset($contract) ? $contract->contract_date->format('Y-m-d') : '') }}" required>
                        @error('contract_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4"></div>

                    {{-- Amal qilish muddati --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amal qilish boshlanishi <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', isset($contract) ? $contract->start_date->format('Y-m-d') : '') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Amal qilish tugashi <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', isset($contract) ? $contract->end_date->format('Y-m-d') : '') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4"></div>

                    {{-- Tuman bo'yicha --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tuman bo'yicha (ixtiyoriy)</label>
                        <select name="region_id" id="regionSelect" class="form-select @error('region_id') is-invalid @enderror"
                                onchange="filterKindgardens()">
                            <option value="">— Tumanlarni tanlang —</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}"
                                    {{ old('region_id', $contract->region_id ?? '') == $region->id ? 'selected' : '' }}>
                                    {{ $region->region_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Tuman tanlasangiz, o'sha tumandagi barcha bog'chalarga avtomatik qo'llaniladi.</small>
                    </div>

                    {{-- Alohida bog'chalar --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Alohida bog'chalar (ixtiyoriy)</label>
                        <div class="mb-1">
                            <input type="text" id="kindgardenSearch" class="form-control form-control-sm"
                                   placeholder="Bog'cha qidirish..." oninput="searchKindgardens()">
                        </div>
                        <div class="kindgarden-list" id="kindgardenList">
                            @foreach($kindgardens as $kg)
                            <div class="kindgarden-item form-check"
                                 data-region="{{ $kg->region_id }}"
                                 data-name="{{ mb_strtolower($kg->kingar_name) }} {{ $kg->number_of_org }}">
                                <input class="form-check-input" type="checkbox"
                                       name="kindgarden_ids[]"
                                       value="{{ $kg->id }}"
                                       id="kg_{{ $kg->id }}"
                                       {{ in_array($kg->id, old('kindgarden_ids', $selectedKindgardens ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="kg_{{ $kg->id }}">
                                    <strong>{{ $kg->number_of_org }}</strong> — {{ $kg->kingar_name }}
                                    <small class="text-muted">({{ $kg->region->region_name ?? '' }})</small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAll()">Barchasini belgilash</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="uncheckAll()">Barchasini bekor qilish</button>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function filterKindgardens() {
        var regionId = document.getElementById('regionSelect').value;
        var items = document.querySelectorAll('#kindgardenList .kindgarden-item');
        items.forEach(function(item) {
            if (!regionId || item.dataset.region == regionId) {
                item.classList.remove('hidden-item');
            } else {
                item.classList.add('hidden-item');
            }
        });
    }

    function searchKindgardens() {
        var q = document.getElementById('kindgardenSearch').value.toLowerCase();
        var regionId = document.getElementById('regionSelect').value;
        var items = document.querySelectorAll('#kindgardenList .kindgarden-item');
        items.forEach(function(item) {
            var matchesSearch = !q || item.dataset.name.includes(q);
            var matchesRegion = !regionId || item.dataset.region == regionId;
            item.classList.toggle('hidden-item', !(matchesSearch && matchesRegion));
        });
    }

    function checkAll() {
        var items = document.querySelectorAll('#kindgardenList .kindgarden-item:not(.hidden-item) input[type=checkbox]');
        items.forEach(function(cb) { cb.checked = true; });
    }

    function uncheckAll() {
        var items = document.querySelectorAll('#kindgardenList .kindgarden-item:not(.hidden-item) input[type=checkbox]');
        items.forEach(function(cb) { cb.checked = false; });
    }

    // Run filter on page load if region is pre-selected
    filterKindgardens();
</script>
@endsection
