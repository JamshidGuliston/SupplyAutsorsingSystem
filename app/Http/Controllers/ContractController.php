<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Kindgarden;
use App\Models\Region;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with(['region', 'kindgardens'])
            ->orderByDesc('start_date')
            ->get();

        return view('accountant.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $regions = Region::where('hide', 0)->orWhereNull('hide')->orderBy('region_name')->get();
        $kindgardens = Kindgarden::where('hide', 0)->orWhereNull('hide')->orderBy('kingar_name')->get();

        return view('accountant.contracts.form', compact('regions', 'kindgardens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_number' => 'required|string|max:100',
            'contract_date'   => 'required|date',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'region_id'       => 'nullable|exists:regions,id',
            'kindgarden_ids'  => 'nullable|array',
            'kindgarden_ids.*'=> 'exists:kindgardens,id',
        ]);

        $contract = Contract::create([
            'contract_number' => $data['contract_number'],
            'contract_date'   => $data['contract_date'],
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'region_id'       => $data['region_id'] ?? null,
        ]);

        if (!empty($data['kindgarden_ids'])) {
            $contract->kindgardens()->sync($data['kindgarden_ids']);
        }

        return redirect()->route('contracts.index')->with('success', 'Shartnoma muvaffaqiyatli qo\'shildi.');
    }

    public function edit(Contract $contract)
    {
        $regions = Region::where('hide', 0)->orWhereNull('hide')->orderBy('region_name')->get();
        $kindgardens = Kindgarden::where('hide', 0)->orWhereNull('hide')->orderBy('kingar_name')->get();
        $selectedKindgardens = $contract->kindgardens->pluck('id')->toArray();

        return view('accountant.contracts.form', compact('contract', 'regions', 'kindgardens', 'selectedKindgardens'));
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'contract_number' => 'required|string|max:100',
            'contract_date'   => 'required|date',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'region_id'       => 'nullable|exists:regions,id',
            'kindgarden_ids'  => 'nullable|array',
            'kindgarden_ids.*'=> 'exists:kindgardens,id',
        ]);

        $contract->update([
            'contract_number' => $data['contract_number'],
            'contract_date'   => $data['contract_date'],
            'start_date'      => $data['start_date'],
            'end_date'        => $data['end_date'],
            'region_id'       => $data['region_id'] ?? null,
        ]);

        $contract->kindgardens()->sync($data['kindgarden_ids'] ?? []);

        return redirect()->route('contracts.index')->with('success', 'Shartnoma muvaffaqiyatli yangilandi.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Shartnoma o\'chirildi.');
    }
}
