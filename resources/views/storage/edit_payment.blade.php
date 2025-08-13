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
.payment-type-badge {
    font-size: 1.1em;
    padding: 8px 16px;
}
</style>
@endsection

@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection

@section('content')
<div class="py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>To'lovni tahrirlash</h3>
        <a href="{{ route('storage.payment_history') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Orqaga qaytish
        </a>
    </div>

    <!-- To'lov turi ko'rsatish -->
    <div class="mb-4">
        @if($payment->payment_type == 'storage')
            <span class="badge bg-success payment-type-badge">
                <i class="fa fa-arrow-up"></i> Kirim to'lovi (Biz qarzimiz uchun)
            </span>
        @elseif($payment->payment_type == 'sale')
            <span class="badge bg-primary payment-type-badge">
                <i class="fa fa-arrow-down"></i> Sotuv to'lovi (Shop qarzi uchun)
            </span>
        @endif
    </div>

    <!-- Tahrirlash formasi -->
    <div class="payment-form">
        <h5 class="mb-3">
            @if($payment->payment_type == 'storage')
                💸 Kirim to'lovi - Biz qarzimiz uchun to'laymiz
            @else
                💰 Sotuv to'lovi - Shop qarzi uchun to'laydi
            @endif
        </h5>
        
        <form method="POST" action="{{ route('storage.update_payment', $payment->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Do'kon</label>
                    <select class="form-select" name="shop_id" required>
                        @foreach($shops as $shop)
                            <option value="{{ $shop['id'] }}" {{ $payment->shop_id == $shop['id'] ? 'selected' : '' }}>
                                {{ $shop['shop_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sana</label>
                    <select class="form-select" name="day_id" required>
                        @foreach($days as $day)
                            <option value="{{ $day['id'] }}" {{ $payment->day_id == $day['id'] ? 'selected' : '' }}>
                                {{ $day['day_number'].'.'.$day['month_name'].'.'.$day['year_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">To'lov ID</label>
                    <input type="text" class="form-control" value="#{{ $payment->id }}" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Naqt pul</label>
                    <input type="number" name="cash_amount" class="form-control" placeholder="Naqt pul" 
                           value="{{ $payment->cash_amount }}" oninput="calculateTotal(this)" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Karta pul</label>
                    <input type="number" name="card_amount" class="form-control" placeholder="Karta pul" 
                           value="{{ $payment->card_amount }}" oninput="calculateTotal(this)" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pul o'tkazish</label>
                    <input type="number" name="transfer_amount" class="form-control" placeholder="Pul o'tkazish" 
                           value="{{ $payment->transfer_amount }}" oninput="calculateTotal(this)" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Rasm</label>
                    <input type="file" name="image" class="form-control">
                    @if($payment->image)
                        <small class="text-muted">Mavjud rasm: {{ $payment->image }}</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Izoh</label>
                    <input type="text" name="description" class="form-control" placeholder="Izoh" 
                           value="{{ $payment->description }}">
                </div>
            </div>

            <!-- Mavjud to'lov ma'lumotlari -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h6>Mavjud to'lov ma'lumotlari:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Jami to'lov:</strong><br>
                                <span class="formatted-amount">{{ number_format($payment->total_amount, 0, ',', ' ') }} so'm</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Qarz yopildi:</strong><br>
                                <span class="text-primary">{{ number_format($payment->paid_to_debts, 0, ',', ' ') }} so'm</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Ortiqcha pul:</strong><br>
                                <span class="text-warning">{{ number_format($payment->excess_amount, 0, ',', ' ') }} so'm</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Yaratilgan:</strong><br>
                                <small>{{ $payment->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <strong>Yangi jami to'lov:</strong> <span id="newTotalAmount" class="formatted-amount">
                            {{ number_format($payment->total_amount, 0, ',', ' ') }} so'm
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('storage.payment_history') }}" class="btn btn-secondary me-2">
                    <i class="fa fa-times"></i> Bekor qilish
                </a>
                <button type="submit" class="btn btn-primary px-4" onclick="return confirm('To\'lovni yangilashni xohlaysizmi?')">
                    <i class="bi bi-check-circle"></i> To'lovni yangilash
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 

@section('js')
<script>
    // Jami summa hisoblash
    function calculateTotal(input) {
        const form = input.closest('form');
        const cash = parseFloat(form.querySelector('input[name="cash_amount"]').value) || 0;
        const card = parseFloat(form.querySelector('input[name="card_amount"]').value) || 0;
        const transfer = parseFloat(form.querySelector('input[name="transfer_amount"]').value) || 0;
        const total = cash + card + transfer;
        
        document.getElementById('newTotalAmount').textContent = formatAmount(total);
    }

    // Summa formatlash
    function formatAmount(amount) {
        return new Intl.NumberFormat('uz-UZ').format(amount) + ' so\'m';
    }

    // Sahifa yuklanganda jami summani hisoblash
    $(document).ready(function(){
        calculateTotal(document.querySelector('input[name="cash_amount"]'));
    });
</script>
@endsection 