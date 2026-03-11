<?php

namespace App\Services;

use App\Models\SalesChannel;
use App\Repositories\SalesChannelRepository;
use Illuminate\Support\Str;

class SalesChannelService
{
    public function __construct(
        protected SalesChannelRepository $salesChannelRepository,
    ) {
    }

    public function create(array $data, int $companyId): SalesChannel
    {
        return $this->salesChannelRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'slug' => $this->generateUniqueSlug($data['name'], $companyId),
            'notes' => $data['notes'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(SalesChannel $salesChannel, array $data, int $companyId): SalesChannel
    {
        $salesChannel->name = $data['name'];
        $salesChannel->slug = $this->generateUniqueSlug($data['name'], $companyId, (int) $salesChannel->id);
        $salesChannel->notes = $data['notes'] ?? null;
        $salesChannel->is_active = $data['is_active'] ?? false;

        return $this->salesChannelRepository->save($salesChannel);
    }

    public function delete(SalesChannel $salesChannel): void
    {
        $this->salesChannelRepository->delete($salesChannel);
    }

    protected function generateUniqueSlug(string $name, int $companyId, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'canal';
        $slug = $baseSlug;
        $suffix = 2;

        $existingSlugs = $this->salesChannelRepository->getByCompany($companyId)
            ->reject(fn (SalesChannel $salesChannel) => $ignoreId !== null && (int) $salesChannel->id === $ignoreId)
            ->pluck('slug')
            ->all();

        while (in_array($slug, $existingSlugs, true)) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}