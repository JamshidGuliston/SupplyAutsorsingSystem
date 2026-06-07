<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\ChildrenCount\ChildrenCountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildrenCountController extends Controller
{
    public function __construct(private ChildrenCountService $svc) {}

    public function today(Request $request): JsonResponse
    {
        return response()->json($this->svc->getTodayState($request->user()));
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'counts' => 'required|array|min:1',
            'counts.*' => 'required|integer|min:0',
        ]);
        $counts = [];
        foreach ($validated['counts'] as $ageId => $value) {
            $counts[(int) $ageId] = (int) $value;
        }
        return response()->json($this->svc->submit($request->user(), $counts));
    }
}
