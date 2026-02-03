<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreIssueVoucherResource;
use App\Models\StoreIssueVoucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreIssueVoucherController extends Controller
{
    /**
     * Display a listing of the store issue vouchers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StoreIssueVoucher::with(['project', 'user', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_no', 'like', "%{$search}%")
                    ->orWhere('receiver_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $vouchers = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => StoreIssueVoucherResource::collection($vouchers),
            'meta' => [
                'current_page' => $vouchers->currentPage(),
                'last_page' => $vouchers->lastPage(),
                'per_page' => $vouchers->perPage(),
                'total' => $vouchers->total(),
            ],
        ]);
    }

    /**
     * Store a newly created store issue voucher.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'request_no' => 'nullable|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'receiver_name' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'plate_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_code' => 'nullable|string',
            'items.*.description' => 'nullable|string',
            'items.*.unit' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            foreach ($validated['items'] as $item) {
                $item['total_price'] = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                $totalPrice += $item['total_price'];
            }

            $voucher = StoreIssueVoucher::create([
                'date' => $validated['date'] ?? now()->toDateString(),
                'request_no' => $validated['request_no'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'user_id' => $request->user()->id,
                'receiver_name' => $validated['receiver_name'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'plate_no' => $validated['plate_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $voucher->items()->create($item);
            }

            $voucher->load(['project', 'user', 'items']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Store issue voucher created successfully',
                'data' => new StoreIssueVoucherResource($voucher),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create store issue voucher: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified store issue voucher.
     */
    public function show(StoreIssueVoucher $storeIssueVoucher): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new StoreIssueVoucherResource($storeIssueVoucher->load(['project', 'user', 'items.item'])),
        ]);
    }

    /**
     * Update the specified store issue voucher.
     */
    public function update(Request $request, StoreIssueVoucher $storeIssueVoucher): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'request_no' => 'nullable|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'receiver_name' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'plate_no' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $storeIssueVoucher->update($validated);
        $storeIssueVoucher->load(['project', 'user', 'items']);

        return response()->json([
            'success' => true,
            'message' => 'Store issue voucher updated successfully',
            'data' => new StoreIssueVoucherResource($storeIssueVoucher),
        ]);
    }

    /**
     * Remove the specified store issue voucher.
     */
    public function destroy(StoreIssueVoucher $storeIssueVoucher): JsonResponse
    {
        $storeIssueVoucher->items()->delete();
        $storeIssueVoucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store issue voucher deleted successfully',
        ]);
    }
}
