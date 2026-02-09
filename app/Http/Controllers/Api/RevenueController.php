<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RevenueResource;
use App\Models\Revenue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $revenues = Revenue::with('project')->get();
        return response()->json([
            'success' => true,
            'data' => RevenueResource::collection($revenues),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'payment_phase' => 'required|string|max:255',
            'net_payment' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $revenue = Revenue::create($validator->validated());
        $revenue->load('project');

        return response()->json([
            'success' => true,
            'message' => 'Revenue created successfully',
            'data' => new RevenueResource($revenue),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Revenue $revenue): JsonResponse
    {
        $revenue->load('project');
        return response()->json([
            'success' => true,
            'data' => new RevenueResource($revenue),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Revenue $revenue): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|exists:projects,id',
            'payment_phase' => 'sometimes|string|max:255',
            'net_payment' => 'sometimes|numeric|min:0',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $revenue->update($validator->validated());
        $revenue->load('project');

        return response()->json([
            'success' => true,
            'message' => 'Revenue updated successfully',
            'data' => new RevenueResource($revenue),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revenue $revenue): JsonResponse
    {
        $revenue->delete();
        return response()->json([
            'success' => true,
            'message' => 'Revenue deleted successfully',
        ]);
    }
}
