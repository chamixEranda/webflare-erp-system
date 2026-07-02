<?php

namespace App\Http\Controllers\Admin\ProductCatalog;

use App\Enums\UomType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Services\UnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    public function __construct(protected UnitService $unitService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.product-catalog.units.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Get paginate list of units.
     */
    public function list(Request $request): JsonResponse
    {
        $totalRecords = $this->unitService->getUnitCount();
        $totalFiltered = $totalRecords;

        if ($request->input('length') != -1) {
            $limit = $request->input('length');
        } else {
            $limit = $totalRecords;
        }

        $start = $request->input('start');
        $columns = ['id', 'name', 'short_name', 'uom_type', 'base_unit_id', 'conversion_factor', 'action'];
        $orderIndex = $request->input('order.0.column', 1);
        $order = $columns[$orderIndex] ?? 'name';
        $dir = $request->input('order.0.dir', 'asc');
        $search = $request->input('search.value');

        $units = $this->unitService->getAllUnitsWithPaginate($start, $limit, $order, $dir, $search);
        $data = [];

        foreach ($units as $key => $unit) {
            $nestedData['key'] = str_pad($start + $key + 1, 2, '0', STR_PAD_LEFT);
            $nestedData['id'] = $unit->id;
            $nestedData['name'] = $unit->name;
            $nestedData['short_name'] = $unit->short_name ? $unit->short_name : 'N/A';
            $nestedData['uom_type'] = UomType::getUomTypeName($unit->uom_type);
            $nestedData['base_unit_id'] = $unit->baseUnit ? $unit->baseUnit->name : 'N/A';
            $nestedData['conversion_factor'] = $unit->conversion_factor;
            $nestedData['action'] = '<div class="d-flex align-items-center gap-2 justify-content-center">
                                    <button type="button" class="btn btn-icon btn-sm rounded-2" style="background:rgba(var(--bs-primary-rgb),0.1); color:var(--bs-primary); border:none;" onclick="editUnit('.$unit->id.')" title="Edit Unit">
                                        <i class="ti ti-edit" style="font-size:1rem;"></i>
                                    </button>
                                    <button class="btn btn-icon btn-sm rounded-2" style="background:rgba(var(--bs-danger-rgb),0.1); color:var(--bs-danger); border:none;" type="button" onclick="form_alert(\'unit-'.$unit->id.'\',\'Want to delete this unit ?\')" title="Delete Unit">
                                        <i class="ti ti-trash" style="font-size:1rem;"></i>
                                    </button>
                                    <form action="'.route('admin.units.destroy', $unit->id).'"
                                                 method="post" id="unit-'.$unit->id.'">
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
    public function store(StoreUnitRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $unit = $this->unitService->create($validated);

            return new JsonResponse([
                'message' => 'Unit created successfully.',
                'status' => JsonResponse::HTTP_CREATED,
                'data' => $unit,
            ], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Error creating unit: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to create unit.',
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
            $unit = $this->unitService->getUnitById((int) $id);
            if (! $unit) {
                return new JsonResponse([
                    'message' => 'Unit not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Unit fetched successfully.',
                'status' => JsonResponse::HTTP_OK,
                'data' => $unit,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error fetching unit: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to fetch unit.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $unit = $this->unitService->getUnitById((int) $id);
            if (! $unit) {
                return new JsonResponse([
                    'message' => 'Unit not found.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            $updated = $this->unitService->updateUnit((int) $id, $validated);
            if (! $updated) {
                return new JsonResponse([
                    'message' => 'Unit update failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Unit updated successfully.',
                'status' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error updating unit: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to update unit.',
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
            $deleted = $this->unitService->deleteUnit((int) $id);
            if (! $deleted) {
                return new JsonResponse([
                    'message' => 'Unit not found or delete failed.',
                    'status' => JsonResponse::HTTP_NOT_FOUND,
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'message' => 'Unit deleted successfully.',
                'status' => JsonResponse::HTTP_OK,
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Error deleting unit: '.$th->getMessage().' in '.$th->getFile().' on line '.$th->getLine());

            return new JsonResponse([
                'message' => 'Failed to delete unit.',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $th->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get base units for a given UOM type (AJAX endpoint).
     */
    public function baseUnits(Request $request): JsonResponse
    {
        $uomType = $request->query('uom_type', '');
        $excludeId = $request->query('exclude_id') ? (int) $request->query('exclude_id') : null;

        if (! $uomType) {
            return new JsonResponse(['data' => []], JsonResponse::HTTP_OK);
        }

        $units = $this->unitService->getBaseUnitsByUomType($uomType, $excludeId);

        return new JsonResponse(['data' => $units], JsonResponse::HTTP_OK);
    }
}
