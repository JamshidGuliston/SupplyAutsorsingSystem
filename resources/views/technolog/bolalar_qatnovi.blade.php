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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    }
    
    .attendance-table th {
        background: #f8f9fa;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 14px;
        min-width: 80px;
    }
    
    .attendance-table td {
        padding: 10px 8px;
        text-align: center;
        border: 1px solid #dee2e6;
        font-size: 14px;
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
        min-width: 200px;
    }
    
    .children-count {
        font-weight: 500;
        color: #28a745;
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
        
        // Ustun bo'yicha jami hisoblash
        var columnTotals = {};
        days.forEach(function(day) {
            columnTotals[day.id] = 0;
            Object.keys(data).forEach(function(regionId) {
                var region = data[regionId];
                if (region.total_row && region.total_row.days[day.id]) {
                    columnTotals[day.id] += region.total_row.days[day.id];
                }
            });
        });
        
        // Har bir tuman uchun jadval yaratish
        Object.keys(data).forEach(function(regionId) {
            var region = data[regionId];
            html += `
                <div class="attendance-table mb-4">
                    <div class="region-header">
                        <i class="fas fa-map-marker-alt me-2"></i>${region.region_name}
                    </div>
                    <div class="table-container">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">MTT-nomi</th>
                                    <th style="min-width: 100px;">Tashkilot №</th>
            `;
            
            // Kunlar ustunlarini qo'shish
            days.forEach(function(day) {
                html += `<th>${day.day_number}.${day.month_name}</th>`;
            });
            
            // Jami ustunini qo'shish
            html += `<th style="min-width: 100px; background-color: #f8f9fa; color: #000;">JAMI</th>`;
            
            html += `
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            // Har bir bog'cha uchun qatorlar
            region.kindgardens.forEach(function(kindgarden) {
                // Har bir yosh guruhi uchun alohida qator
                kindgarden.age_groups.forEach(function(ageGroup) {
                    html += `
                        <tr>
                            <td class="kindgarden-name">${kindgarden.name} - ${ageGroup.age_name}</td>
                            <td>${kindgarden.number_of_org || '-'}</td>
                    `;
                    
                    // Har bir kun uchun bolalar soni
                    days.forEach(function(day) {
                        var dayData = ageGroup.days[day.id];
                        var childrenCount = dayData ? dayData.children_count : 0;
                        html += `<td class="children-count">${childrenCount}</td>`;
                    });
                    
                    // Yosh guruhi bo'yicha jami
                    var ageGroupTotal = ageGroup.total || 0;
                    html += `<td class="children-count" style="background-color: #f8f9fa; font-weight: bold; color: #000;">${ageGroupTotal}</td>`;
                    
                    html += `</tr>`;
                });
                
                // Bog'cha bo'yicha jami qatorini qo'shish
                html += `
                    <tr style="background-color: #e3f2fd; font-weight: bold;">
                        <td class="kindgarden-name">${kindgarden.name} - JAMI</td>
                        <td style="background-color: #e3f2fd;"></td>
                `;
                
                // Har bir kun uchun bog'cha bo'yicha jami bolalar soni
                days.forEach(function(day) {
                    var kindgardenDayTotal = 0;
                    kindgarden.age_groups.forEach(function(ageGroup) {
                        var dayData = ageGroup.days[day.id];
                        if (dayData) {
                            kindgardenDayTotal += dayData.children_count;
                        }
                    });
                    html += `<td class="children-count" style="background-color: #e3f2fd; color: #000; font-weight: bold;">${kindgardenDayTotal}</td>`;
                });
                
                // Bog'cha bo'yicha jami
                var kindgardenTotal = kindgarden.total || 0;
                html += `<td class="children-count" style="background-color: #e3f2fd; color: #000; font-weight: bold;">${kindgardenTotal}</td>`;
                
                html += `</tr>`;
            });
            
            // Jami qatorini qo'shish
            if (region.total_row) {
                html += `
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td class="kindgarden-name">${region.total_row.name}</td>
                        <td></td>
                `;
                
                // Har bir kun uchun jami bolalar soni
                days.forEach(function(day) {
                    var totalCount = region.total_row.days[day.id] || 0;
                    html += `<td class="children-count" style="color: #000;">${totalCount}</td>`;
                });
                
                // Tuman bo'yicha jami
                var regionTotal = 0;
                days.forEach(function(day) {
                    regionTotal += region.total_row.days[day.id] || 0;
                });
                html += `<td class="children-count" style="background-color: #6c757d; color: white; font-weight: bold;">${regionTotal}</td>`;
                
                html += `</tr>`;
            }
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        });
        
        // Ustun bo'yicha jami qatorini qo'shish
        if (Object.keys(data).length > 0) {
            html += `
                <div class="attendance-table mb-4">
                    <div class="region-header" style="background-color: #6c757d;">
                        <i class="fas fa-calculator me-2"></i>UMUMIY JAMI
                    </div>
                    <div class="table-container">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">Jami</th>
                                    <th style="min-width: 100px;"></th>
            `;
            
            // Kunlar ustunlarini qo'shish
            days.forEach(function(day) {
                html += `<th>${day.day_number}.${day.month_name}</th>`;
            });
            
            // Jami ustunini qo'shish
            html += `<th style="min-width: 100px; background-color: #6c757d; color: white;">JAMI</th>`;
            
            html += `
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #fff3e0; font-weight: bold;">
                                    <td class="kindgarden-name">UMUMIY JAMI</td>
                                    <td></td>
            `;
            
            // Har bir kun uchun jami bolalar soni
            days.forEach(function(day) {
                var totalCount = columnTotals[day.id] || 0;
                html += `<td class="children-count" style="color: #000; font-size: 16px;">${totalCount}</td>`;
            });
            
            // Umumiy jami
            var grandTotal = 0;
            days.forEach(function(day) {
                grandTotal += columnTotals[day.id] || 0;
            });
            html += `<td class="children-count" style="background-color: #343a40; color: white; font-size: 16px; font-weight: bold;">${grandTotal}</td>`;
            
            html += `
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }
        
        $('#report_container').html(html);
    }
});
</script>
@endsection 