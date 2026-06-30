<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UnitService
{
    public function __construct(protected Unit $unitModel) {}

    public function getAllUnitsWithPaginate($start, $limit, $order, $dir, $search = null)
    {
        $query = $this->unitModel::with('baseUnit')
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        return $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
    }

    public function getUnitCount(): int
    {
        return $this->unitModel->where('is_active', true)->count();
    }

    public function create(array $data): Unit
    {
        if (empty($data['base_unit_id'])) {
            $data['conversion_factor'] = 1.0;
        }

        return $this->unitModel->create($data);
    }

    public function getUnitById(int $id): ?Unit
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            Log::error("Unit with ID {$id} not found.");

            return null;
        }

        return $unit;
    }

    public function updateUnit(int $id, array $data): bool
    {
        $unit = $this->getUnitById($id);
        if ($unit) {
            if (empty($data['base_unit_id'])) {
                $data['conversion_factor'] = 1.0;
            }

            return $unit->update($data);
        }

        return false;
    }

    /**
     * @return Collection<int, Unit>
     */
    public function getBaseUnitsByUomType(string $uomType, ?int $excludeUnitId = null): Collection
    {
        return $this->unitModel
            ->where('is_active', true)
            ->where('uom_type', $uomType)
            ->whereNull('base_unit_id')
            ->when($excludeUnitId, fn ($query) => $query->where('id', '!=', $excludeUnitId))
            ->orderBy('name')
            ->get(['id', 'name', 'short_name']);
    }

    public function deleteUnit(int $id): bool
    {
        $unit = $this->getUnitById($id);
        if ($unit) {
            return $unit->delete();
        }

        return false;
    }
}
