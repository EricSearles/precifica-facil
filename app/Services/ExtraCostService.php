<?php

namespace App\Services;

use App\Models\ExtraCost;
use App\Repositories\ExtraCostRepository;

class ExtraCostService
{
    public function __construct(
        protected ExtraCostRepository $extraCostRepository,
        protected RecipeService $recipeService,
    ) {
    }

    public function create(array $data, int $companyId): ExtraCost
    {
        $normalizedData = $this->normalizeData($data);

        $extraCost = $this->extraCostRepository->create([
            'company_id' => $companyId,
            'product_id' => null,
            'recipe_id' => $normalizedData['recipe_id'],
            'description' => $normalizedData['description'],
            'type' => $normalizedData['type'],
            'value' => $normalizedData['value'],
            'labor_minutes' => $normalizedData['labor_minutes'],
            'labor_hourly_rate' => $normalizedData['labor_hourly_rate'],
            'monthly_salary' => $normalizedData['monthly_salary'],
            'monthly_hours' => $normalizedData['monthly_hours'],
        ]);

        $this->recipeService->recalculateAndUpdate((int) $extraCost->recipe_id, $companyId);

        return $extraCost;
    }

    public function update(ExtraCost $extraCost, array $data, int $companyId): ExtraCost
    {
        $normalizedData = $this->normalizeData($data);

        $extraCost->description = $normalizedData['description'];
        $extraCost->type = $normalizedData['type'];
        $extraCost->value = $normalizedData['value'];
        $extraCost->labor_minutes = $normalizedData['labor_minutes'];
        $extraCost->labor_hourly_rate = $normalizedData['labor_hourly_rate'];
        $extraCost->monthly_salary = $normalizedData['monthly_salary'];
        $extraCost->monthly_hours = $normalizedData['monthly_hours'];

        $this->extraCostRepository->save($extraCost);
        $this->recipeService->recalculateAndUpdate((int) $extraCost->recipe_id, $companyId);

        return $extraCost;
    }

    public function delete(ExtraCost $extraCost, int $companyId): void
    {
        $recipeId = (int) $extraCost->recipe_id;

        $this->extraCostRepository->delete($extraCost);
        $this->recipeService->recalculateAndUpdate($recipeId, $companyId);
    }

    protected function normalizeData(array $data): array
    {
        $laborMinutes = array_key_exists('labor_minutes', $data) && $data['labor_minutes'] !== null && $data['labor_minutes'] !== ''
            ? (int) $data['labor_minutes']
            : null;

        $laborHourlyRate = array_key_exists('labor_hourly_rate', $data) && $data['labor_hourly_rate'] !== null && $data['labor_hourly_rate'] !== ''
            ? round((float) $data['labor_hourly_rate'], 4)
            : null;

        $monthlySalary = array_key_exists('monthly_salary', $data) && $data['monthly_salary'] !== null && $data['monthly_salary'] !== ''
            ? round((float) $data['monthly_salary'], 4)
            : null;

        $monthlyHours = array_key_exists('monthly_hours', $data) && $data['monthly_hours'] !== null && $data['monthly_hours'] !== ''
            ? (int) $data['monthly_hours']
            : null;

        if ($monthlySalary !== null && $monthlyHours === null) {
            $monthlyHours = 220;
        }

        if ($laborHourlyRate === null && $monthlySalary !== null && $monthlyHours !== null && $monthlyHours > 0) {
            $laborHourlyRate = round($monthlySalary / $monthlyHours, 4);
        }

        $hasLaborCalculation = $laborMinutes !== null && $laborHourlyRate !== null;

        return [
            'recipe_id' => $data['recipe_id'] ?? null,
            'description' => $data['description'],
            'type' => $hasLaborCalculation ? 'fixed' : $data['type'],
            'value' => $hasLaborCalculation
                ? round(($laborMinutes / 60) * $laborHourlyRate, 4)
                : (float) $data['value'],
            'labor_minutes' => $laborMinutes,
            'labor_hourly_rate' => $laborHourlyRate,
            'monthly_salary' => $monthlySalary,
            'monthly_hours' => $monthlyHours,
        ];
    }
}
