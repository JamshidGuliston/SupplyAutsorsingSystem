@extends('layouts.app')

@section('css')
<link href="/css/dates.css?ver=1.0" rel="stylesheet"/>
<style>
    .date-selector {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .attendance-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .region-header {
        background: linear-gradient(135deg,rgb(206, 206, 206) 0%,rgb(148, 148, 148) 100%);
        color: white;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 18px;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .attendance-table table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    
    .attendance-table th {
        background: #f8f9fa;
        padding: 8px 4px;
        text-align: center;
        border: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 11px;
        min-width: 60px;
        color: #000 !important;
    }
    
    .attendance-table td {
        padding: 6px 4px;
        text-align: center;
        border: 1px solid #dee2e6;
        font-size: 11px;
    }
    
    .attendance-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .attendance-table tr:hover {
        background-color: #e3f2fd;
    }
    
    .kindgarden-name {
        font-weight: 600;
        color: #495057;
        text-align: left;
        min-width: 150px;
    }
    
    .children-count {
        font-weight: 500;
        color:rgb(30, 31, 30);
    }
    
    .loading {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }
    
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }
    
    /* Yangi jadval dizayni */
    .main-header {
        background: #343a40 !important;
        color: white !important;
        font-weight: bold;
    }
    
    .sub-header {
        background:rgb(247, 250, 252) !important;
        color: white !important;
        font-weight: 600;
    }
    
    .date-header {
        background: white !important;
        color: black !important;
        font-weight: 600;
        border: 1px solid #dee2e6 !important;
    }
    
    .total-row {
        background: #e9ecef;
        font-weight: bold;
    }
    
    .grand-total-row {
        background: #343a40;
        color: white;
        font-weight: bold;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="date-selector">
        <h4 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Bolalar qatnovi hisoboti</h4>
        
        <div class="row">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Boshlanish sanasi</label>
                <select id="start_date" class="form-select">
                    <option value="">Sanani tanlang</option>
                    @foreach($days as $day)
                        <option value="{{ $day->id }}">{{ $day->day_number }}.{{ $day->month_name }}.{{ $day->year_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Oxirgi sana</label>
                <select id="end_date" class="form-select">
                    <option value="">Sanani tanlang</option>
                    @foreach($days as $day)
                        <option value="{{ $day->id }}">{{ $day->day_number }}.{{ $day->month_name }}.{{ $day->year_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="region_filter" class="form-label">Tuman (ixtiyoriy)</label>
                <select id="region_filter" class="form-select">
                    <option value="">Barcha tumanlar</option>
                    @foreach(\App\Models\Region::all() as $region)
                        <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" id="generate_report" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Hisobot yaratish
                </button>
            </div>
        </div>
    </div>
    
    <div id="report_container">
        <div class="no-data">
            <i class="fas fa-chart-bar fa-3x mb-3 text-muted"></i>
            <p>Hisobot yaratish uchun sanalarni tanlang va "Hisobot yaratish" tugmasini bosing</p>
        </div>
    </div>
    
    <!-- Download buttons (hidden by default) -->
    <div id="download_buttons" class="mt-3" style="display: none;">
        <div class="d-flex gap-2">
            <button type="button" id="download_pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i>PDF yuklab olish
            </button>
            <button type="button" id="download_excel" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Excel yuklab olish
            </button>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var currentStartDate = '';
    var currentEndDate = '';
    var currentRegionId = '';
    
    $('#generate_report').click(function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var regionId = $('#region_filter').val();
        
        if (!startDate || !endDate) {
            alert('Iltimos, boshlanish va oxirgi sanalarni tanlang!');
            return;
        }
        
        if (parseInt(startDate) > parseInt(endDate)) {
            alert('Boshlanish sanasi oxirgi sanadan katta bo\'lishi mumkin emas!');
            return;
        }
        
        currentStartDate = startDate;
        currentEndDate = endDate;
        currentRegionId = regionId;
        
        generateReport(startDate, endDate, regionId);
    });
    
    // PDF yuklab olish
    $('#download_pdf').click(function() {
        if (!currentStartDate || !currentEndDate) {
            alert('Avval hisobot yarating!');
            return;
        }
        
        var form = $('<form>', {
            'method': 'POST',
            'action': '/technolog/download-bolalar-qatnovi-pdf'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'start_date',
            'value': currentStartDate
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'end_date',
            'value': currentEndDate
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'region_id',
            'value': currentRegionId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    });
    
    // Excel yuklab olish
    $('#download_excel').click(function() {
        if (!currentStartDate || !currentEndDate) {
            alert('Avval hisobot yarating!');
            return;
        }
        
        // Excel yuklab olish uchun yangi oynada ochish
        var url = '/technolog/download-bolalar-qatnovi-excel';
        var form = $('<form>', {
            'method': 'POST',
            'action': url,
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'start_date',
            'value': currentStartDate
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'end_date',
            'value': currentEndDate
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'region_id',
            'value': currentRegionId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    });
    
    function generateReport(startDate, endDate, regionId) {
        $('#report_container').html(`
            <div class="loading">
                <div class="spinner"></div>
                <p>Hisobot yaratilmoqda...</p>
            </div>
        `);
        $('#download_buttons').hide();
        
        $.ajax({
            url: '/technolog/get-bolalar-qatnovi-data',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate,
                region_id: regionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    displayReport(response.data, response.days, response.age_ranges);
                    $('#download_buttons').show();
                } else {
                    $('#report_container').html(`
                        <div class="no-data">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                            <p>Ma'lumotlarni yuklashda xatolik yuz berdi</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#report_container').html(`
                    <div class="no-data">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3 text-danger"></i>
                        <p>Server bilan bog'lanishda xatolik yuz berdi</p>
                    </div>
                `);
            }
        });
    }
    
    function displayReport(data, days, ageRanges) {
        if (Object.keys(data).length === 0) {
            $('#report_container').html(`
                <div class="no-data">
                    <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
                    <p>Tanlangan vaqt oralig'ida ma'lumot topilmadi</p>
                </div>
            `);
            return;
        }
        
        var html = '';
        
        // Har bir tuman uchun jadval yaratish
        Object.keys(data).forEach(function(regionId) {
            var region = data[regionId];
            
            // Bog'chalarni number_of_org bo'yicha saralash
            region.kindgardens.sort(function(a, b) {
                var aNumber = parseInt(a.number_of_org) || 999999;
                var bNumber = parseInt(b.number_of_org) || 999999;
                
                if (aNumber === 999999 && bNumber === 999999) {
                    return (a.kingar_name || '').localeCompare(b.kingar_name || '');
                }
                
                return aNumber - bNumber;
            });
            
            html += `
                <div class="attendance-table mb-4">
                    <div class="region-header">
                        <i class="fas fa-map-marker-alt me-2"></i>${region.region_name}
                    </div>
                    <div class="table-container">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <!-- Asosiy sarlavha qatori -->
                                <tr class="main-header">
                                    <th rowspan="2" style="min-width: 80px;">TR</th>
                                    <th rowspan="2" style="min-width: 120px;">DMTT</th>
            `;
            
            // Har bir bog'cha uchun ustunlar (number_of_org bo'yicha)
            region.kindgardens.forEach(function(kindgarden) {
                // Bog'cha raqamini ko'rsatish
                var orgNumber = kindgarden.number_of_org || kindgarden.kingar_name || '-';
                html += `<th colspan="3">${orgNumber}</th>`;
            });
            
            // Jami ustuni
            html += `<th colspan="3">Jami</th>`;
            
            html += `
                                </tr>
                                <!-- Ikkinchi sarlavha qatori -->
                                <tr class="sub-header">
            `;
            
            // Har bir bog'cha uchun kichik ustunlar
            region.kindgardens.forEach(function(kindgarden) {
                html += `
                    <th class="date-header">3-7 yosh</th>
                    <th class="date-header">Qisqa guruh</th>
                    <th class="date-header">Xodim</th>
                `;
            });
            
            // Jami uchun kichik ustunlar
            html += `
                    <th class="date-header">3-7 yosh</th>
                    <th class="date-header">Qisqa guruh</th>
                    <th class="date-header">Xodim</th>
            `;
            
            html += `
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            // Har bir sana uchun qatorlar
            days.forEach(function(day, dayIndex) {
                html += `
                    <tr>
                        <td>${dayIndex + 1}</td>
                        <td class="kindgarden-name">${day.day_number}.${day.month_name}.${day.year_name}</td>
                `;
                
                // Har bir bog'cha uchun ma'lumotlar
                region.kindgardens.forEach(function(kindgarden) {
                    // Xavfsiz ma'lumot olish
                    var dayData = null;
                    if (kindgarden.days && kindgarden.days[day.id]) {
                        dayData = kindgarden.days[day.id];
                    }
                    
                    var childrenCount = dayData ? (dayData.children_count || 0) : 0;
                    var shortGroupCount = dayData ? (dayData.short_group_count || 0) : 0;
                    var workersCount = dayData ? (dayData.workers_count || 0) : 0;
                    
                    html += `
                        <td class="children-count">${childrenCount}</td>
                        <td class="children-count">${shortGroupCount}</td>
                        <td class="children-count">${workersCount}</td>
                    `;
                });
                
                // Kun bo'yicha jami - barcha bog'chalardan yig'ish
                var dayTotal = 0;
                var dayShortTotal = 0;
                var dayWorkersTotal = 0;
                
                region.kindgardens.forEach(function(kindgarden) {
                    var dayData = null;
                    if (kindgarden.days && kindgarden.days[day.id]) {
                        dayData = kindgarden.days[day.id];
                    }
                    
                    if (dayData) {
                        dayTotal += parseInt(dayData.children_count) || 0;
                        dayShortTotal += parseInt(dayData.short_group_count) || 0;
                        dayWorkersTotal += parseInt(dayData.workers_count) || 0;
                    }
                });
                
                html += `
                    <td class="children-count" style="background-color: #f8f9fa; font-weight: bold;">${dayTotal}</td>
                    <td class="children-count" style="background-color: #f8f9fa; font-weight: bold;">${dayShortTotal}</td>
                    <td class="children-count" style="background-color: #f8f9fa; font-weight: bold;">${dayWorkersTotal}</td>
                `;
                
                html += `</tr>`;
            });
            
            // Jami qatorini qo'shish
            html += `
                <tr class="total-row">
                    <td></td>
                    <td class="kindgarden-name">Jami</td>
            `;
            
            // Har bir bog'cha bo'yicha jami
            region.kindgardens.forEach(function(kindgarden) {
                var kindgardenTotal = parseInt(kindgarden.total) || 0;
                var kindgardenShortTotal = parseInt(kindgarden.short_total) || 0;
                var kindgardenWorkersTotal = parseInt(kindgarden.workers_total) || 0;
                
                html += `
                    <td class="children-count" style="background-color: #e3f2fd; font-weight: bold;">${kindgardenTotal}</td>
                    <td class="children-count" style="background-color: #e3f2fd; font-weight: bold;">${kindgardenShortTotal}</td>
                    <td class="children-count" style="background-color: #e3f2fd; font-weight: bold;">${kindgardenWorkersTotal}</td>
                `;
            });
            
            // Umumiy jami - barcha bog'chalardan yig'ish
            var regionTotal = 0;
            var regionShortTotal = 0;
            var regionWorkersTotal = 0;
            
            region.kindgardens.forEach(function(kindgarden) {
                regionTotal += parseInt(kindgarden.total) || 0;
                regionShortTotal += parseInt(kindgarden.short_total) || 0;
                regionWorkersTotal += parseInt(kindgarden.workers_total) || 0;
            });
            
            html += `
                <td class="children-count" style="background-color: #6c757d; color: white; font-weight: bold;">${regionTotal}</td>
                <td class="children-count" style="background-color: #6c757d; color: white; font-weight: bold;">${regionShortTotal}</td>
                <td class="children-count" style="background-color: #6c757d; color: white; font-weight: bold;">${regionWorkersTotal}</td>
            `;
            
            html += `</tr>`;
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        });
        
        $('#report_container').html(html);
    }
});
</script>
@endsection 