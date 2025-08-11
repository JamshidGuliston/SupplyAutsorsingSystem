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
    <h3>To'lovlar</h3>
    <!-- Ochish/Yopish tugmasi -->
    <button class="btn btn-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#paymentForm" aria-expanded="false" aria-controls="paymentForm">
        <i class="bi bi-caret-down"></i> To'lov qo'shish
    </button>

    <!-- Yashirin/Ochilib-yopiladigan forma -->
    <div class="collapse" id="paymentForm">
        <form method="POST" action="{{route('storage.createpay')}}" enctype="multipart/form-data">
            @csrf
            <div class="container p-4 bg-light rounded shadow-sm">
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
                        <input type="number" name="cash_amount" class="form-control" placeholder="Naqt pul" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Karta pul</label>
                        <input type="number" name="card_amount" class="form-control" placeholder="Karta pul" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pul o'tkazish</label>
                        <input type="number" name="transfer_amount" class="form-control" placeholder="Pul o'tkazish" value="0">
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

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4" onclick="return confirm('To\'lovni qo\'shishni xohlaysizmi?')">
                        <i class="bi bi-plus-circle"></i> To'lov qo'shish
                    </button>
                </div>
            </div>
        </form>
    </div>
<hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
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
                    <td>{{ $payment->shop_name }}</td>
                    <td>{{ number_format($payment->total_amount) }} so'm</td>
                    <td>{{ number_format($payment->cash_amount) }} so'm</td>
                    <td>{{ number_format($payment->card_amount) }} so'm</td>
                    <td>{{ number_format($payment->transfer_amount) }} so'm</td>
                    <td>{{ number_format($payment->paid_to_debts) }} so'm</td>
                    <td>{{ number_format($payment->excess_amount) }} so'm</td>
                    <td>{{ $payment->day_number.'.'.$payment->month_name.'.'.$payment->year_name }}</td>
                    <td>{{ $payment->image }}</td>
                    <td>{{ $payment->description }}</td>
                    <td>
                        <i class="fa fa-trash deletepay" data-bs-toggle="modal" data-bs-target="#deleteModal" style="cursor: pointer; color:crimson" data-payment-id="{{ $payment->id }}"></i>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $payments->links() }}
    <br>
</div>
@endsection 

@section('js')
<script>
    $(document).ready(function(){
        $('.deletepay').on('click', function(event){
            alert('delete');
            var button = $(event.relatedTarget);
            var paymentId = button.data('payment-id');
            $('#payment_id').val(paymentId); 

        });
    });
</script>
@endsection