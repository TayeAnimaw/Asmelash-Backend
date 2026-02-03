<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_adjustment_id',
        'item_id',
        'quantity',
        'type',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'type' => 'string',
    ];

    // Relationships
    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
