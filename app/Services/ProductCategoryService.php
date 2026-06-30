<?php

namespace App\Services;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductCategoryService
{
    protected $productCategoryModel;
    public function __construct()
    {
        $this->productCategoryModel = new ProductCategory();
    }

    public function getAllCategoriesWithPaginate($start, $limit, $order, $dir, $search = null)
    {
        $query = $this->productCategoryModel::with('parentCategory')
            ->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
    }
    
    public function getCategoryCount(): int
    {
        return $this->productCategoryModel->where('is_active', true)->count();
    }
    
    public function create(array $data): ProductCategory
    {
        return $this->productCategoryModel->create($data);
    }

    public function getCategoryById(int $id): ?ProductCategory
    {
        $category =  $this->productCategoryModel->find($id);
        if (!$category) {
            Log::error("Category with ID {$id} not found.");
            return null;
        }
        $category->image = $category->image ? Storage::url($category->image) : asset('assets/images/default.png');
        return $category;
    }

    public function updateCategory(int $id, array $data): bool
    {
        $category = $this->getCategoryById($id);
        if ($category) {
            return $category->update($data);
        }
        return false;
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);
        if ($category) {
            if ($category->image && Storage::exists($category->image)) {
                Storage::delete($category->image);
            }
            return $category->delete();
        }
        return false;
    }
}