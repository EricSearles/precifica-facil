<?php

namespace App\Services;

use App\Models\Packaging;
use App\Repositories\PackagingRepository;

class PackagingService
{
    public function __construct(
        protected PackagingRepository $packagingRepository,
    ) {
    }

    public function create(array $data, int $companyId): Packaging
    {
        return $this->packagingRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'unit_cost' => $data['unit_cost'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function update(Packaging $packaging, array $data): Packaging
    {
        $packaging->name = $data['name'];
        $packaging->unit_cost = $data['unit_cost'];
        $packaging->notes = $data['notes'] ?? null;

        return $this->packagingRepository->save($packaging);
    }

    public function delete(Packaging $packaging): void
    {
        $this->packagingRepository->delete($packaging);
    }
}
