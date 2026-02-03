<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAdjustmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'store_keeper_id' => $this->store_keeper_id,
            'store_keeper' => $this->whenLoaded('storeKeeper') ? [
                'id' => $this->storeKeeper->id,
                'name' => $this->storeKeeper->name,
            ] : null,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'items' => StockAdjustmentItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
