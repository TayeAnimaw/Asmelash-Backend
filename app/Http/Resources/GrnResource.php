<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toIso8601String(),
            'supplier_name' => $this->supplier_name,
            'purchase_order_no' => $this->purchase_order_no,
            'total_price' => $this->total_price,
            'details' => $this->details,
            'project_id' => $this->project_id,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'user_id' => $this->user_id,
            'items' => GrnItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
