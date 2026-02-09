<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_requisition_id',
        'item_id',
        'quantity',
        'remarks',
    ];

    /**
     * Get the requisition that owns the item.
     */
    public function storeRequisition(): BelongsTo
    {
        return $this->belongsTo(StoreRequisition::class);
    }

    /**
     * Get the item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
