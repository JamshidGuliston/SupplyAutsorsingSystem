<table>
    <!-- Header qismi -->
    <tr>
        <td colspan="9">СЧЁТ-ФАКТУРА</td>
    </tr>
    <tr>
        <td colspan="9">№ {{ $invoice_number ?? "_________________________________" }}</td>
    </tr>
    <tr>
        <td colspan="9">{{ $invoice_date."  й" ?? "_________________________________" }}</td>
    </tr>
    <tr>
        <td colspan="9">{{ "Хизмат кўрсатиш шартномаси:"." № ".$contract_data }}</td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9"></td></tr>
    
    <!-- Kompaniya ma'lumotlari -->
    <tr>
        <td colspan="4"><strong>Аутсорсер:</strong></td>
        <td colspan="5"><strong>Буюртмачи:</strong></td>
    </tr>
    <tr>
        <td colspan="4">Ташкилот: {{ $autorser['company_name'] ?? 'IOS-Service MCHJ' }}</td>
        <td colspan="5">Ташкилот: {{ $buyurtmachi['company_name'] ?? '_________________________________' }}</td>
    </tr>
    <tr>
        <td colspan="4">Манзил: {{ $autorser['address'] ?? 'Toshkent shahri, Olmazor tumani, 1' }}</td>
        <td colspan="5">Манзил: {{ $buyurtmachi['address'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4">ИНН: {{ $autorser['inn'] ?? '123456789' }}</td>
        <td colspan="5">ИНН: {{ $buyurtmachi['inn'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4">МФО: {{ $autorser['mfo'] ?? '12345' }}</td>
        <td colspan="5">МФО: {{ $buyurtmachi['mfo'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4">Хисоб рақам: {{ $autorser['bank_account'] ?? '1234567890123456' }}</td>
        <td colspan="5">Х/р: {{ $buyurtmachi['bank_account'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4">Банк: {{ $autorser['bank'] ?? 'Асака банк' }}</td>
        <td colspan="5">Ягона ғ.х/р: {{ $buyurtmachi['account_number'] ?? '' }}</td>
    </tr>
    <tr>
        <td colspan="4">Телефон: {{ $autorser['phone'] ?? '+998901234567' }}</td>
        <td colspan="5">Банк: {{ $buyurtmachi['bank'] ?? "" }}</td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9"></td></tr>
    
    <!-- Jadval header -->
    <tr>
        <td rowspan="2">№</td>
        <td rowspan="2">Иш, хизмат номи</td>
        <td rowspan="2">Ўл.бир</td>
        <td rowspan="2">Сони</td>
        <td rowspan="2">Нархи</td>
        <td rowspan="2">Етказиб бериш нархи</td>
        <td colspan="2">ҚҚС ва устама</td>
        <td rowspan="2">Кўрсатилган хизмат суммаси (ҚҚС билан)</td>
    </tr>
    <tr>
        <td>%</td>
        <td>Сумма</td>
    </tr>
    
    <!-- Ma'lumotlar qatori -->
    @php
        $total_service = 0;
        $total_nds_raise = 0;
        $total_cost = 0;
        $tr = 1;
    @endphp
    
    @foreach($ages as $age)
    @if(isset($costs[$age->id]) && $costs[$age->id])
    <tr>
        <td>{{ $tr++ }}</td>
        <td>{{ $region->region_name . " MMTB " . $age->description . "ли гуруҳ тарбияланувчилари учун кўрсатилган ".$days[0]->year_name." йил ".$days[0]->day_number."-".$days[count($days)-1]->day_number." ".$days[0]->month_name." даги Аутсорсинг хизмати" }}</td>
        <td>хизмат</td>
        <td>1</td>
        <td>{{ number_format($number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0) / (1 + ($costs[$age->id]->nds ?? 0)/100), 2) }}</td>
        <td>{{ number_format($number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0) / (1 + ($costs[$age->id]->nds ?? 0)/100), 2) }}</td>
        @php $total_cost += $number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0) / (1 + ($costs[$age->id]->nds ?? 0)/100); @endphp
        <td>{{ $costs[$age->id]->nds ?? 0 }}%</td>
        <td>
            @php
                $amount = $number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                $nds_val = ($costs[$age->id]->nds ?? 0);
                $nds_calc = $nds_val > 0 ? $amount * ($nds_val/(100+$nds_val)) : 0;
                $total_nds_raise += $nds_calc;
            @endphp
            {{ number_format($nds_calc, 2) }}
        </td>
        <td>
            @php
                $vat = $number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                $total_service += $vat;
            @endphp
            {{ number_format($vat, 2) }}
        </td>
    </tr>
    @endif
    @endforeach
    
    <!-- Ustama qatori -->
    <tr>
        <td>{{ $tr++ }}</td>
        <td>Аутсорсинг хизмати устамаси {{ $ustama_settings->raise . "%" ?? 0 }}</td>
        <td>Хизмат</td>
        <td>1</td>
        <td></td>
        <td>{{ number_format($total_cost * ($ustama_settings->raise/100 ?? 0), 2) }}</td>
        @php $raise = $total_cost * ($ustama_settings->raise/100 ?? 0); @endphp
        <td>{{ $ustama_settings->nds . "%" ?? 0 }}</td>
        <td>{{ number_format($total_cost * ($ustama_settings->raise/100 ?? 0) * ($ustama_settings->nds/100 ?? 0), 2) }}</td>
        @php $nds = $total_cost * ($ustama_settings->raise/100 ?? 0) * ($ustama_settings->nds/100 ?? 0); @endphp
        <td>{{ number_format($raise + $nds, 2) }}</td>
        @php $total_service += $raise + $nds; @endphp
        @php $total_cost += $total_cost * ($ustama_settings->raise/100 ?? 0); @endphp
        @php $total_nds_raise += $nds; @endphp
    </tr>
    
    <!-- Jami qator -->
    <tr>
        <td></td>
        <td colspan="4"><strong>Жами сумма:</strong></td>
        <td><strong>{{ number_format($total_cost, 2) }}</strong></td>
        <td></td>
        <td><strong>{{ number_format($total_nds_raise, 2) }}</strong></td>
        <td><strong>{{ number_format($total_service, 2) }}</strong></td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9"></td></tr>
    
    <!-- Footer qismi -->
    <tr>
        <td colspan="4">Аутсорсер директори: ____________________________</td>
        <td colspan="5">Бўлими директори: ____________________________</td>
    </tr>
    <tr>
        <td colspan="4">Бош хисобчиси: ____________________________</td>
        <td colspan="5">Бош хисобчиси: ____________________________</td>
    </tr>
</table> 