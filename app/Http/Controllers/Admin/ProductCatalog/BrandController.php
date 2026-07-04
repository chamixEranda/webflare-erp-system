<?php

namespace App\Http\Controllers\Admin\ProductCatalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Services\ProductBrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function __construct(protected ProductBrandService $productBrandService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product-catalog.brands.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Get paginated list of brands for DataTable.
     */
    public function list(Request $request): JsonResponse
    {
        $totalRecords = $this->productBrandService->getBrandCount();
        $totalFiltered = $totalRecords;

        if ($request->input('length') != -1) {
            $limit = $request->input('length');
        } else {
            $limit = $totalRecords;
        }

        $start = $request->input('start');
        $columns = ['id', 'image', 'name', 'action'];

        $orderIndex = $request->input('order.0.column', 2);
        $order = $columns[$orderIndex] ?? 'name';
        $dir = $request->input('order.0.dir', 'asc');
        $search = $request->input('search.value');

        $brands = $this->productBrandService->getAllBrandsWithPaginate($start, $limit, $order, $dir, $search);
        $data = [];

        foreach ($brands as $key => $brand) {
            $nestedData['key'] = str_pad($start + $key + 1, 2, '0', STR_PAD_LEFT);
            $nestedData['id'] = $brand->id;

            $imageUrl = $brand->image ? Storage::url($brand->image) : asset('assets/images/default.png');
            $nestedData['image'] = '<img src="'.$imageUrl.'" alt="'.e($brand->name).'" width="50" height="50" style="object-fit: cover; border-radius: 4px;">';
            $nestedData['name'] = $brand->name;
            $nestedData['action'] = '<div class="d-flex align-items-center gap-2 justify-content-center">
                                    <button type="button" class="btn btn-icon btn-sm rounded-2" style="background:rgba(var(--bs-primary-rgb),0.1); color:var(--bs-primary); border:none;" onclick="editProductBrand('.$brand->id.')" title="Edit Brand">
                                        <i class="ti ti-edit" style="font-size:1rem;"></i>
                                    </button>
                                    <button class="btn btn-icon btn-sm rounded-2" style="background:rgba(var(--bs-danger-rgb),0.1); color:var(--bs-danger); border:none;" type="button" onclick="form_alert(\'brand-'.$brand->id.'\',\'Want to delete this brand ?\')" title="Delete Brand">
                                        <i class="ti ti-trash" style="font-size:1rem;"></i>
                                    </button>
                                    <form action="'.route('admin.brands.destroy', $brand->id).'"
                                                method="post" id="brand-'.$brand->id.'">
                                            <input type="hidden" name="_token" value="'.csrf_token().'">
                                            <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                    </div>';
            $data[] = $nestedData;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('brands', $filename, 'public');
                $validated['image'] = $path;
            }

            $validated['company_id'] = 1; // Default company ID, consistent with category factory
            $brand = $this->productBrandService->create($validated);

            return new JsonResponse([
                'message' => 'Brand created successfully.',
                'status' => JsonResponse::HTTP_CREATED,
                'data' => $brand,
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Error creating brand: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to create brand.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
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
    public function edit(string $id): JsonResponse
    {
        try {
            $brand = $this->productBrandService->getBrandById((int) $id);
            if (! $brand) {
                return new JsonResponse([
                    'message' => 'Brand not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Brand fetched successfully.',
                'status' => JsonResponse::HTTP_OK,
                'data' => $brand,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error fetching brand: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to fetch brand.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $brand = $this->productBrandService->getBrandById((int) $id);
            if (! $brand) {
                return new JsonResponse([
                    'message' => 'Brand not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $rawImage = $brand->getRawOriginal('image');
                if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                    Storage::disk('public')->delete($rawImage);
                }

                $image = $request->file('image');
                $filename = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('brands', $filename, 'public');
                $validated['image'] = $path;
            }

            $updated = $this->productBrandService->updateBrand((int) $id, $validated);

            if (! $updated) {
                return new JsonResponse([
                    'message' => 'Brand not found or update failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Brand updated successfully.',
                'status' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error updating brand: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to update brand.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            // Delete the image and soft delete
            $brand = $this->productBrandService->getBrandById((int) $id);
            if ($brand) {
                $rawImage = $brand->getRawOriginal('image');
                if ($rawImage && Storage::disk('public')->exists($rawImage)) {
                    Storage::disk('public')->delete($rawImage);
                }
            }

            $deleted = $this->productBrandService->deleteBrand((int) $id);

            if (! $deleted) {
                return new JsonResponse([
                    'message' => 'Brand not found or delete failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Brand deleted successfully.',
                'status' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error deleting brand: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to delete brand.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
