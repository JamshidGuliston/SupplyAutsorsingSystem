<!-- resources/views/pdf/menu.blade.php -->
<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<title>Menu PDF</title>
<style>
@page { margin: 10mm 10mm 10mm 10mm; }
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 7px;
    margin: 0;
    padding: 0;
}

/* Jadval asosiy dizayni */
table {
    border-collapse: collapse;
    width: 100%;
    border: 1px solid #000;
    table-layout: fixed;
}
th, td {
    border: 1px solid #000;
    text-align: center;
    vertical-align: middle;
    padding: 1px;
    word-wrap: break-word;
}
tr:nth-child(even) { background: #e2fdff; }
tr:nth-child(odd) { background: #fff; }

/* === ROTATE asosida vertikal sarlavha uchun yechim === */
.vrt-header {
    height: 100px;
    width: 20px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    position: relative;
}
.vrt-header span {
    display: block;
    position: absolute;
    bottom: 50%;
    left: 50%;
    transform: translate(-50%, 50%) rotate(-90deg);
    transform-origin: center;
    width: 120px;
    font-size: 5px;
    line-height: 1;
    text-align: center;
}

/* Maxsulot nomlari */
.product-name-short {
    font-size: 5px;
    line-height: 1;
    white-space: normal;
    width: 60px;
}

/* Mealtime nomlari (бирinchi ustun) */
.mealtime-header {
    font-size: 6px;
    line-height: 1.1;
    white-space: nowrap;
}

/* Footer satrlar */
.footer-row td {
    font-weight: bold;
    border-top: 2px solid black;
}

/* Pastki qismdagi imzolar */
.signature-row {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    font-size: 7px;
}
.summary {
    margin-top: 10px;
    text-align: center;
    font-size: 8px;
}
</style>
</head>
<body>

<div style="text-align:center;">
    <h5><b>ТАСДИҚЛАЙМАН</b></h5>
    <p><b>{{ $menu[0]['kingar_name'] }}</b></p>
    <p>Боғча номи: <b>{{ $menu[0]['kingar_name'] }}</b></p>
    <p>Таомнома: <b>{{ $menu[0]['menu_name'] }}</b></p>
    <p>Сана: <b>{{ $day['day_number'] }}.{{ $day['month_name'] }} {{ $day['year_name'] }}й</b></p>
    <p>{{ $menu[0]['age_name'] }}ли болалар сони: <b>{{ $menu[0]['kingar_children_number'] }}</b>;
    @if(isset($workerfood[0]) && $workerfood[0]['worker_age_id'] == $menu[0]['king_age_name_id'])
        ходимлар сони: <b>{{ $menu[0]['workers_count'] }}</b>
    @endif
    </p>
</div>

<table>
    <thead>
        <tr>
            <th style="width:2%">№</th>
            <th style="width:9%">Махсулотлар номи</th>
            <th style="width:3%">Таом вазни</th>
            @foreach($products as $p)
                @if(isset($p['yes']))
                    <th class="vrt-header"><span>{{ $p['product_name'] }}</span></th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach($menuitem as $rows)
            @foreach($rows as $item)
                @if($loop->index == 0) @continue; @endif
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $item['foodname'] }}</td>
                    <td>{{ $item['foodweight'] }}</td>
                    @foreach($products as $p)
                        @if(isset($p['yes']))
                            <td>{{ $item[$p['id']] ?? '' }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        @endforeach

        <!-- Бир бола учун -->
        <tr class="footer-row">
            <td colspan="3">{{ $menu[0]['age_name'] }}ли бир бола учун (гр)</td>
            @foreach($products as $p)
                @if(isset($p['yes']))
                    <td>{{ $productallcount[$p['id']] ?? '' }}</td>
                @endif
            @endforeach
        </tr>

        <!-- Жами болалар -->
        <tr class="footer-row">
            <td colspan="3">Жами миқдори (кг/хис)</td>
            @foreach($products as $p)
                @if(isset($p['yes']))
                    <td>
                        @php
                            if(isset($productallcount[$p['id']])) 
                                printf("%01.3f", ($menu[0]['kingar_children_number'] * $productallcount[$p['id']]) / $p['div']);
                        @endphp
                    </td>
                @endif
            @endforeach
        </tr>

        @if(env('WORKERSFORMENU') == "true")
        <tr class="footer-row">
            <td colspan="3">1 та ходим учун (гр)</td>
            @foreach($products as $p)
                @if(isset($p['yes']))
                    <td>{{ $workerproducts[$p['id']] ?? '' }}</td>
                @endif
            @endforeach
        </tr>
        <tr class="footer-row">
            <td colspan="3">Жами сарфланган миқдор (кг)</td>
            @foreach($products as $p)
                @if(isset($p['yes']))
                    <td>
                        @php
                            $total = 0;
                            if(isset($productallcount[$p['id']])) {
                                $total += ($menu[0]['kingar_children_number'] * $productallcount[$p['id']]) / $p['div'];
                            }
                            if(isset($workerproducts[$p['id']])) {
                                $total += ($menu[0]['workers_count'] * $workerproducts[$p['id']]) / $p['div'];
                            }
                            printf("%01.3f", $total);
                        @endphp
                    </td>
                @endif
            @endforeach
        </tr>
        @endif
    </tbody>
</table>

<div class="summary">
    <p><b>Бир нафар {{ $menu[0]['age_name'] }}ли бола учун:</b> {{ number_format($protsent->eater_cost, 0, ',', ' ') }} сўм</p>
    <p><b>Жами сарфланган сумма:</b> {{ number_format($menu[0]['kingar_children_number'] * $protsent->eater_cost, 0, ',', ' ') }}</p>
</div>

<div class="signature-row">
    <span><b>Технолог:</b> __________________;</span>
    <span><b>Бухгалтер:</b> __________________;</span>
    <span><b>{{ env('MENU_SIGNATURE') }}:</b> __________________;</span>
    <span><b>ДМТТ рахбари:</b> __________________;</span>
</div>

</body>
</html>
