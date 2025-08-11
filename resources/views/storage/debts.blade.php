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
<!-- deleteModal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('storage.deletedebt')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body foodcomposition"> 
                <select id='dshopsSelect' class="form-select" aria-label="Default select example" required disabled>
                    @foreach($shops as $row)
                        <option value='{{ $row->id }}'>{{ $row->shop_name }}</option>
                    @endforeach
                </select><br>
                <select id='dproductSelect' class="form-select" aria-label="Default select example" required disabled>
                    @foreach($products as $row)
                        <option value='{{ $row->id }}'>{{ $row->product_name }}</option>
                    @endforeach
                </select><br>
                <label>Miqdori</label>
                <input type="number" id="dpweight" class="form-control" disabled><br>
                <label>Narxi</label>
                <input type="number" id="dpcost" class="form-control" disabled><br>
                <label>To'landi</label>
                <input type="number" id="dpay_" class="form-control" disabled><br>
                <select id='ddaySelect' class="form-select" aria-label="Default select example" required disabled>
                    @foreach($days as $row)
                        <option value='{{ $row->id }}'>{{ $row->day_number.'.'.$row->month_name.'.'.$row->year_name }}</option>
                    @endforeach
                </select><br>
                <input type="hidden" id="dlarline" name="dlarid" class="form-control" ><br>
                <input type="hidden" id="ddebt_id" name="ddebt_id" class="form-control" ><br>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn editsub btn-success">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- Delete -->
<!-- EditModal -->
<div class="modal editesmodal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('storage.editedebts')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body foodcomposition"> 
                <select id='shopsSelect' name="editeshop_id" class="form-select" aria-label="Default select example" required>
                    @foreach($shops as $row)
                        <option value='{{ $row->id }}'>{{ $row->shop_name }}</option>
                    @endforeach
                </select><br>
                <select id='productSelect' name="productid" class="form-select" aria-label="Default select example" required>
                    @foreach($products as $row)
                        <option value='{{ $row->id }}'>{{ $row->product_name }}</option>
                    @endforeach
                </select><br>
                <label>Miqdori</label>
                <input type="number" id="pweight" name="weight" class="form-control" ><br>
                <label>Narxi</label>
                <input type="number" id="pcost" name="cost" class="form-control" ><br>
                <label>To'landi</label>
                <input type="number" id="pay_" name="pay_value" class="form-control" ><br>
                <select id='daySelect' name="editedayid" class="form-select" aria-label="Default select example" required>
                    @foreach($days as $row)
                        <option value='{{ $row->id }}'>{{ $row->day_number.'.'.$row->month_name.'.'.$row->year_name }}</option>
                    @endforeach
                </select><br>
                <input type="hidden" id="larline" name="larid" class="form-control" ><br>
                <input type="hidden" id="debt_id" name="debt_id" class="form-control" ><br>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn editsub btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Qarzdorliklar</h3>
        <a href="{{ route('storage.payment_history') }}" class="btn btn-info">To'lovlar tarixi</a>
    </div>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Firma</th>
                <th scope="col">Maxsulot</th>
                <th scope="col">O'lchov</th>
                <th scope="col">Miqdor</th>
                <th scope="col">Narxi</th>
                <th scope="col">Jami</th>
                <th scope="col">To'landi</th>
                <th scope="col">Farqi</th>
                <th scope="col">Sana</th>
                <th scope="col">...</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($debts as $row)
                <tr>
                    <td>{{ $row->debtid }}</td>
                    <td><a href="/storage/shopdebts?ShopId={{ $row->shop_id }}">{{ $row->shop_name }}</a></td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->size_name }}</td>
                    <td>{{ $row->weight }}</td>
                    <td>{{ $row->cost }}</td>
                    <td>{{ $row->loan }}</td>
                    <td>{{ $row->pay }}</td>
                    <td>{{ $row->pay-$row->loan }}</td>
                    <td>{{ $days->find($row->day_id)->day_number.'.'.$days->find($row->day_id)->month_name.'.'.$days->find($row->day_id)->year_name}}</td>
                    <td style="text-align: end;">
                        <!-- <i class="edite_  fa fa-edit" aria-hidden="true" 
                            data-debt-id="{{ $row->debtid }}" 
                            data-shop-id="{{ $row->shop_id }}" 
                            data-large-id="{{ $row->lid }}" 
                            data-product-id="{{ $row->productid }}" 
                            data-weight="{{ $row->weight }}" 
                            data-cost="{{ $row->cost }}" 
                            data-pay="{{ $row->pay }}" 
                            data-day-id="{{ $row->day_id }}" 
                            data-bs-toggle="modal" style="cursor: pointer; color:cadetblue" data-bs-target="#editModal"></i>
                        <i class="detete  fa fa-trash" aria-hidden="true" 
                            data-debt-id="{{ $row->debtid }}" 
                            data-shop-id="{{ $row->shop_id }}" 
                            data-large-id="{{ $row->lid }}" 
                            data-product-id="{{ $row->productid }}" 
                            data-weight="{{ $row->weight }}" 
                            data-cost="{{ $row->cost }}" 
                            data-pay="{{ $row->pay }}" 
                            data-day-id="{{ $row->day_id }}"
                            data-bs-toggle="modal" style="cursor: pointer; color: crimson" data-bs-target="#deleteModal"></i> -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $debts->links() }}
    <br>
    <a href="/storage/home/0/0">Orqaga</a>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.edite_').click(function() {
            var debtid = $(this).attr('data-debt-id');
            document.getElementById("debt_id").value = debtid;
            var shopid = $(this).attr('data-shop-id');
            var options = document.getElementById("shopsSelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == shopid) {
                    options[i].selected = true;
                    break;
                }
            }
            var productid = $(this).attr('data-product-id');
            var options = document.getElementById("productSelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == productid) {
                    options[i].selected = true;
                    break;
                }
            }
            var dayid = $(this).attr('data-day-id');
            var options = document.getElementById("daySelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == dayid) {
                    options[i].selected = true;
                    break;
                }
            }
            var weight = $(this).attr('data-weight');
            document.getElementById("pweight").value = weight;
            var cost = $(this).attr('data-cost');
            document.getElementById("pcost").value = cost;
            var larid = $(this).attr('data-large-id');
            document.getElementById("larline").value = larid;
            var pay = $(this).attr("data-pay");
            document.getElementById("pay_").value = pay;
            
        });
        $('.detete').click(function() {
            var debtid = $(this).attr('data-debt-id');
            document.getElementById("ddebt_id").value = debtid;
            var shopid = $(this).attr('data-shop-id');
            var options = document.getElementById("dshopsSelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == shopid) {
                    options[i].selected = true;
                    break;
                }
            }
            var productid = $(this).attr('data-product-id');
            var options = document.getElementById("dproductSelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == productid) {
                    options[i].selected = true;
                    break;
                }
            }
            var dayid = $(this).attr('data-day-id');
            var options = document.getElementById("ddaySelect").options;
            for (var i = 0; i < options.length; i++) {
                if (options[i].value == dayid) {
                    options[i].selected = true;
                    break;
                }
            }
            var weight = $(this).attr('data-weight');
            document.getElementById("dpweight").value = weight;
            var cost = $(this).attr('data-cost');
            document.getElementById("dpcost").value = cost;
            var larid = $(this).attr('data-large-id');
            document.getElementById("dlarline").value = larid;
            var pay = $(this).attr("data-pay");
            document.getElementById("dpay_").value = pay;
            
        });

        
    });
</script>
@endsection