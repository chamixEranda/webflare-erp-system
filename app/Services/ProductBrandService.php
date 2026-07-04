<?php

namespace App\Services;

use App\Models\ProductBrand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductBrandService
{
    public function __construct(protected ProductBrand $productBrandModel) {}

    public function getAllBrandsWithPaginate(int $start, int $limit, string $order, string $dir, ?string $search = null)
    {
        $query = $this->productBrandModel::where('is_active', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
    }

    public function getBrandCount(): int
    {
        return $this->productBrandModel->where('is_active', true)->count();
    }

    public function create(array $data): ProductBrand
    {
        return $this->productBrandModel->create($data);
    }

    public function getBrandById(int $id): ?ProductBrand
    {
        $brand = $this->productBrandModel->find($id);
        if (! $brand) {
            Log::error("Brand with ID {$id} not found.");

            return null;
        }
        $brand->image = $brand->image ? Storage::url($brand->image) : asset('assets/images/default.png');

        return $brand;
    }

    public function updateBrand(int $id, array $data): bool
    {
        $brand = $this->productBrandModel->find($id);
        if ($brand) {
            return $brand->update($data);
        }

        return false;
    }

    public function deleteBrand(int $id): bool
    {
        $brand = $this->productBrandModel->find($id);
        if ($brand) {
            if ($brand->image && Storage::exists($brand->image)) {
                Storage::delete($brand->image);
            }

            return $brand->delete();
        }

        return false;
    }
}
