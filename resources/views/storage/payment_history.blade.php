@extends('layouts.app')

@section('css')
<style>
.w-5{
    width: 2%;
    text-decoration: none;
}
.flex-1{
    display: none;
}
.payment-type-btn {
    margin-right: 10px;
    margin-bottom: 10px;
}
.payment-form {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f8f9fa;
}
.formatted-amount {
    font-weight: bold;
    color: #198754;
}
</style>
@endsection

@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection

@section('content')
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">To'lovni o'chirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('storage.delete_payment')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="hidden" name="payment_id" id="payment_id" required>
                            <p>To'lovni o'chirishni xohlaysizmi?</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger">O'chirish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="py-4 px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <h3>To'lovlar</h3>
    
    <!-- To'lov turi tanlash -->
    <div class="mb-3">
        <button class="btn btn-success payment-type-btn" type="button" data-bs-toggle="collapse" data-bs-target="#storagePaymentForm" aria-expanded="false">
            <i class="fa fa-arrow-up"></i> Kirim to'lovi (Biz qarzimiz uchun)
        </button>
        <button class="btn btn-primary payment-type-btn" type="button" data-bs-toggle="collapse" data-bs-target="#salePaymentForm" aria-expanded="false">
            <i class="fa fa-arrow-down"></i> Sotuv to'lovi (Shop qarzi uchun)
        </button>
    </div>

    <!-- Kirim to'lovi formasi (biz qarzimiz uchun) -->
    <div class="collapse" id="storagePaymentForm">
        <div class="payment-form">
            <h5 class="text-success mb-3">💸 Kirim to'lovi - Biz qarzimiz uchun to'laymiz</h5>
            <form method="POST" action="{{route('storage.createpay')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="payment_type" value="storage">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Do'kon</label>
                        <select class="form-select" name="catid" required>
                            @foreach($shops as $row)
                                <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sana</label>
                        <select class="form-select" name="dayid" required>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Naqt pul</label>
                        <input type="number" name="cash_amount" class="form-control" placeholder="Naqt pul" value="0" oninput="calculateTotal(this)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Karta pul</label>
                        <input type="number" name="card_amount" class="form-control" placeholder="Karta pul" value="0" oninput="calculateTotal(this)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pul o'tkazish</label>
                        <input type="number" name="transfer_amount" class="form-control" placeholder="Pul o'tkazish" value="0" oninput="calculateTotal(this)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Rasm</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Izoh</label>
                        <input type="text" name="description" class="form-control" placeholder="Izoh">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Jami to'lov:</strong> <span id="storageTotalAmount" class="formatted-amount">0 so'm</span>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4" onclick="return confirm('Kirim to\'lovini qo\'shishni xohlaysizmi?')">
                        <i class="bi bi-plus-circle"></i> Kirim to'lovi qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sotuv to'lovi formasi (shop qarzi uchun) -->
    <div class="collapse" id="salePaymentForm">
        <div class="payment-form">
            <h5 class="text-primary mb-3">💰 Sotuv to'lovi - Shop qarzi uchun to'laydi</h5>
            <form method="POST" action="{{route('storage.createSalePayment')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="payment_type" value="sale">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Do'kon</label>
                        <select class="form-select" name="shop_id" id="saleShopSelect" required onchange="loadShopSales()">
                            <option value="">Do'konni tanlang</option>
                            @foreach($shops as $row)
                                <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sana</label>
                        <select class="form-select" name="day_id" required>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{$row['day_number'].'.'.$row['month_name'].'.'.$row['year_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sotuv tanlash (ixtiyoriy)</label>
                        <select class="form-select" name="sale_id" id="saleSelect">
                            <option value="">Avtomatik taqsimlash</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Naqt pul</label>
                        <input type="number" name="cash_amount" class="form-control" placeholder="Naqt pul" value="0" oninput="calculateSaleTotal(this)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Karta pul</label>
                        <input type="number" name="card_amount" class="form-control" placeholder="Karta pul" value="0" oninput="calculateSaleTotal(this)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pul o'tkazish</label>
                        <input type="number" name="transfer_amount" class="form-control" placeholder="Pul o'tkazish" value="0" oninput="calculateSaleTotal(this)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Rasm</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Izoh</label>
                        <input type="text" name="description" class="form-control" placeholder="Izoh">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-primary">
                            <strong>Jami to'lov:</strong> <span id="saleTotalAmount" class="formatted-amount">0 so'm</span>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Sotuv to\'lovini qo\'shishni xohlaysizmi?')">
                        <i class="bi bi-plus-circle"></i> Sotuv to'lovi qo'shish
                    </button>
                </div>
            </form>
        </div>
    </div>

<hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">To'lov turi</th>
                <th scope="col">Firma</th>
                <th scope="col">Umumiy pul</th>
                <th scope="col">Naqt pul</th>
                <th scope="col">Karta pul</th>
                <th scope="col">Pul o'tkazish</th>
                <th scope="col">Qarz yopildi</th>
                <th scope="col">Ortiqcha pul</th>
                <th scope="col">Sana</th>
                <th scope="col">Rasm</th>
                <th scope="col">Izoh</th>
                <th scope="col">Amallar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>
                        @if($payment->payment_type == 'storage')
                            <span class="badge bg-success"><i class="fa fa-arrow-up"></i> Kirim to'lovi</span>
                        @elseif($payment->payment_type == 'sale')
                            <span class="badge bg-primary"><i class="fa fa-arrow-down"></i> Sotuv to'lovi</span>
                        @else
                            <span class="badge bg-secondary">{{ $payment->payment_type }}</span>
                        @endif
                    </td>
                    <td>{{ $payment->shop_name }}</td>
                    <td class="formatted-amount">{{ number_format($payment->total_amount, 0, ',', ' ') }} so'm</td>
                    <td>{{ number_format($payment->cash_amount, 0, ',', ' ') }} so'm</td>
                    <td>{{ number_format($payment->card_amount, 0, ',', ' ') }} so'm</td>
                    <td>{{ number_format($payment->transfer_amount, 0, ',', ' ') }} so'm</td>
                    <td>{{ number_format($payment->paid_to_debts, 0, ',', ' ') }} so'm</td>
                    <td>{{ number_format($payment->excess_amount, 0, ',', ' ') }} so'm</td>
                    <td>{{ $payment->day_number.'.'.$payment->month_name.'.'.$payment->year_name }}</td>
                    <td>{{ $payment->image }}</td>
                    <td>{{ $payment->description }}</td>
                    <td>
                        <a href="{{ route('storage.edit_payment', $payment->id) }}" class="btn btn-sm btn-warning me-1" title="Tahrirlash">
                            <i class="fa fa-edit"></i>
                        </a>
                        <!-- <i class="fa fa-trash deletepay" data-bs-toggle="modal" data-bs-target="#deleteModal" style="cursor: pointer; color:crimson" data-payment-id="{{ $payment->id }}"></i> -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="row">
        <div class="col-md-6">
            <p class="text-muted small">{{ $payments->firstItem() }} dan {{ $payments->lastItem() }} gacha, jami {{ $payments->total() }} to'lov</p>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            {{ $payments->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <br>
</div>
@endsection 

@section('js')
<script>
    // Kirim to'lovi uchun jami summa hisoblash
    function calculateTotal(input) {
        const form = input.closest('form');
        const cash = parseFloat(form.querySelector('input[name="cash_amount"]').value) || 0;
        const card = parseFloat(form.querySelector('input[name="card_amount"]').value) || 0;
        const transfer = parseFloat(form.querySelector('input[name="transfer_amount"]').value) || 0;
        const total = cash + card + transfer;
        
        document.getElementById('storageTotalAmount').textContent = formatAmount(total);
    }

    // Sotuv to'lovi uchun jami summa hisoblash
    function calculateSaleTotal(input) {
        const form = input.closest('form');
        const cash = parseFloat(form.querySelector('input[name="cash_amount"]').value) || 0;
        const card = parseFloat(form.querySelector('input[name="card_amount"]').value) || 0;
        const transfer = parseFloat(form.querySelector('input[name="transfer_amount"]').value) || 0;
        const total = cash + card + transfer;
        
        document.getElementById('saleTotalAmount').textContent = formatAmount(total);
    }

    // Summa formatlash
    function formatAmount(amount) {
        return new Intl.NumberFormat('uz-UZ').format(amount) + ' so\'m';
    }

    // Shop sotuvlarini yuklash
    function loadShopSales() {
        const shopId = document.getElementById('saleShopSelect').value;
        const saleSelect = document.getElementById('saleSelect');
        
        if (!shopId) {
            saleSelect.innerHTML = '<option value="">Avtomatik taqsimlash</option>';
            return;
        }

        fetch(`/storage/getShopSales/${shopId}`)
            .then(response => response.json())
            .then(data => {
                saleSelect.innerHTML = '<option value="">Avtomatik taqsimlash</option>';
                data.sales.forEach(sale => {
                    const option = document.createElement('option');
                    option.value = sale.id;
                    option.textContent = `Sotuv #${sale.invoice_number} - ${formatAmount(sale.debt_amount)} qarz`;
                    saleSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    $(document).ready(function(){
        $('.deletepay').on('click', function(event){
            var paymentId = $(this).data('payment-id');
            $('#payment_id').val(paymentId); 
        });
    });
</script>
@endsection