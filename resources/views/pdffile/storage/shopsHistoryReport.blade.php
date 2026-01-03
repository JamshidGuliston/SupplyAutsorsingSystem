<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetkazuvchilar hisoboti</title>
    <style>
        @page {
            margin: 0.5in 0.3in;
            size: A4 landscape;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f0f0;
        }

        .header h2 {
            margin: 0;
            padding: 5px 0;
            font-size: 16px;
        }

        .header .date-info {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }

        .region-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .region-title {
            background-color: #4a5568;
            color: white;
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }

        table thead {
            background-color: #e2e8f0;
        }

        table thead th {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        table tbody td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
        }

        table tbody td:first-child {
            text-align: left;
            font-weight: 500;
        }

        table tbody td:nth-child(2) {
            text-align: center;
        }

        table tbody td:last-child {
            font-weight: bold;
            background-color: #f7fafc;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>YETKAZUVCHILAR HISOBOTI</h2>
        <div class="date-info">
            @if($dateType === 'daily' && $days->count() === 1)
                {{ $days->first()->day_number }}.{{ $days->first()->month->month_name }}.{{ $days->first()->year->year_name }}
            @elseif($dateType === 'monthly')
                {{ $days->first()->month->month_name }} {{ $days->first()->year->year_name }}
            @elseif($dateType === 'range' && $days->count() > 0)
                {{ $days->first()->day_number }}.{{ $days->first()->month->month_name }} - {{ $days->last()->day_number }}.{{ $days->last()->month->month_name }}.{{ $days->last()->year->year_name }}
            @endif
        </div>
    </div>

    @if(count($reportData) > 0)
        @foreach($reportData as $regionName => $regionData)
            <div class="region-section">
                <div class="region-title">{{ $regionName }} tumani</div>

                @php
                    $kindgardensOrdered = collect($regionData['kindgardens'])->sortBy('number_org');
                    $kindgardenCount = $kindgardensOrdered->count();
                    $maxColumnsPerTable = 12; // Maksimal ustunlar soni bitta jadvalda
                    $kindgardenChunks = $kindgardensOrdered->chunk($maxColumnsPerTable);
                    $totalChunks = $kindgardenChunks->count();
                @endphp

                @foreach($kindgardenChunks as $chunkIndex => $kindgardenChunk)
                    @php
                        $isLastChunk = ($chunkIndex == $totalChunks - 1);
                        $chunkCount = $kindgardenChunk->count();
                        $columnWidth = $chunkCount > 0 ? (60 / $chunkCount) : 10;
                    @endphp

                    @if($chunkIndex > 0)
                        <div style="margin-top: 15px; font-weight: bold; font-size: 10px; color: #4a5568;">
                            Davomi ({{ $chunkIndex + 1 }}-qism):
                        </div>
                    @endif

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 25%;">Maxsulot nomi</th>
                                <th style="width: 10%;">O'lchov birligi</th>

                                @foreach($kindgardenChunk as $kindgardenId => $kindgarden)
                                    <th style="width: {{ $columnWidth }}%;">{{ $kindgarden['number_org'] }}</th>
                                @endforeach

                                @if($isLastChunk)
                                    <th style="width: 10%;">Jami</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regionData['products'] as $productId => $product)
                                <tr>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['size'] }}</td>

                                    @php
                                        $rowTotal = 0;
                                    @endphp

                                    @foreach($kindgardenChunk as $kindgardenId => $kindgarden)
                                        @php
                                            $weight = $product['kindgardens'][$kindgardenId] ?? 0;
                                            $rowTotal += $weight;
                                        @endphp
                                        <td>{{ $weight > 0 ? number_format($weight, 2, '.', '') : '-' }}</td>
                                    @endforeach

                                    @if($isLastChunk)
                                        @php
                                            // Barcha bog'chalar bo'yicha umumiy jami
                                            $grandTotal = 0;
                                            foreach($kindgardensOrdered as $kId => $kg) {
                                                $grandTotal += $product['kindgardens'][$kId] ?? 0;
                                            }
                                        @endphp
                                        <td>{{ number_format($grandTotal, 2, '.', '') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="no-data">
            Ma'lumot topilmadi
        </div>
    @endif

    <div class="footer">
        Hisobot yaratildi: {{ date('d.m.Y H:i') }}
    </div>
</body>
</html>
