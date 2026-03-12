<?php

namespace App\Services;

use App\Models\Ingredient;
use InvalidArgumentException;
use Illuminate\Support\Collection;

class UnitConversionService
{
    protected array $unitMap = [
        'un' => ['family' => 'count', 'factor' => 1.0],
        'ml' => ['family' => 'volume', 'factor' => 1.0],
        'l' => ['family' => 'volume', 'factor' => 1000.0],
        'g' => ['family' => 'weight', 'factor' => 1.0],
        'kg' => ['family' => 'weight', 'factor' => 1000.0],
    ];

    public function normalize(?string $unit): ?string
    {
        if ($unit === null) {
            return null;
        }

        $normalized = strtolower(trim($unit));

        return $normalized !== '' ? $normalized : null;
    }

    public function canConvert(?string $fromUnit, ?string $toUnit): bool
    {
        $from = $this->normalize($fromUnit);
        $to = $this->normalize($toUnit);

        if ($from === null || $to === null) {
            return false;
        }

        if (! isset($this->unitMap[$from], $this->unitMap[$to])) {
            return false;
        }

        return $this->unitMap[$from]['family'] === $this->unitMap[$to]['family'];
    }

    public function convert(float $quantity, string $fromUnit, string $toUnit): float
    {
        $from = $this->normalize($fromUnit);
        $to = $this->normalize($toUnit);

        if ($from === $to) {
            return $quantity;
        }

        if (! $this->canConvert($from, $to)) {
            throw new InvalidArgumentException('As unidades informadas nao sao compativeis para conversao.');
        }

        $quantityInBase = $quantity * $this->unitMap[$from]['factor'];

        return $quantityInBase / $this->unitMap[$to]['factor'];
    }

    public function compatibleUnitsFor(string $unit): array
    {
        $normalized = $this->normalize($unit);

        if ($normalized === null || ! isset($this->unitMap[$normalized])) {
            return [];
        }

        $family = $this->unitMap[$normalized]['family'];

        return collect($this->unitMap)
            ->filter(fn (array $definition) => $definition['family'] === $family)
            ->keys()
            ->values()
            ->all();
    }

    public function compatibleUnitsForIngredient(Ingredient $ingredient): array
    {
        $unit = $this->normalize($ingredient->base_unit ?: $ingredient->content_unit ?: $ingredient->purchase_unit);

        return $unit ? $this->compatibleUnitsFor($unit) : [];
    }

    public function compatibleUnitsForIngredientMap(iterable $ingredients): array
    {
        return collect($ingredients)
            ->mapWithKeys(fn (Ingredient $ingredient) => [
                $ingredient->id => $this->compatibleUnitsForIngredient($ingredient),
            ])
            ->all();
    }

    public function familyLabel(string $unit): string
    {
        $normalized = $this->normalize($unit);

        return match ($this->unitMap[$normalized]['family'] ?? null) {
            'volume' => 'volume',
            'weight' => 'peso',
            'count' => 'unidade',
            default => 'medida',
        };
    }
}
