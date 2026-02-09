<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreRequisitionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name' => $this->item->name,
            'item_code' => $this->item->code,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'remarks' => $this->remarks,
        ];
    }
}
