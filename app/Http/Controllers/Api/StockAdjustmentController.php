<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockAdjustmentResource;
use App\Models\StockAdjustment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the stock adjustments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockAdjustment::with(['project', 'storeKeeper', 'items']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $stockAdjustments = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => StockAdjustmentResource::collection($stockAdjustments),
            'meta' => [
                'current_page' => $stockAdjustments->currentPage(),
                'last_page' => $stockAdjustments->lastPage(),
                'per_page' => $stockAdjustments->perPage(),
                'total' => $stockAdjustments->total(),
            ],
        ]);
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.type' => 'required|in:increase,decrease',
            'items.*.reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $stockAdjustment = StockAdjustment::create([
                'project_id' => $validated['project_id'] ?? null,
                'store_keeper_id' => $request->user()->id,
                'status' => 'not_approved',
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $stockAdjustment->items()->create($item);
            }

            $stockAdjustment->load(['project', 'storeKeeper', 'items']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment created successfully',
                'data' => new StockAdjustmentResource($stockAdjustment),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create stock adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show(StockAdjustment $stockAdjustment): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new StockAdjustmentResource($stockAdjustment->load(['project', 'storeKeeper', 'items.item'])),
        ]);
    }

    /**
     * Approve the specified stock adjustment.
     */
    public function approve(StockAdjustment $stockAdjustment): JsonResponse
    {
        if ($stockAdjustment->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Stock adjustment is already approved',
            ], 400);
        }

        $stockAdjustment->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment approved successfully',
            'data' => new StockAdjustmentResource($stockAdjustment->load(['project', 'storeKeeper', 'items'])),
        ]);
    }

    /**
     * Update the specified stock adjustment.
     */
    public function update(Request $request, StockAdjustment $stockAdjustment): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'remarks' => 'nullable|string',
            'status' => 'sometimes|in:not_approved,approved',
        ]);

        $stockAdjustment->update($validated);
        $stockAdjustment->load(['project', 'storeKeeper', 'items']);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment updated successfully',
            'data' => new StockAdjustmentResource($stockAdjustment),
        ]);
    }

    /**
     * Remove the specified stock adjustment.
     */
    public function destroy(StockAdjustment $stockAdjustment): JsonResponse
    {
        $stockAdjustment->items()->delete();
        $stockAdjustment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock adjustment deleted successfully',
        ]);
    }
}
