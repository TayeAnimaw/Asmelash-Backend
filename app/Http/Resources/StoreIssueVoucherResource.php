<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreIssueVoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date->toIso8601String(),
            'request_no' => $this->request_no,
            'project_id' => $this->project_id,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user') ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'receiver_name' => $this->receiver_name,
            'driver_name' => $this->driver_name,
            'plate_no' => $this->plate_no,
            'remarks' => $this->remarks,
            'items' => StoreIssueVoucherItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
