@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('css')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .card-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border-bottom: none;
    }
    
    .form-label {
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 14px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        transform: scale(1.02);
        transition: all 0.3s ease;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .form-control:hover, .form-select:hover {
        border-color: #007bff;
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.7rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
    }
    
    .btn-secondary {
        background: linear-gradient(45deg, #6c757d, #5a6268);
        border: none;
    }
    
    .table {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table th {
        background-color: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem 0.75rem;
    }
    
    .table td {
        border: none;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table tr:hover {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .text-danger {
        font-weight: bold;
    }
    
    .documentnumber {
        color: #007bff;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
        
        .form-control-lg, .form-select-lg {
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
        }
    }
    
    /* Modal stillar */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    .modal-header {
        border-radius: 15px 15px 0 0;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 1.5rem;
        border-radius: 0 0 15px 15px;
    }
    
    .btn-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800);
        border: none;
        color: white !important;
    }
    
    .editess, .detete {
        transition: all 0.3s ease;
        padding: 0.5rem;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .editess:hover {
        background-color: rgba(0, 123, 255, 0.1);
        color: #007bff !important;
        transform: scale(1.1);
    }
    
    .detete:hover {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545 !important;
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="exampleModalss" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bu mahsulotni o'chirasizmi
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn dele btn-danger">O'chirish</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->

<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form action="{{route('technolog.editproductfood')}}" method="POST">
            @csrf
            <input type="hidden" name="titleid" value="{{$id}}">
            <div id="hiddenid">
            </div>
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title fw-bold" id="exampleModalLabel">
                    <i class="fas fa-edit me-2"></i>Mahsulotni tahrirlash
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Mahsulot <span class="text-danger">*</span></label>
                        <select class="form-select" name="productid" required aria-label="Default select example">
                            <option value="" id="editProductSelect" selected>--Mahsulotlar--</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Miqdori (gram) <span class="text-danger">*</span></label>
                        <input type="number" step="0.001" min="0" class="form-control" name="gram" id="editGramInput" placeholder="Gram miqdorini kiriting" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Chiqindisiz (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="weight_without_waste" id="editWeightInput" placeholder="Chiqindisiz miqdor">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Oqsillar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="proteins" id="editProteinsInput" placeholder="Oqsillar miqdori">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Yog'lar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="fats" id="editFatsInput" placeholder="Yog'lar miqdori">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Uglevodlar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="carbohydrates" id="editCarbohydratesInput" placeholder="Uglevodlar miqdori">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Kaloriya</label>
                        <input type="number" step="0.001" min="0" class="form-control" name="kcal" id="editKcalInput" placeholder="Kaloriya miqdori">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Bekor qilish
                </button>
                <button type="submit" class="btn btn-warning text-white">
                    <i class="fas fa-save me-1"></i>Saqlash
                </button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->

<div class="box-products py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="text-title">
                <h3 class="documentnumber">{{ (empty($food[0]->food_name)) ? "" :  $food[0]->food_name }}</h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Yangi mahsulot qo'shish</h5>
        </div>
        <div class="card-body">
            <form action="{{route('technolog.addproductfood')}}" method="POST">
                @csrf
                <input type="hidden" name="titleid" value="{{$id}}">
                
                <!-- Birinchi qator -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mahsulot <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" name="productid" required aria-label="Mahsulot tanlash">
                            <option value="">-- Mahsulotni tanlang --</option>
                            @foreach($productall as $all)
                            @if(!isset($all['ok']))
                            <option value="{{$all['id']}}">{{$all['product_name']}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Miqdori (gram) <span class="text-danger">*</span></label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="gram" placeholder="Masalan: 100.5" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Chiqindisiz (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="weight_without_waste" placeholder="Masalan: 85.2">
                    </div>
                </div>
                
                <!-- Ikkinchi qator -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Oqsillar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="proteins" placeholder="Masalan: 12.5">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Yog'lar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="fats" placeholder="Masalan: 8.3">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Uglevodlar (gram)</label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="carbohydrates" placeholder="Masalan: 15.7">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Kaloriya</label>
                        <input type="number" step="0.001" min="0" class="form-control form-control-lg" name="kcal" placeholder="Masalan: 150">
                    </div>
                </div>
                
                <!-- Tugma qatori -->
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="reset" class="btn btn-secondary btn-lg me-2">
                            <i class="fas fa-times me-1"></i>Tozalash
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-plus me-1"></i>Qo'shish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row py-1 px-4">
    <div class="col-md-12">
        <div class="table">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col">Miqdori (gram)</th>
                        <th scope="col">Chiqindisiz (gr)</th>
                        <th scope="col">Oqsillar (gr)</th>
                        <th scope="col">Yog'lar (gr)</th>
                        <th scope="col">Uglevodlar (gr)</th>
                        <th scope="col">Kaloriya</th>
                        <th scope="col" style="text-align: end;">...</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($food as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->gram }} gr</td>
                        <td>{{ $item->weight_without_waste ?? '-' }}</td>
                        <td>{{ $item->proteins ?? '-' }}</td>
                        <td>{{ $item->fats ?? '-' }}</td>
                        <td>{{ $item->carbohydrates ?? '-' }}</td>
                        <td>{{ $item->kcal ?? '-' }}</td>
                        <td style="text-align: end;">
                            <!-- <i data-edites-id="{{ $item->id }}" 
                               data-productid="{{ $item->productid }}"
                               data-productname="{{ $item->product_name }}"
                               data-gram="{{ $item->gram }}" 
                               data-weight="{{ $item->weight_without_waste }}"
                               data-proteins="{{ $item->proteins }}"
                               data-fats="{{ $item->fats }}"
                               data-carbohydrates="{{ $item->carbohydrates }}"
                               data-kcal="{{ $item->kcal }}"
                               class="editess far fa-edit text-info" 
                               data-bs-toggle="modal" 
                               data-bs-target="#exampleModal" 
                               style="cursor: pointer; margin-right: 16px;"></i> -->
                            <i class="detete  fa fa-trash" aria-hidden="true" data-delet-id="{{$item->id}}" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#exampleModalss"></i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/technolog/food">Orqaga</a>
    </div>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function() {
        
        // Modal ochilganda mahsulotlar ro'yxatini to'ldirish
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            if (button.hasClass('editess')) {
                // Mahsulotlar ro'yxatini olish
                var selectElement = $('#exampleModal select[name="productid"]');
                
                // Avval tozalash
                selectElement.html('<option value="">-- Mahsulotni tanlang --</option>');
                
                // Mahsulotlar ro'yxatini qo'shish
                @foreach($productall as $all)
                @if(!isset($all['ok']))
                selectElement.append('<option value="{{ $all['id'] }}">{{ $all['product_name'] }}</option>');
                @endif
                @endforeach
            }
        });
        
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            var productid = $(this).attr('data-productid');
            var productname = $(this).attr('data-productname');
            var gram = $(this).attr('data-gram');
            var weight = $(this).attr('data-weight');
            var proteins = $(this).attr('data-proteins');
            var fats = $(this).attr('data-fats');
            var carbohydrates = $(this).attr('data-carbohydrates');
            var kcal = $(this).attr('data-kcal');
            
            var div = $('#hiddenid');
            div.html("<input type='hidden' name='id' value="+g+">");
            
            // Timeout bilan productid ni set qilish (select to'ldirilganidan keyin)
            setTimeout(function() {
                $('#exampleModal select[name="productid"]').val(productid);
            }, 100);
            
            $('#editGramInput').val(gram);
            $('#editWeightInput').val(weight || '');
            $('#editProteinsInput').val(proteins || '');
            $('#editFatsInput').val(fats || '');
            $('#editCarbohydratesInput').val(carbohydrates || '');
            $('#editKcalInput').val(kcal || '');
        });

        $('.detete').click(function() {
            deletes = $(this).attr('data-delet-id');
        });

        $('.dele').click(function() {
            var del = deletes
            $.ajax({
                method: "GET",
                url: '/technolog/deleteproductfood',
                data: {
                    'id': del,
                },
                success: function(data) {
                    location.reload();
                }

            })
        })

    });
</script>
@endsection