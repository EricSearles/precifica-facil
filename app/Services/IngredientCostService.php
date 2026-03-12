<?php

namespace App\Services;

use App\Models\Ingredient;
use InvalidArgumentException;

class IngredientCostService
{
    public function __construct(
        protected UnitConversionService $unitConversionService,
    ) {
    }

    public function calculateUnitCost(Ingredient $ingredient): float
    {
        $referenceQuantity = $this->resolveReferenceQuantity($ingredient);

        if ($referenceQuantity == 0) {
            return 0;
        }

        return (float) $ingredient->purchase_price / $referenceQuantity;
    }

    public function calculateCostForUsage(Ingredient $ingredient, float $quantityUsed, string $unitUsed): array
    {
        $referenceUnit = $this->resolveReferenceUnit($ingredient);
        $normalizedUnitUsed = $this->unitConversionService->normalize($unitUsed);

        if ($normalizedUnitUsed === null || ! $this->unitConversionService->canConvert($normalizedUnitUsed, $referenceUnit)) {
            $allowedUnits = collect($this->unitConversionService->compatibleUnitsFor($referenceUnit))
                ->map(fn (string $unit) => strtoupper($unit))
                ->implode(', ');

            throw new InvalidArgumentException(
                'O ingrediente '.$ingredient->name.' aceita apenas unidades de '
                .$this->unitConversionService->familyLabel($referenceUnit)
                .' ('.$allowedUnits.'). Ajuste a unidade usada na receita.'
            );
        }

        $referenceQuantityUsed = $this->unitConversionService->convert($quantityUsed, $normalizedUnitUsed, $referenceUnit);
        $unitCost = $this->calculateUnitCost($ingredient);

        return [
            'reference_unit' => $referenceUnit,
            'reference_quantity_used' => $referenceQuantityUsed,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost * $referenceQuantityUsed,
        ];
    }

    public function resolveReferenceUnit(Ingredient $ingredient): string
    {
        $measurementUnit = $this->resolveMeasurementUnit($ingredient);
        $baseUnit = $this->unitConversionService->normalize($ingredient->base_unit);

        if ($measurementUnit === null) {
            throw new InvalidArgumentException('O ingrediente '.$ingredient->name.' esta sem unidade de medida configurada.');
        }

        if ($baseUnit === null || $baseUnit === $measurementUnit) {
            return $measurementUnit;
        }

        if (! $this->unitConversionService->canConvert($measurementUnit, $baseUnit)) {
            throw new InvalidArgumentException('O ingrediente '.$ingredient->name.' esta com unidade usada na receita incompatível com o conteúdo informado.');
        }

        return $baseUnit;
    }

    public function resolveReferenceQuantity(Ingredient $ingredient): float
    {
        $referenceUnit = $this->resolveReferenceUnit($ingredient);
        $measurementUnit = $this->resolveMeasurementUnit($ingredient);
        $measurementQuantity = $this->resolveMeasurementQuantity($ingredient);

        if ($referenceUnit === $measurementUnit) {
            return $measurementQuantity;
        }

        return $this->unitConversionService->convert($measurementQuantity, $measurementUnit, $referenceUnit);
    }

    protected function resolveMeasurementUnit(Ingredient $ingredient): ?string
    {
        $contentUnit = $this->unitConversionService->normalize($ingredient->content_unit);

        if ($contentUnit !== null) {
            return $contentUnit;
        }

        return $this->unitConversionService->normalize($ingredient->purchase_unit);
    }

    protected function resolveMeasurementQuantity(Ingredient $ingredient): float
    {
        $purchaseQuantity = (float) $ingredient->purchase_quantity;
        $contentQuantity = (float) ($ingredient->content_quantity ?? 0);

        if ($contentQuantity > 0) {
            return $purchaseQuantity * $contentQuantity;
        }

        return $purchaseQuantity;
    }
}
