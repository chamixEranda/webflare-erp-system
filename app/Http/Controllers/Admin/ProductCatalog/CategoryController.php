<?php

namespace App\Http\Controllers\Admin\ProductCatalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Support\Facades\Log;
use App\Services\ProductCategoryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected $productCategoryService;

    public function __construct(ProductCategoryService $productCategoryService)
    {
        $this->productCategoryService = $productCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product-catalog.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function list(Request $request)
    {
        $totalRecords = $this->productCategoryService->getCategoryCount();
        $totalFiltered = $totalRecords;

        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalRecords;
        $start = $request->input('start');
        $columns = ['id', 'name', 'parent_id', 'slug', 'action'];
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $categories = $this->productCategoryService->getAllCategoriesWithPaginate($start, $limit, $order, $dir, $search);
        $data = [];

        foreach ($categories as $key => $category) {
            $nestedData['key'] = $key + 1;
            $nestedData['id'] = $category->id;
            $nestedData['image'] = $category->image ? '<img src="' . Storage::url($category->image) . '" alt="' . $category->name . '" width="50" height="50">' : '<img src="' . asset('assets/images/default.png') . '" alt="No Image" width="50" height="50">';
            $nestedData['name'] = $category->name;
            $nestedData['parent_id'] = $category->parentCategory ? $category->parentCategory->name : 'N/A';
            $nestedData['slug'] = $category->slug ? $category->slug : 'N/A';
            $nestedData['action'] = '<button type="button" class="btn btn-sm btn-primary" onclick="editProductCategory(' . $category->id . ')">Edit</button> 
                                    <button class="btn btn-sm btn-danger" type="button" onclick="form_alert(\'category-' . $category->id . '\',\'Want to delete this category ?\')" title="Delete Category"><i class="tio-delete-outlined"></i>
                                        Delete
                                    </button>
                                    <form action="' . route('admin.categories.destroy', $category->id) . '"
                                                method="post" id="category-' . $category->id . '">
                                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                                            <input type="hidden" name="_method" value="DELETE">
                                    </form>';
            $data[] = $nestedData;
        }
        
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('categories', $filename, 'public');
                $validated['image'] = $path;
            }

            $category = $this->productCategoryService->create($validated);

            return new JsonResponse([
                'message' => 'Category created successfully.',
                'status' => JsonResponse::HTTP_CREATED,
                'data' => $category
            ], JsonResponse::HTTP_CREATED);
            
        } catch (\Throwable $th) {
            Log::error('Error creating category: ' . $th->getMessage().' in ' . $th->getFile() . ' on line ' . $th->getLine());
            return new JsonResponse([
                'message' => 'Failed to create category.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
             $category = $this->productCategoryService->getCategoryById($id);
            if (!$category) {
                return new JsonResponse([
                    'message' => 'Category not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Category fetched successfully.',
                'status' => JsonResponse::HTTP_OK,
                'data' => $category
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error fetching category: ' . $th->getMessage().' in ' . $th->getFile() . ' on line ' . $th->getLine());
            return new JsonResponse([
                'message' => 'Failed to fetch category.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('categories', $filename, 'public');
                $validated['image'] = $path;
            }

            $category = $this->productCategoryService->getCategoryById($id);
            if (!$category) {
                return new JsonResponse([
                    'message' => 'Category not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($request->hasFile('image') && $category->image && Storage::exists($category->image)) {
                Storage::delete($category->image);
            }

            $updated = $this->productCategoryService->updateCategory($id, $validated);

            if (!$updated) {
                return new JsonResponse([
                    'message' => 'Category not found or update failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Category updated successfully.',
                'status' => JsonResponse::HTTP_OK
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error updating category: ' . $th->getMessage().' in ' . $th->getFile() . ' on line ' . $th->getLine());
            return new JsonResponse([
                'message' => 'Failed to update category.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = $this->productCategoryService->deleteCategory($id);

            if (!$deleted) {
                return new JsonResponse([
                    'message' => 'Category not found or delete failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Category deleted successfully.',
                'status' => JsonResponse::HTTP_OK
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error deleting category: ' . $th->getMessage().' in ' . $th->getFile() . ' on line ' . $th->getLine());
            return new JsonResponse([
                'message' => 'Failed to delete category.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
