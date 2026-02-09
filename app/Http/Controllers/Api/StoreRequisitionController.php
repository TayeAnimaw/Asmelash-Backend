<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreRequisitionResource;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $requisitions = StoreRequisition::with(['project', 'items.item'])->get();
        return response()->json([
            'success' => true,
            'data' => StoreRequisitionResource::collection($requisitions),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $requisition = StoreRequisition::create([
                'requisition_no' => StoreRequisition::generateRequisitionNo(),
                'project_id' => $request->project_id,
                'description' => $request->description,
                'status' => 'pending',
                'is_approved' => false,
            ]);

            foreach ($request->items as $item) {
                StoreRequisitionItem::create([
                    'store_requisition_id' => $requisition->id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();

            $requisition->load(['project', 'items.item']);

            return response()->json([
                'success' => true,
                'message' => 'Store requisition created successfully',
                'data' => new StoreRequisitionResource($requisition),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create store requisition',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StoreRequisition $storeRequisition): JsonResponse
    {
        $storeRequisition->load(['project', 'items.item']);

        return response()->json([
            'success' => true,
            'data' => new StoreRequisitionResource($storeRequisition),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoreRequisition $storeRequisition): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|exists:projects,id',
            'description' => 'nullable|string',
            'items' => 'sometimes|array',
            'items.*.item_id' => 'required_with:items|exists:items,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $storeRequisition->update($request->only(['project_id', 'description']));

            if ($request->has('items')) {
                $storeRequisition->items()->delete();
                foreach ($request->items as $item) {
                    StoreRequisitionItem::create([
                        'store_requisition_id' => $storeRequisition->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'remarks' => $item['remarks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $storeRequisition->load(['project', 'items.item']);

            return response()->json([
                'success' => true,
                'message' => 'Store requisition updated successfully',
                'data' => new StoreRequisitionResource($storeRequisition),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update store requisition',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve the specified resource.
     */
    public function approve(StoreRequisition $storeRequisition): JsonResponse
    {
        if ($storeRequisition->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Store requisition is already approved',
            ], 400);
        }

        $storeRequisition->update([
            'is_approved' => true,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Store requisition approved successfully',
            'data' => new StoreRequisitionResource($storeRequisition),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreRequisition $storeRequisition): JsonResponse
    {
        try {
            $storeRequisition->items()->delete();
            $storeRequisition->delete();

            return response()->json([
                'success' => true,
                'message' => 'Store requisition deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete store requisition',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
