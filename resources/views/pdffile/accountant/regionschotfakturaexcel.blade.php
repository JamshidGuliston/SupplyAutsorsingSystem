<table>
    <!-- Header qismi -->
    <tr>
        <td colspan="9" style="text-align: center; font-size: 18px; font-weight: bold; padding: 10px;">
            СЧЁТ-ФАКТУРА
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-size: 14px; padding: 5px;">
            № {{ $invoice_number ?? "_________________________________" }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-size: 14px; padding: 5px;">
            {{ $invoice_date."  й" ?? "_________________________________" }}
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-size: 14px; padding: 5px;">
            {{ "Хизмат кўрсатиш шартномаси:"." № ".$contract_data }}
        </td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9" style="height: 20px;"></td></tr>
    
    <!-- Kompaniya ma'lumotlari -->
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 14px; padding: 8px;">Аутсорсер:</td>
        <td colspan="5" style="font-weight: bold; font-size: 14px; padding: 8px;">Буюртмачи:</td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>Ташкилот:</strong> {{ $autorser['company_name'] ?? 'IOS-Service MCHJ' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>Ташкилот:</strong> {{ $buyurtmachi['company_name'] ?? '_________________________________' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>Манзил:</strong> {{ $autorser['address'] ?? 'Toshkent shahri, Olmazor tumani, 1' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>Манзил:</strong> {{ $buyurtmachi['address'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>ИНН:</strong> {{ $autorser['inn'] ?? '123456789' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>ИНН:</strong> {{ $buyurtmachi['inn'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>МФО:</strong> {{ $autorser['mfo'] ?? '12345' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>МФО:</strong> {{ $buyurtmachi['mfo'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>Хисоб рақам:</strong> {{ $autorser['bank_account'] ?? '1234567890123456' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>Х/р:</strong> {{ $buyurtmachi['bank_account'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>Банк:</strong> {{ $autorser['bank'] ?? 'Асака банк' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>Ягона ғ.х/р:</strong> {{ $buyurtmachi['account_number'] ?? '' }}
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 5px;">
            <strong>Телефон:</strong> {{ $autorser['phone'] ?? '+998901234567' }}
        </td>
        <td colspan="5" style="padding: 5px;">
            <strong>Банк:</strong> {{ $buyurtmachi['bank'] ?? "" }}
        </td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9" style="height: 20px;"></td></tr>
    
    <!-- Jadval header -->
    <tr style="background-color: #f0f0f0;">
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">№</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Иш, хизмат номи</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Ўл.бир</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Сони</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Нархи</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Етказиб бериш нархи</td>
        <td colspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">ҚҚС ва устама</td>
        <td rowspan="2" style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Кўрсатилган хизмат суммаси (ҚҚС билан)</td>
    </tr>
    <tr style="background-color: #f0f0f0;">
        <td style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">%</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid black; padding: 8px;">Сумма</td>
    </tr>
    
    <!-- Ma'lumotlar qatori -->
    @php
        $total_service = 0;
        $total_nds_raise = 0;
        $total_cost = 0;
        $tr = 1;
    @endphp
    
    @foreach($ages as $age)
    <tr>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">{{ $tr++ }}</td>
        <td style="text-align: left; border: 1px solid black; padding: 5px;">{{ $region->region_name . " MMTB " . $age->description . "ли гуруҳ тарбияланувчилари учун кўрсатилган ".$days[0]->year_name." йил ".$days[0]->day_number."-".$days[count($days)-1]->day_number." ".$days[0]->month_name." даги Аутсорсинг хизмати" }}</td>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">хизмат</td>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">1</td>
        <!-- without nds -->
        <td style="text-align: right; border: 1px solid black; padding: 5px;">{{ number_format($number_childrens[$age->id] * $costs[$age->id]->eater_cost / (1 + $costs[$age->id]->nds/100) ?? 0, 2) }}</td>
        <td style="text-align: right; border: 1px solid black; padding: 5px;">{{ number_format($number_childrens[$age->id] * $costs[$age->id]->eater_cost/ (1 + $costs[$age->id]->nds/100) ?? 0, 2) }}</td>
        @php $total_cost += $number_childrens[$age->id] * $costs[$age->id]->eater_cost / (1 + $costs[$age->id]->nds/100) ?? 0; @endphp
        <td style="text-align: center; border: 1px solid black; padding: 5px;">{{ $costs[$age->id]->nds ?? 0 }}%</td>
        <!-- only nds -->
        <td style="text-align: right; border: 1px solid black; padding: 5px;">
            @php
                $amount = $number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                $total_nds_raise += $amount * ($costs[$age->id]->nds/(100+$costs[$age->id]->nds) ?? 0);
            @endphp
            {{ number_format($amount * ($costs[$age->id]->nds/(100+$costs[$age->id]->nds) ?? 0), 2) }}
        </td>
        <td style="text-align: right; border: 1px solid black; padding: 5px;">
            @php
                $vat = $number_childrens[$age->id] * ($costs[$age->id]->eater_cost ?? 0);
                $total_service += $vat;
            @endphp
            {{ number_format($vat, 2) }}
        </td>
    </tr>
    @endforeach
    
    <!-- Ustama qatori -->
    <tr>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">{{ $tr++ }}</td>
        <td style="text-align: left; border: 1px solid black; padding: 5px;">Аутсорсинг хизмати устамаси {{ $ustama_settings->raise . "%" ?? 0 }}</td>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">Хизмат</td>
        <td style="text-align: center; border: 1px solid black; padding: 5px;">1</td>
        <td style="border: 1px solid black; padding: 5px;"></td>
        <td style="text-align: right; border: 1px solid black; padding: 5px;">{{ number_format($total_cost * ($ustama_settings->raise/100 ?? 0), 2) }}</td>
        @php $raise = $total_cost * ($ustama_settings->raise/100 ?? 0); @endphp
        <td style="text-align: center; border: 1px solid black; padding: 5px;">{{ $ustama_settings->nds . "%" ?? 0 }}</td>
        <td style="text-align: right; border: 1px solid black; padding: 5px;">{{ number_format($total_cost * ($ustama_settings->raise/100 ?? 0) * ($ustama_settings->nds/100 ?? 0), 2) }}</td>
        @php $nds = $total_cost * ($ustama_settings->raise/100 ?? 0) * ($ustama_settings->nds/100 ?? 0); @endphp
        <td style="text-align: right; border: 1px solid black; padding: 5px;">{{ number_format($raise + $nds, 2) }}</td>
        @php $total_service += $raise + $nds; @endphp
        @php $total_cost += $total_cost * ($ustama_settings->raise/100 ?? 0); @endphp
        @php $total_nds_raise += $nds; @endphp
    </tr>
    
    <!-- Jami qator -->
    <tr style="background-color: #f8f9fa; font-weight: bold;">
        <td style="border: 1px solid black; padding: 5px;"></td>
        <td colspan="4" style="text-align: right; border: 1px solid black; padding: 5px; font-weight: bold;">Жами сумма:</td>
        <td style="text-align: right; border: 1px solid black; padding: 5px; font-weight: bold;">{{ number_format($total_cost, 2) }}</td>
        <td style="border: 1px solid black; padding: 5px;"></td>
        <td style="text-align: right; border: 1px solid black; padding: 5px; font-weight: bold;">{{ number_format($total_nds_raise, 2) }}</td>
        <td style="text-align: right; border: 1px solid black; padding: 5px; font-weight: bold;">{{ number_format($total_service, 2) }}</td>
    </tr>
    
    <!-- Bo'sh qator -->
    <tr><td colspan="9" style="height: 30px;"></td></tr>
    
    <!-- Footer qismi -->
    <tr>
        <td colspan="4" style="padding: 10px;">
            Аутсорсер директори: ____________________________
        </td>
        <td colspan="5" style="padding: 10px;">
            Бўлими директори: ____________________________
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding: 10px;">
            Бош хисобчиси: ____________________________
        </td>
        <td colspan="5" style="padding: 10px;">
            Бош хисобчиси: ____________________________
        </td>
    </tr>
</table> 