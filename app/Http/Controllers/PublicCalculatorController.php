<?php

namespace App\Http\Controllers;

use App\Services\QuickPriceCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class PublicCalculatorController extends Controller
{
    public function __construct(
        protected QuickPriceCalculatorService $quickPriceCalculatorService,
    ) {
    }

    public function show(): View
    {
        return view('public.calculator', [
            'calculatorDefaults' => [
                'product_name' => '',
                'recipe_total_cost' => '',
                'yield_quantity' => '',
                'packaging_unit_cost' => '',
                'other_costs' => '',
                'profit_margin_percentage' => 100,
                'sales_channel_name' => '',
                'channel_percentage_rate' => '',
                'simulate_url' => route('calculator.simulate'),
            ],
        ]);
    }

    public function simulate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_name' => ['nullable', 'string', 'max:255'],
            'recipe_total_cost' => ['required', 'numeric', 'min:0'],
            'yield_quantity' => ['required', 'numeric', 'gt:0'],
            'packaging_unit_cost' => ['nullable', 'numeric', 'min:0'],
            'other_costs' => ['nullable', 'numeric', 'min:0'],
            'profit_margin_percentage' => ['required', 'numeric', 'min:0'],
            'sales_channel_name' => ['nullable', 'string', 'max:255'],
            'channel_percentage_rate' => ['nullable', 'numeric', 'min:0', 'lt:100'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        try {
            return response()->json([
                'result' => $this->quickPriceCalculatorService->calculatePublic($validator->validated()),
            ]);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
