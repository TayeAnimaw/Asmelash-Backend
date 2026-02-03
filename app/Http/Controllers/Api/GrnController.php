<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GrnResource;
use App\Models\Grn;
use App\Models\GrnItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrnController extends Controller
{
    /**
     * Display a listing of the GRNs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Grn::with(['project', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $grns = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => GrnResource::collection($grns),
            'meta' => [
                'current_page' => $grns->currentPage(),
                'last_page' => $grns->lastPage(),
                'per_page' => $grns->perPage(),
                'total' => $grns->total(),
            ],
        ]);
    }

    /**
     * Store a newly created GRN.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'purchase_order_no' => 'required|string|max:255',
            'details' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.stock_code' => 'nullable|string',
            'items.*.unit' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.details' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($validated['items'] as $item) {
                $item['total_price'] = $item['quantity'] * $item['unit_price'];
                $totalPrice += $item['total_price'];
            }

            $grn = Grn::create([
                'date' => $validated['date'],
                'supplier_name' => $validated['supplier_name'],
                'purchase_order_no' => $validated['purchase_order_no'],
                'total_price' => $totalPrice,
                'details' => $validated['details'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'user_id' => $request->user()->id,
            ]);

            foreach ($validated['items'] as $item) {
                $grn->items()->create($item);
            }

            $grn->load(['project', 'items']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'GRN created successfully',
                'data' => new GrnResource($grn),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create GRN: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified GRN.
     */
    public function show(Grn $grn): JsonResponse
    {
        $grn->load(['project', 'items.item']);

        return response()->json([
            'success' => true,
            'data' => new GrnResource($grn),
        ]);
    }

    /**
     * Update the specified GRN.
     */
    public function update(Request $request, Grn $grn): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'sometimes|required|date',
            'supplier_name' => 'sometimes|required|string|max:255',
            'purchase_order_no' => 'sometimes|required|string|max:255',
            'details' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $grn->update($validated);
        $grn->load(['project', 'items']);

        return response()->json([
            'success' => true,
            'message' => 'GRN updated successfully',
            'data' => new GrnResource($grn),
        ]);
    }

    /**
     * Remove the specified GRN.
     */
    public function destroy(Grn $grn): JsonResponse
    {
        $grn->items()->delete();
        $grn->delete();

        return response()->json([
            'success' => true,
            'message' => 'GRN deleted successfully',
        ]);
    }
}
