<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolalar qatnovi hisoboti</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .region-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .region-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
        }
        
        .kindgarden-name {
            text-align: left;
            font-weight: bold;
            min-width: 150px;
        }
        
        .org-number {
            font-weight: bold;
            color: #666;
        }
        
        .children-count {
            font-weight: bold;
            color: #000;
        }
        
        .day-header {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 9px;
            min-width: 40px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BOLALAR QATNOVI HISOBOTI</h1>
        <p>Vaqt oralig'i: {{ $selectedDays->first()->day_number }}.{{ $selectedDays->first()->month_name }}.{{ $selectedDays->first()->year_name }} - {{ $selectedDays->last()->day_number }}.{{ $selectedDays->last()->month_name }}.{{ $selectedDays->last()->year_name }}</p>
        <p>Hisobot sanasi: {{ date('d.m.Y H:i') }}</p>
    </div>

    @foreach($attendanceData as $regionId => $region)
        <div class="region-section">
            <div class="region-title">
                {{ $region['region_name'] }}
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 200px;">MTT-nomi</th>
                        <th style="width: 80px;">Tashkilot №</th>
                        @foreach($selectedDays as $day)
                            <th class="day-header">{{ $day->day_number }}.{{ $day->month_name }}</th>
                        @endforeach
                        <th class="day-header" style="background-color: #f8f9fa; color: #000;">JAMI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($region['kindgardens'] as $kindgarden)
                        <tr>
                            <td class="kindgarden-name">{{ $kindgarden['name'] }}</td>
                            <td class="org-number">{{ $kindgarden['number_of_org'] ?? '-' }}</td>
                            @foreach($selectedDays as $day)
                                @php
                                    $dayData = $kindgarden['days'][$day->id] ?? null;
                                    $childrenCount = $dayData ? $dayData['children_count'] : 0;
                                @endphp
                                <td class="children-count">{{ $childrenCount }}</td>
                            @endforeach
                            <td class="children-count" style="background-color: #f8f9fa; font-weight: bold; color: #000;">{{ $kindgarden['total'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    
                    @if(isset($region['total_row']))
                        <tr style="background-color: #f8f9fa;">
                            <td class="kindgarden-name" style="font-weight: bold;">{{ $region['total_row']['name'] }}</td>
                            <td class="org-number"></td>
                            @foreach($selectedDays as $day)
                                @php
                                    $totalCount = $region['total_row']['days'][$day->id] ?? 0;
                                @endphp
                                <td class="children-count" style="color: #000; font-weight: bold;">{{ $totalCount }}</td>
                            @endforeach
                            @php
                                $regionTotal = 0;
                                foreach($selectedDays as $day) {
                                    $regionTotal += $region['total_row']['days'][$day->id] ?? 0;
                                }
                            @endphp
                            <td class="children-count" style="background-color: #6c757d; color: white; font-weight: bold;">{{ $regionTotal }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach
    
    <!-- Ustun bo'yicha jami -->
    @php
        $columnTotals = [];
        foreach($selectedDays as $day) {
            $columnTotals[$day->id] = 0;
            foreach($attendanceData as $regionData) {
                if (isset($regionData['total_row']['days'][$day->id])) {
                    $columnTotals[$day->id] += $regionData['total_row']['days'][$day->id];
                }
            }
        }
    @endphp
    
            <div class="region-section">
            <div class="region-title" style="background-color: #6c757d; color: white;">UMUMIY JAMI</div>
        <table>
                            <thead>
                    <tr>
                        <th style="width: 200px;">Jami</th>
                        <th style="width: 80px;"></th>
                        @foreach($selectedDays as $day)
                            <th class="day-header">{{ $day->day_number }}.{{ $day->month_name }}</th>
                        @endforeach
                        <th class="day-header" style="background-color: #6c757d; color: white;">JAMI</th>
                    </tr>
                </thead>
            <tbody>
                <tr style="background-color: #fff3e0; font-weight: bold;">
                    <td class="kindgarden-name">UMUMIY JAMI</td>
                    <td class="org-number"></td>
                    @foreach($selectedDays as $day)
                        <td class="children-count" style="color: #000; font-size: 16px;">{{ $columnTotals[$day->id] ?? 0 }}</td>
                    @endforeach
                    @php
                        $grandTotal = 0;
                        foreach($selectedDays as $day) {
                            $grandTotal += $columnTotals[$day->id] ?? 0;
                        }
                    @endphp
                    <td class="children-count" style="background-color: #343a40; color: white; font-size: 16px; font-weight: bold;">{{ $grandTotal }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Bu hisobot tizim orqali avtomatik yaratildi</p>
        <p>Jami tumanlar: {{ count($attendanceData) }} | Jami kunlar: {{ count($selectedDays) }}</p>
    </div>
</body>
</html> 