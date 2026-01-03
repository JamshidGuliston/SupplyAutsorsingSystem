<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash-alt me-2"></i>O'chirish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ddebt_id" name="ddebt_id" class="form-control">
                    <p>Rostdan ham o'chirmoqchimisiz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-danger">O'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('storage.editegroup')}}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="fas fa-edit me-2"></i>O'zgartirish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="title" name="nametitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="daySelect" class="form-label">Sana</label>
                        <select id='daySelect' name="editedayid" class="form-select" required>
                            @foreach($days as $row)
                                <option value='{{ $row->id }}'>{{ $row->day_number.'.'.$row->month_name.'.'.$row->year_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="group_id" name="group_id">
                    <input type="hidden" id="gyear_id" name="year_id">
                    <input type="hidden" id="gmonth_id" name="month_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Residual Modal -->
<div class="modal fade" id="addresidual" tabindex="-1" aria-labelledby="addresidualLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addresidualLabel">
                    <i class="fas fa-box me-2"></i>Qoldiq qo'shish
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-form-residual" action="" method="get">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mahsulot</label>
                            <select id="input-notebar" class="form-select" required>
                                @foreach($products as $row)
                                    <option value="{{$row['id']}}">{{$row['product_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Miqdori (kg/dona)</label>
                            <input id="input-expensebar" class="form-control" type="text" onkeypress="javascript:return isNumber(event)" placeholder="kg yoki ta">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Narxi</label>
                            <input id="input-incomebar" class="form-control" type="number" placeholder="Narx">
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button" id="additem" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Qo'shish
                            </button>
                        </div>
                    </div>
                </form>
                <hr>
                <form method="POST" action="{{route('storage.addr_products')}}">
                    @csrf
                    <input type="hidden" name="month_id" value="{{ $id }}">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mahsulot</th>
                                    <th>Miqdor</th>
                                    <th>Narxi</th>
                                    <th class="text-center">O'chirish</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <input type="text" name="title" class="form-control" placeholder="Izoh" required>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="date_id" required>
                                <option value="">--Sana--</option>
                                @foreach($start as $row)
                                    <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="residual" name="residual" value="True">
                                <label class="form-check-label" for="residual">Qoldiq</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-save me-2"></i>Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Mahsulot qo'shish
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-form-product" action="" method="get">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Mahsulot</label>
                            <select id="input-note-bar" class="form-select" required>
                                @foreach($products as $row)
                                    <option value="{{$row['id']}}">{{$row['product_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Do'kon</label>
                            <select id="get_shop_select" class="form-select" required>
                                @foreach($shops as $row)
                                    <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Miqdor</label>
                            <input id="input-expense-bar" class="form-control" type="text" onkeypress="javascript:return isNumber(event)" placeholder="kg/ta">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Narxi</label>
                            <input id="input-income-bar" class="form-control" type="number" placeholder="Narx">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To'lov</label>
                            <input id="input-summa-bar" class="form-control" type="number" value="0" disabled>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="button" id="add-item" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Qo'shish
                            </button>
                        </div>
                    </div>
                </form>
                <hr>
                <form method="POST" action="{{route('storage.addproducts')}}">
                    @csrf
                    <input type="hidden" name="month_id" value="{{ $id }}">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Mahsulot</th>
                                    <th>Miqdor</th>
                                    <th>Narxi</th>
                                    <th>Do'kon</th>
                                    <th>To'landi</th>
                                    <th class="text-center">O'chirish</th>
                                </tr>
                            </thead>
                            <tbody id="tablebody"></tbody>
                        </table>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <input type="text" name="title" class="form-control" placeholder="Izoh" required>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" name="date_id" required>
                                <option value="">--Sana--</option>
                                @foreach($start as $row)
                                    <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success px-5">
                            <i class="fas fa-save me-2"></i>Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
