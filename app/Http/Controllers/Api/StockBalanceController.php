<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockBalanceResource;
use App\Models\StockBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockBalanceController extends Controller
{
    /**
     * Display a listing of the stock balances.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockBalance::with(['project', 'item']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('project', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $stockBalances = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => StockBalanceResource::collection($stockBalances),
            'meta' => [
                'current_page' => $stockBalances->currentPage(),
                'last_page' => $stockBalances->lastPage(),
                'per_page' => $stockBalances->perPage(),
                'total' => $stockBalances->total(),
            ],
        ]);
    }

    /**
     * Store a newly created stock balance.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'item_id' => 'nullable|exists:items,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $stockBalance = StockBalance::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock balance created successfully',
            'data' => new StockBalanceResource($stockBalance),
        ], 201);
    }

    /**
     * Display the specified stock balance.
     */
    public function show(StockBalance $stockBalance): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new StockBalanceResource($stockBalance->load(['project', 'item'])),
        ]);
    }

    /**
     * Update the specified stock balance.
     */
    public function update(Request $request, StockBalance $stockBalance): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'item_id' => 'nullable|exists:items,id',
            'quantity' => 'sometimes|required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $stockBalance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock balance updated successfully',
            'data' => new StockBalanceResource($stockBalance),
        ]);
    }

    /**
     * Remove the specified stock balance.
     */
    public function destroy(StockBalance $stockBalance): JsonResponse
    {
        $stockBalance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock balance deleted successfully',
        ]);
    }
}
