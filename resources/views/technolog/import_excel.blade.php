@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    .import-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .import-section h5 {
        margin-bottom: 20px;
        color: #495057;
    }
    .template-table {
        font-size: 13px;
    }
    .template-table th {
        background-color: #0dcaf0;
        color: white;
        text-align: center;
        padding: 8px;
    }
    .template-table td {
        text-align: center;
        padding: 6px;
    }
    .import-result {
        max-height: 400px;
        overflow-y: auto;
    }
    .filter-notification {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
        animation: slideInRight 0.3s ease-out;
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu')
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <h4 class="mb-4"><i class="fas fa-file-excel me-2"></i>Excel orqali ma'lumot yuklash</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="import-section">
                <h5><i class="fas fa-upload me-2"></i>Excel faylni yuklash</h5>
                <form id="import-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="kindergarten_id" class="form-label fw-bold">Muassasani tanlang:</label>
                        <select class="form-select" id="kindergarten_id" name="kindergarten_id" required>
                            <option value="">-- Muassasani tanlang --</option>
                            @foreach($kindergartens as $kg)
                                <option value="{{ $kg->id }}">{{ $kg->kingar_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="excel_file" class="form-label fw-bold">Excel fayl:</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Faqat .xlsx, .xls, .csv fayl formatlari qabul qilinadi</small>
                    </div>
                    <button type="submit" class="btn btn-success" id="import-btn">
                        <i class="fas fa-upload me-1"></i>Yuklash
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="import-section">
                <h5><i class="fas fa-info-circle me-2"></i>Excel fayl namunasi</h5>
                <p class="text-muted">Excel fayl quyidagi ko'rinishda bo'lishi kerak:</p>
                <div class="table-responsive">
                    <table class="table table-bordered template-table">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Hafta kuni</th>
                                <th>Bolalar soni</th>
                                <th>Yosh toifasi</th>
                                <th>Menu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>03.02.2026</td>
                                <td>Seshanba</td>
                                <td>120</td>
                                <td>4</td>
                                <td>1</td>
                            </tr>
                            <tr>
                                <td>04.02.2026</td>
                                <td>Chorshanba</td>
                                <td>85</td>
                                <td>3</td>
                                <td>2</td>
                            </tr>
                            <tr>
                                <td>05.02.2026</td>
                                <td>Payshanba</td>
                                <td>95</td>
                                <td>4</td>
                                <td>3</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Qoidalar:</strong>
                    <ul class="mb-0 mt-1">
                        <li><strong>Sana</strong> ustuni: KK.OO.YYYY formatida (masalan: 03.02.2026)</li>
                        <li><strong>Yosh toifasi</strong> ustuni: ID (raqam) yoki age_name (matn) bo'lishi mumkin</li>
                        <li><strong>Menu</strong> ustuni: ID (raqam) yoki menu_name/short_name (matn) bo'lishi mumkin</li>
                        <li>Sana bazada mavjud bo'lmasa, avtomatik yaratiladi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Natija qismi -->
    <div id="import-result" style="display: none;">
        <div class="import-section">
            <h5><i class="fas fa-list-alt me-2"></i>Import natijasi</h5>
            <div id="result-content" class="import-result"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        function showNotification(message, type) {
            $('.filter-notification').remove();
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show filter-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                '<i class="' + icon + '" style="margin-right: 8px;"></i>' + message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            $('body').append(notification);
            setTimeout(function() { notification.fadeOut(); }, 5000);
        }

        $('#import-form').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var btn = $('#import-btn');
            var originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Yuklanmoqda...');

            $.ajax({
                url: '/technolog/import-excel',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');

                        // Natijalarni ko'rsatish
                        var html = '<div class="alert alert-success"><strong>Umumiy:</strong> ' + response.summary.total + ' ta qator | ' +
                            '<strong>Muvaffaqiyatli:</strong> ' + response.summary.success + ' | ' +
                            '<strong>Xatolik:</strong> ' + response.summary.errors + '</div>';

                        if (response.details && response.details.length > 0) {
                            html += '<table class="table table-sm table-bordered"><thead><tr>' +
                                '<th>#</th><th>Sana</th><th>Bolalar soni</th><th>Yosh toifasi</th><th>Menu</th><th>Holat</th></tr></thead><tbody>';
                            response.details.forEach(function(item, index) {
                                var rowClass = item.status === 'success' ? 'table-success' : 'table-danger';
                                html += '<tr class="' + rowClass + '">' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + (item.date || '') + '</td>' +
                                    '<td>' + (item.children_count || '') + '</td>' +
                                    '<td>' + (item.age_range || '') + '</td>' +
                                    '<td>' + (item.menu || '') + '</td>' +
                                    '<td>' + item.message + '</td></tr>';
                            });
                            html += '</tbody></table>';
                        }

                        $('#result-content').html(html);
                        $('#import-result').show();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Xatolik yuz berdi!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification(errorMessage, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endsection
