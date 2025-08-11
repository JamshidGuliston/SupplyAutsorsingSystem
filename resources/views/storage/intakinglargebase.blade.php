@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
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
<div class="row py-1 px-4">
    <h3>Katta ombor - Sotilgan maxsulotlar</h3>
    
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-light">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Maxsulot</th>
                        <th>O'lcham</th>
                        <th>Og'irlik (kg)</th>
                        <th>Narx</th>
                        <th>Sotuv ma'lumotlari</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($res as $row)
                    <tr>
                        <td>{{ $row->tid }}</td>
                        <td>{{ $row->product_name }}</td>
                        <td>{{ $row->size_name }}</td>
                        <td>{{ $row->weight }}</td>
                        <td>{{ $row->cost }}</td>
                        <td>
                            @if($row->sale_id)
                                <button class="btn btn-sm btn-info" onclick="viewSaleDetails({{ $row->sale_id }})">
                                    <i class="fa fa-eye"></i> Sotuv #{{ $row->sale_id }}
                                </button>
                            @else
                                <span class="text-muted">Sotilmagan</span>
                            @endif
                        </td>
                        <td>
                            @if($row->sale_id)
                                <button class="btn btn-sm btn-warning" onclick="editSale({{ $row->sale_id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sale ma'lumotlarini ko'rish modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="saleDetailsModalLabel">Sotuv ma'lumotlari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Sale ma'lumotlari bu yerga yuklanadi -->
            </div>
        </div>
    </div>
</div>

<script>
// Sale ma'lumotlarini ko'rish
function viewSaleDetails(saleId) {
    fetch(`/storage/getSaleDetails/${saleId}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('saleDetailsContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Faktura raqami:</strong> ${data.sale.invoice_number}</p>
                            <p><strong>Xaridor:</strong> ${data.sale.buyer_shop.shop_name}</p>
                            <p><strong>Sana:</strong> ${data.sale.day.day_number}.${data.sale.day.month_name}.${data.sale.day.year_name}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jami summa:</strong> ${data.sale.total_amount} so'm</p>
                            <p><strong>To'langan:</strong> ${data.sale.paid_amount} so'm</p>
                            <p><strong>Qarz:</strong> ${data.sale.debt_amount} so'm</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Maxsulotlar:</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Maxsulot</th>
                                <th>Og'irlik</th>
                                <th>Narx</th>
                                <th>Jami</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.products.map(product => `
                                <tr>
                                    <td>${product.product.product_name}</td>
                                    <td>${product.weight} kg</td>
                                    <td>${product.cost} so'm</td>
                                    <td>${product.weight * product.cost} so'm</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                new bootstrap.Modal(document.getElementById('saleDetailsModal')).show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xatolik yuz berdi!');
        });
}

function editSale(saleId) {
    // Sotuvni tahrirlash funksiyasi
    alert('Sotuvni tahrirlash funksiyasi keyingi versiyada qo\'shiladi');
}
</script>
@endsection