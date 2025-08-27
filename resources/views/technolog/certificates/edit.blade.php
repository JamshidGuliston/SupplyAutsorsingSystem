@extends('layouts.app')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .preview-image {
        max-width: 200px;
        height: auto;
        margin-top: 10px;
    }
    .select2-container {
        width: 100% !important;
    }
    .current-files {
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .current-files img {
        max-width: 150px;
        height: auto;
    }
</style>
@endsection

@section('leftmenu')
    @include('technolog.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4">Sertifikatni tahrirlash</h1>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('technolog.home') }}">Bosh sahifa</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('technolog.certificates.index') }}">Sertifikatlar</a></li>
                    <li class="breadcrumb-item active">Tahrirlash</li>
                </ol>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('technolog.certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="certificate_number" class="form-label">Sertifikat raqami <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="certificate_number" name="certificate_number" 
                                           value="{{ old('certificate_number', $certificate->certificate_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Sertifikat nomi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $certificate->name) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Boshlanish sanasi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ old('start_date', $certificate->start_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Tugash sanasi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ old('end_date', $certificate->end_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="products" class="form-label">Maxsulotlar <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="products" name="products[]" multiple required>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        {{ in_array($product->id, old('products', $certificate->products->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Qo'shimcha ma'lumot</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $certificate->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pdf_file" class="form-label">PDF fayl</label>
                                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                                    <small class="text-muted">Maksimal hajm: 10MB</small>
                                    <div class="current-files">
                                        <p>Joriy PDF fayl: 
                                            <a href="{{ Storage::url($certificate->pdf_file) }}" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Ko'rish
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image_file" class="form-label">Rasm</label>
                                    <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                                    <small class="text-muted">Maksimal hajm: 5MB</small>
                                    <div id="imagePreview">
                                        @if($certificate->image_file)
                                            <div class="current-files">
                                                <p>Joriy rasm:</p>
                                                <img src="{{ Storage::url($certificate->image_file) }}" alt="Current image">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ $certificate->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Faol holat
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('technolog.certificates.index') }}" class="btn btn-secondary">Bekor qilish</a>
                            <button type="submit" class="btn btn-primary">Saqlash</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Select2 ni ishga tushirish
        $('.select2').select2({
            placeholder: "Maxsulotlarni tanlang",
            allowClear: true
        });

        // Rasm preview
        $('#image_file').change(function(){
            const file = this.files[0];
            if (file){
                const reader = new FileReader();
                reader.onload = function(e){
                    $('#imagePreview').html('<img src="'+ e.target.result +'" class="preview-image">');
                }
                reader.readAsDataURL(file);
            }
        });

        // Sanalarni tekshirish
        $('#end_date').change(function(){
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($(this).val());
            
            if(endDate <= startDate){
                alert('Tugash sanasi boshlanish sanasidan keyin bo\'lishi kerak!');
                $(this).val('');
            }
        });
    });
</script>
@endsection 