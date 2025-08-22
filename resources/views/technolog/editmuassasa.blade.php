@extends('layouts.app')

@section('css')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f8f9fa;
    }

    .checkbox-item.selected {
        background: #e3f2fd;
        border-color: #2196f3;
    }

    .checkbox-item input[type="checkbox"] {
        margin-right: 10px;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="form-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $kindgarden->kingar_name }} - Tahrirlash</h2>
            <a href="{{ route('technolog.muassasalar') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Orqaga
            </a>
        </div>

        <form method="POST" action="{{ route('technolog.updatemuassasa') }}">
            @csrf
            <input type="hidden" name="kindgarden_id" value="{{ $kindgarden->id }}">
            
            <div class="form-group row">
                <label for="kingar_name" class="col-sm-3 col-form-label"><strong>Muassasa nomi *</strong></label>
                <div class="col-sm-9">
                    <input type="text" name="kingar_name" class="form-control" id="kingar_name" required value="{{ $kindgarden->kingar_name }}" placeholder="Muassasa nomini kiriting">
                </div>
            </div>

            <div class="form-group row">
                <label for="short_name" class="col-sm-3 col-form-label"><strong>Muassasa kod *</strong></label>
                <div class="col-sm-9">
                    <input type="text" name="short_name" class="form-control" id="short_name" required value="{{ $kindgarden->short_name }}" placeholder="Muassasa kodini kiriting">
                </div>
            </div>

            <div class="form-group row">
                <label for="number_of_org" class="col-sm-3 col-form-label"><strong>Tashkilot № *</strong></label>
                <div class="col-sm-9">
                    <input type="text" name="number_of_org" class="form-control" id="number_of_org" required value="{{ $kindgarden->number_of_org }}" placeholder="Tashkilot raqamini kiriting">
                </div>
            </div>

            <div class="form-group row">
                <label for="region_id" class="col-sm-3 col-form-label"><strong>Tuman *</strong></label>
                <div class="col-sm-9">
                    <select class="form-select" name="region_id" required>
                        <option value="">-- Tumanni tanlang --</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ $kindgarden->region_id == $region->id ? 'selected' : '' }}>
                                {{ $region->region_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="worker_count" class="col-sm-3 col-form-label"><strong>Xodimlar soni *</strong></label>
                <div class="col-sm-9">
                    <input type="number" name="worker_count" class="form-control" id="worker_count" required min="0" value="{{ $kindgarden->worker_count }}" placeholder="Xodimlar sonini kiriting">
                </div>
            </div>

            <div class="form-group row">
                <label for="hide" class="col-sm-3 col-form-label"><strong>Holati</strong></label>
                <div class="col-sm-9">
                    <select class="form-select" name="hide">
                        <option value="1" {{ $kindgarden->hide == 1 ? 'selected' : '' }}>Faol</option>
                        <option value="0" {{ $kindgarden->hide == 0 ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label"><strong>Yosh guruhlari *</strong></label>
                <div class="col-sm-9">
                    <p class="text-muted small">Ushbu muassasada mavjud yosh guruhlarini tanlang:</p>
                    <div class="checkbox-group">
                        @foreach($ages as $age)
                            @php
                                $isSelected = $kindgarden->age_range->contains('id', $age->id);
                            @endphp
                            <div class="checkbox-item {{ $isSelected ? 'selected' : '' }}">
                                <input type="checkbox" 
                                       name="yongchek[]" 
                                       value="{{ $age->id }}" 
                                       id="age_{{ $age->id }}"
                                       {{ $isSelected ? 'checked' : '' }}>
                                <label for="age_{{ $age->id }}" class="mb-0">{{ $age->age_name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-group row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> O'zgarishlarni saqlash
                    </button>
                    <a href="{{ route('technolog.muassasalar') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i> Bekor qilish
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Form validation
    $('form').on('submit', function(e) {
        let checkedAges = $('input[name="yongchek[]"]:checked').length;
        if(checkedAges === 0) {
            e.preventDefault();
            alert('Kamida bitta yosh guruhini tanlang!');
            return false;
        }
    });

    // Checkbox style toggle
    $('input[type="checkbox"]').on('change', function() {
        const $item = $(this).closest('.checkbox-item');
        if($(this).is(':checked')) {
            $item.addClass('selected');
        } else {
            $item.removeClass('selected');
        }
    });
});
</script>
@endsection 