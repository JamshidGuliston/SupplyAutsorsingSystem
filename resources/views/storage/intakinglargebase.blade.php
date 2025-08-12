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

/* Jadval stillari */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.05);
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-dark {
    background-color: #343a40;
    color: white;
}

.table-info {
    background-color: #d1ecf1;
}

.text-end {
    text-align: right !important;
}

.text-center {
    text-align: center !important;
}

/* Modal stillari */
.modal-lg {
    max-width: 900px;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
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
                        <th>Narx (so'm/kg)</th>
                        <th style="text-align: right;">Jami narx (so'm)</th>
                    </tr>
                </thead>
                <?php $total_price = 0; ?>
                <tbody>
                    @foreach($res as $row)
                    <tr>
                        <td>{{ $row->tid }}</td>
                        <td>{{ $row->product_name }}</td>
                        <td>{{ $row->size_name }}</td>
                        <td>{{ $row->weight }}</td>
                        <td>{{ number_format($row->cost, 0, ',', ' ') }} so'm</td>
                        <td style="text-align: right;">{{ number_format($row->cost * $row->weight, 0, ',', ' ') }} so'm</td>
                        <?php $total_price += $row->cost * $row->weight; ?>
                    </tr>
                    @endforeach
                    <tr style="border-top: 2px solid #000;">
                        <td colspan="4" style="text-align: right;"><strong>Umumiy summa:</strong></td>
                        <td></td>
                        <td style="text-align: right;"><strong>{{ number_format($total_price, 0, ',', ' ') }} so'm</strong></td>
                    </tr>
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
// Narxni formatlash funksiyasi
function formatPrice(price) {
    return new Intl.NumberFormat('uz-UZ').format(price) + ' so\'m';
}

// Sale ma'lumotlarini ko'rish
function viewSaleDetails(saleId) {
    fetch(`/storage/getSaleDetails/${saleId}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Umumiy narxni hisoblash
                let totalProductPrice = 0;
                data.products.forEach(product => {
                    totalProductPrice += product.weight * product.cost;
                });
                
                document.getElementById('saleDetailsContent').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Faktura raqami:</strong> ${data.sale.invoice_number}</p>
                            <p><strong>Xaridor:</strong> ${data.sale.buyer_shop.shop_name}</p>
                            <p><strong>Sana:</strong> ${data.sale.day.day_number}.${data.sale.day.month_name}.${data.sale.day.year_name}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jami summa:</strong> ${formatPrice(data.sale.total_amount)}</p>
                            <p><strong>To'langan:</strong> ${formatPrice(data.sale.paid_amount)}</p>
                            <p><strong>Qarz:</strong> ${formatPrice(data.sale.debt_amount)}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Maxsulotlar:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>№</th>
                                    <th>Maxsulot</th>
                                    <th>Og'irlik (kg)</th>
                                    <th>Narx (so'm/kg)</th>
                                    <th>Jami (so'm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.products.map((product, index) => `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td><strong>${product.product.product_name}</strong></td>
                                        <td class="text-center">${product.weight}</td>
                                        <td class="text-end">${formatPrice(product.cost)}</td>
                                        <td class="text-end"><strong>${formatPrice(product.weight * product.cost)}</strong></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="table-info">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Umumiy narx:</strong></td>
                                    <td class="text-end"><strong>${formatPrice(totalProductPrice)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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