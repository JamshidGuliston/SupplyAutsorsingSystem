<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolalar qatnovi hisoboti</title>
    <style>
        @page { 
            margin: 0.5in; 
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        
        .region-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .region-title {
            background-color: #333;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 7px;
        }
        
        .main-header {
            background-color: #333 !important;
            color: white !important;
            font-weight: bold;
        }
        
        .sub-header {
            background-color: #666 !important;
            color: white !important;
            font-weight: 600;
        }
        
        .date-header {
            background-color: #999 !important;
            color: white !important;
            font-weight: 600;
        }
        
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        .grand-total-row {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .kindgarden-name {
            text-align: left;
            font-weight: 600;
        }
        
        .children-count {
            font-weight: 500;
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
                    <!-- Asosiy sarlavha qatori -->
                    <tr class="main-header">
                        <th rowspan="2" style="width: 30px;">TR</th>
                        <th rowspan="2" style="width: 80px;">DMTT</th>
                        @foreach($region['kindgardens'] as $kindgarden)
                            <th colspan="3">{{ $kindgarden['number_of_org'] ?: $kindgarden['kingar_name'] }}</th>
                        @endforeach
                        <th colspan="3">Jami</th>
                    </tr>
                    <!-- Ikkinchi sarlavha qatori -->
                    <tr class="sub-header">
                        @foreach($region['kindgardens'] as $kindgarden)
                            <th class="date-header">3-7 yosh</th>
                            <th class="date-header">Qisqa guruh</th>
                            <th class="date-header">Xodim</th>
                        @endforeach
                        <th class="date-header">3-7 yosh</th>
                        <th class="date-header">Qisqa guruh</th>
                        <th class="date-header">Xodim</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selectedDays as $dayIndex => $day)
                        <tr>
                            <td>{{ $dayIndex + 1 }}</td>
                            <td class="kindgarden-name">{{ $day->day_number }}.{{ $day->month_name }}.{{ $day->year_name }}</td>
                            
                            @php
                                $dayTotal = 0;
                                $dayShortTotal = 0;
                                $dayWorkersTotal = 0;
                            @endphp
                            
                            @foreach($region['kindgardens'] as $kindgarden)
                                @php
                                    $dayData = $kindgarden['days'][$day->id] ?? null;
                                    $childrenCount = $dayData ? $dayData['children_count'] : 0;
                                    $shortGroupCount = $dayData ? $dayData['short_group_count'] : 0;
                                    $workersCount = $dayData ? $dayData['workers_count'] : 0;
                                    
                                    $dayTotal += $childrenCount;
                                    $dayShortTotal += $shortGroupCount;
                                    $dayWorkersTotal += $workersCount;
                                @endphp
                                
                                <td class="children-count">{{ $childrenCount }}</td>
                                <td class="children-count">{{ $shortGroupCount }}</td>
                                <td class="children-count">{{ $workersCount }}</td>
                            @endforeach
                            
                            <td class="children-count" style="background-color: #f0f0f0; font-weight: bold;">{{ $dayTotal }}</td>
                            <td class="children-count" style="background-color: #f0f0f0; font-weight: bold;">{{ $dayShortTotal }}</td>
                            <td class="children-count" style="background-color: #f0f0f0; font-weight: bold;">{{ $dayWorkersTotal }}</td>
                        </tr>
                    @endforeach
                    
                    <!-- Jami qatori -->
                    <tr class="total-row">
                        <td></td>
                        <td class="kindgarden-name">Jami</td>
                        
                        @php
                            $regionTotal = 0;
                            $regionShortTotal = 0;
                            $regionWorkersTotal = 0;
                        @endphp
                        
                        @foreach($region['kindgardens'] as $kindgarden)
                            <td class="children-count" style="background-color: #e0e0e0; font-weight: bold;">{{ $kindgarden['total'] }}</td>
                            <td class="children-count" style="background-color: #e0e0e0; font-weight: bold;">{{ $kindgarden['short_total'] }}</td>
                            <td class="children-count" style="background-color: #e0e0e0; font-weight: bold;">{{ $kindgarden['workers_total'] }}</td>
                            
                            @php
                                $regionTotal += $kindgarden['total'];
                                $regionShortTotal += $kindgarden['short_total'];
                                $regionWorkersTotal += $kindgarden['workers_total'];
                            @endphp
                        @endforeach
                        
                        <td class="children-count" style="background-color: #333; color: white; font-weight: bold;">{{ $regionTotal }}</td>
                        <td class="children-count" style="background-color: #333; color: white; font-weight: bold;">{{ $regionShortTotal }}</td>
                        <td class="children-count" style="background-color: #333; color: white; font-weight: bold;">{{ $regionWorkersTotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        @if(!$loop->last)
            <div style="page-break-before: always;"></div>
        @endif
    @endforeach
</body>
</html> 