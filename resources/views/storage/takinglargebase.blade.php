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

.product-row {
    border: 1px solid #dee2e6;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    background-color: #f8f9fa;
}
.remove-product {
    color: #dc3545;
    cursor: pointer;
    font-size: 18px;
}
.sale-info {
    background-color: #e3f2fd;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
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
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Sale + Take_products Modal -->
<div class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="saleModalLabel">Maxsulot sotish va olish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saleForm">
                <div class="modal-body">
                    <!-- Sale ma'lumotlari -->
                    <div class="sale-info">
                        <h6>Sotuv ma'lumotlari</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select mb-2" name="buyer_shop_id" required>
                                    <option value="">--Xaridor shop--</option>
                                    @foreach($shops as $shop)
                                        <option value="{{$shop->id}}">{{$shop->shop_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select mb-2" name="day_id" required>
                                    <option value="">--Sana--</option>
                                    @foreach($days as $day)
                                        <option value="{{$day->id}}">{{$day->day_number.".".$day->month_name.".".$day->year_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control mb-2" name="total_amount" placeholder="Jami summa" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control mb-2" name="paid_amount" placeholder="To'langan summa" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <textarea class="form-control mb-2" name="notes" placeholder="Izohlar"></textarea>
                            </div>
                            <div class="col-md-6">
                                <!-- <select class="form-select mb-2" name="user_id" required>
                                    <option value="">--Xodim--</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select> -->
                                <select class="form-select mb-2" name="outid" required>
                                    <option value="">--Sabab turi--</option>
                                    @foreach($outtypes as $outtype)
                                        <option value="{{$outtype->id}}">{{$outtype->outside_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Maxsulotlar ro'yxati -->
                    <div class="products-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>Maxsulotlar ro'yxati</h6>
                            <button type="button" class="btn btn-success btn-sm" id="addProductBtn" onclick="addProductRow()">
                                <i class="fa fa-plus"></i> Maxsulot qo'shish
                            </button>
                        </div>
                        
                        <div id="productsContainer">
                            <!-- Maxsulot qatorlari bu yerga qo'shiladi -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center me-auto">
                        <input type="checkbox" class="form-check-input me-2" id="confirmationCheckbox" required>
                        <label class="form-check-label" for="confirmationCheckbox">
                            Tasdiqlash
                        </label>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        Yaratish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Яратиш</button> -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#saleModal">
            <i class="fa fa-plus"></i> Maxsulot sotish
        </button>
    </div>
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
            </tr>
        </thead>
        <tbody>
            @foreach($res as $row)
                <tr>
                    <td>{{ $row->gid }}</td>
                    <td><a href="/storage/intakinglargebase/{{ $row->gid }}">{{ $row->title }}</a></td>
                    <td>{{ $row->outside_name }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
                    <td><a href="#">PDF</a></td>
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
let productCounter = 0;

// Modal ochilganda birinchi maxsulot qatorini qo'shish
document.getElementById('saleModal').addEventListener('show.bs.modal', function () {
    addProductRow();
});

// Maxsulot qatori qo'shish
function addProductRow() {
    productCounter++;
    const container = document.getElementById('productsContainer');
    
    const productRow = document.createElement('div');
    productRow.className = 'product-row';
    productRow.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6>Maxsulot #${productCounter}</h6>
            <span class="remove-product" onclick="removeProductRow(this)">×</span>
        </div>
        <div class="row">
            <div class="col-md-4">
                <select class="form-select mb-2" name="products[${productCounter}][product_id]" required>
                    <option value="">--Maxsulot--</option>
                    @foreach($products as $product)
                        <option value="{{$product->id}}">{{$product->product_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.001" class="form-control mb-2" name="products[${productCounter}][weight]" placeholder="Og'irlik (kg)" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control mb-2" name="products[${productCounter}][cost]" placeholder="Narx" required>
            </div>
        </div>
    `;
    
    container.appendChild(productRow);
}

// Maxsulot qatorini o'chirish
function removeProductRow(element) {
    if (document.querySelectorAll('.product-row').length > 0) {
        element.closest('.product-row').remove();
    }
}

// Checkbox holatini tekshirish
document.getElementById('confirmationCheckbox').addEventListener('change', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = !this.checked;
});

// Form yuborish
document.getElementById('saleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Checkbox tekshirish
    if (!document.getElementById('confirmationCheckbox').checked) {
        alert('Iltimos, tasdiqlash chekboxini belgilang!');
        return;
    }
    
    const formData = new FormData(this);
    
    // Maxsulotlar ma'lumotlarini to'plash
    const products = [];
    document.querySelectorAll('.product-row').forEach((row, index) => {
        const productId = row.querySelector('select[name*="[product_id]"]').value;
        const weight = row.querySelector('input[name*="[weight]"]').value;
        const cost = row.querySelector('input[name*="[cost]"]').value;
        
        if (productId && weight && cost) {
            products.push({
                product_id: productId,
                weight: weight,
                cost: cost,
            });
        }
    });
    
    // Form ma'lumotlarini tayyorlash
    const formDataObj = {
        buyer_shop_id: formData.get('buyer_shop_id'),
        day_id: formData.get('day_id'),
        total_amount: formData.get('total_amount'),
        paid_amount: formData.get('paid_amount'),
        notes: formData.get('notes'),
        user_id: formData.get('user_id'),
        outid: formData.get('outid'),
        products: products
    };
    
    fetch('/storage/createSaleWithTakeGroup', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formDataObj)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Muvaffaqiyatli saqlandi!');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi!');
    });
});

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