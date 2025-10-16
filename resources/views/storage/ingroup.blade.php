@extends('layouts.app')
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
@if(session('status'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('status') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Add Modal -->
<div class="modal fade" id="addRowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('storage.ingroup.add') }}" method="POST">
        @csrf
        <div class="modal-header bg-success">
          <h5 class="modal-title text-white">Yangi mahsulot qo'shish</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="titleid" value="{{ $group->id }}">
          <div class="mb-3">
            <label class="form-label">Mahsulot</label>
            <select class="form-select" name="productid" required>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->product_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Do'kon</label>
            <select class="form-select" name="shop_id" required>
              @foreach($shops as $s)
                <option value="{{ $s->id }}">{{ $s->shop_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Miqdor</label>
              <input type="number" step="0.001" min="0" class="form-control" name="weight" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Narx (so'm/birlik)</label>
              <input type="number" step="0.01" min="0" class="form-control" name="cost" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">To'lov</label>
              <input type="number" step="0.01" min="0" class="form-control" name="pay" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jami (so'm)</label>
              <input type="text" class="form-control" id="total_amount" readonly style="background-color: #f8f9fa;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
          <button type="submit" class="btn btn-success">Saqlash</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editRowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('storage.ingroup.edit') }}" method="POST">
        @csrf
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Mahsulotni tahrirlash</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="row_id" id="edit_row_id">
          <input type="hidden" name="group_id" value="{{ $group->id }}">
          <div class="mb-3">
            <label class="form-label">Mahsulot</label>
            <select class="form-select" name="productid" id="edit_productid" required>
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->product_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Do'kon</label>
            <select class="form-select" name="shop_id" id="edit_shop_id" required>
              @foreach($shops as $s)
                <option value="{{ $s->id }}">{{ $s->shop_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Miqdor</label>
              <input type="number" step="0.001" min="0" class="form-control" name="weight" id="edit_weight" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Narx (so'm/birlik)</label>
              <input type="number" step="0.01" min="0" class="form-control" name="cost" id="edit_cost" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">To'lov</label>
              <input type="number" step="0.01" min="0" class="form-control" name="pay" id="edit_pay" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jami (so'm)</label>
              <input type="text" class="form-control" id="edit_total_amount" readonly style="background-color: #f8f9fa;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
          <button type="submit" class="btn btn-warning">Yangilash</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteRowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('storage.ingroup.delete') }}" method="POST">
        @csrf
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white">O'chirishni tasdiqlang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="row_id" id="delete_row_id">
          <input type="hidden" name="group_id" value="{{ $group->id }}">
          <p>Ushbu yozuvni o'chirishni xohlaysizmi?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
          <button type="submit" class="btn btn-danger">O'chirish</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="py-4 px-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5>{{ $group->group_name }} — {{ sprintf('%02d',$group->day_number).'.'.$group->month_name.'.'.$group->year_name }}</h5>
    <div>
      <button class="btn btn-danger me-2" id="bulkDeleteBtn" style="display:none;">
        <i class="fas fa-trash"></i> Tanlanganlarni o'chirish (<span id="selectedCount">0</span>)
      </button>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRowModal"><i class="fas fa-plus"></i> Qo'shish</button>
    </div>
  </div>

  <table class="table table-light table-striped table-hover">
    <thead>
      <tr>
        <th style="width: 40px;">
          <input type="checkbox" id="selectAll" class="form-check-input">
        </th>
        <th>ID</th>
        <th>Maxsulot</th>
        <th>Birlik</th>
        <th>Miqdor</th>
        <th>Narx (so'm)</th>
        <th>Jami (so'm)</th>
        <th>Do'kon</th>
        <th style="width:110px; text-align:end;">Amallar</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 0; ?>
      @foreach($productall as $item)
      <tr>
        <td>
          <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}">
        </td>
        <td>{{ ++$i }}</td>
        <td>{{ $item->product_name }}</td>
        <td>{{ $item->size_name }}</td>
        <td>{{ $item->weight }}</td>
        <td>{{ number_format($item->cost, 0, ',', ' ') }} so'm</td>
        <td>{{ number_format($item->cost * $item->weight, 0, ',', ' ') }} so'm</td>
        <td>
          @php $shop = $shops->firstWhere('id', $item->shop_id); @endphp
          {{ $shop ? $shop->shop_name : '-' }}
        </td>
        <td style="text-align:end;">
          <button class="btn btn-sm btn-warning edit-btn" 
                  data-bs-toggle="modal" data-bs-target="#editRowModal"
                  data-id="{{ $item->id }}"
                  data-product="{{ $item->product_id }}"
                  data-shop="{{ $item->shop_id }}"
                  data-weight="{{ $item->weight }}"
                  data-cost="{{ $item->cost }}"
                  data-pay="">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteRowModal" data-id="{{ $item->id }}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
      @endforeach
      <tr style="border-top: 2px solid #000;">
        <td colspan="5" style="text-align: right;"><strong>Umumiy summa:</strong></td>
        <td style="text-align: right;"><strong>{{ number_format($productall->sum(function($item) { return $item->cost * $item->weight; }), 0, ',', ' ') }} so'm</strong></td>
        <td colspan="2"></td>
      </tr>
    </tbody>
  </table>
  <a href="/storage/addedproducts/{{ $group->month_id }}/{{ $group->id }}">Orqaga</a>
</div>
@endsection

@section('script')
<script>
  // Narxni formatlash funksiyasi
  function formatPrice(price) {
    return new Intl.NumberFormat('uz-UZ').format(price) + ' so\'m';
  }

  // Jami summani hisoblash funksiyasi
  function calculateTotal(weight, cost) {
    const total = weight * cost;
    return formatPrice(total);
  }

  document.addEventListener('DOMContentLoaded', function(){
    // Add modal uchun
    const weightInput = document.querySelector('input[name="weight"]');
    const costInput = document.querySelector('input[name="cost"]');
    const totalAmountInput = document.getElementById('total_amount');

    function updateTotal() {
      const weight = parseFloat(weightInput.value) || 0;
      const cost = parseFloat(costInput.value) || 0;
      totalAmountInput.value = calculateTotal(weight, cost);
    }

    weightInput.addEventListener('input', updateTotal);
    costInput.addEventListener('input', updateTotal);

    // Edit modal uchun
    const editWeightInput = document.getElementById('edit_weight');
    const editCostInput = document.getElementById('edit_cost');
    const editTotalAmountInput = document.getElementById('edit_total_amount');

    function updateEditTotal() {
      const weight = parseFloat(editWeightInput.value) || 0;
      const cost = parseFloat(editCostInput.value) || 0;
      editTotalAmountInput.value = calculateTotal(weight, cost);
    }

    editWeightInput.addEventListener('input', updateEditTotal);
    editCostInput.addEventListener('input', updateEditTotal);

    document.querySelectorAll('.edit-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.getElementById('edit_row_id').value = this.dataset.id;
        document.getElementById('edit_productid').value = this.dataset.product;
        document.getElementById('edit_shop_id').value = this.dataset.shop;
        document.getElementById('edit_weight').value = this.dataset.weight;
        document.getElementById('edit_cost').value = this.dataset.cost;
        
        // Jami summani hisoblash
        const weight = parseFloat(this.dataset.weight) || 0;
        const cost = parseFloat(this.dataset.cost) || 0;
        document.getElementById('edit_total_amount').value = calculateTotal(weight, cost);
      });
    });

    document.querySelectorAll('.delete-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.getElementById('delete_row_id').value = this.dataset.id;
      });
    });

    // Bulk delete funksiyalari
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Barcha checkboxlarni belgilash/bekor qilish
    selectAllCheckbox.addEventListener('change', function() {
      itemCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateBulkDeleteButton();
    });

    // Har bir checkbox o'zgarganda
    itemCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        updateSelectAllCheckbox();
        updateBulkDeleteButton();
      });
    });

    // "Barchasini belgilash" checkboxni yangilash
    function updateSelectAllCheckbox() {
      const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
      const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
      selectAllCheckbox.checked = allChecked;
      selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }

    // Bulk delete tugmasini ko'rsatish/yashirish
    function updateBulkDeleteButton() {
      const selectedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
      const count = selectedItems.length;
      
      if (count > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        selectedCountSpan.textContent = count;
      } else {
        bulkDeleteBtn.style.display = 'none';
      }
    }

    // Bulk delete tugmasini bosish
    bulkDeleteBtn.addEventListener('click', function() {
      const selectedItems = Array.from(itemCheckboxes).filter(cb => cb.checked);
      const selectedIds = selectedItems.map(cb => cb.value);
      
      if (selectedIds.length === 0) {
        alert('Hech qanday mahsulot tanlanmagan!');
        return;
      }

      if (confirm(`Tanlangan ${selectedIds.length} ta mahsulotni o'chirishni xohlaysizmi?`)) {
        // Form yaratish va yuborish
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("storage.ingroup.bulk.delete") }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Group ID
        const groupIdInput = document.createElement('input');
        groupIdInput.type = 'hidden';
        groupIdInput.name = 'group_id';
        groupIdInput.value = '{{ $group->id }}';
        form.appendChild(groupIdInput);
        
        // Tanlangan ID'lar
        selectedIds.forEach(id => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'ids[]';
          input.value = id;
          form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
      }
    });
  });
</script>
@endsection