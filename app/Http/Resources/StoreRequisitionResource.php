<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreRequisitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'requisition_no' => $this->requisition_no,
            'project_name' => $this->project->name,
            'project_id' => $this->project_id,
            'status' => $this->status,
            'description' => $this->description,
            'is_approved' => $this->is_approved,
            'items' => StoreRequisitionItemResource::collection($this->items),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
